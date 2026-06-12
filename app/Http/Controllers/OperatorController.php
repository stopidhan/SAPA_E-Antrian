<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\ServiceCounter;
use App\Models\Service;
use App\Models\CounterSession;

class OperatorController extends Controller
{
    public function index(Request $request, $instance_slug)
    {
        $userInstance = app(\App\Services\TenantManager::class)->getInstance();

        // Pastikan user mengakses instance-nya sendiri
        if (!$userInstance || $userInstance->instance_slug !== $instance_slug) {
            abort(403, 'Unauthorized access to this instance.');
        }

        // Cari sesi loket yang aktif untuk user ini
        $session = auth()->user()->activeCounterSession;
        $counter = $session ? $session->counter : null;
        
        $namaLoket = $counter ? $counter->counter_number : null;
        $idLoket   = $counter ? $counter->id : null;
        $serviceId = $counter ? $counter->service_id : null;

        // Ambil daftar counter yang sedang digunakan
        $usedCounters = CounterSession::where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())
            ->where('status', 'open')
            ->pluck('service_counter_id')
            ->toArray();

        // Ambil daftar counter yang tersedia (yang belum digunakan)
        $availableCounters = ServiceCounter::where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())
            ->where('is_active', true)
            ->whereNotIn('id', $usedCounters)
            ->with('service')
            ->get();

        // Ambil daftar layanan yang aktif sesuai instance user
        $services = Service::where('is_active', true)
            ->where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())
            ->get();

        // Get today's waiting queues for the user's instance and specific service if assigned
        $queuesQuery = Queue::with('service')
            ->where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())
            ->whereDate('queue_date', today())
            ->where('queue_status', 'waiting');

        if ($serviceId) {
            $queuesQuery->where('service_id', $serviceId);
        }

        $queuesData = $queuesQuery->orderBy('id', 'asc')
            ->get()
            ->map(function ($q) {
                return [
                    'id'      => $q->id,
                    'nomor'   => $q->queue_number,
                    'layanan' => $q->service ? $q->service->service_name : 'Lainnya',
                    'tipe'    => $q->queue_source ?? 'onsite'
                ];
            });

        // Get history for today (completed, skipped, cancelled) handled by this user's counter
        $historyData = collect();
        if ($idLoket) {
            $historyData = Queue::with('service')
                ->where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())
                ->whereDate('queue_date', today())
                ->whereIn('queue_status', ['completed', 'skipped', 'cancelled'])
                ->where('service_counter_id', $idLoket)
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($q) {
                    return [
                        'id'      => $q->id,
                        'nomor'   => $q->queue_number,
                        'status'  => $q->queue_status,
                        'waktu'   => $q->updated_at->format('H:i')
                    ];
                });
        }

        // Cek apakah ada antrean yang sedang dipanggil atau dilayani agar tidak hilang saat refresh/reload
        $activeQueue = null;
        $timerSeconds = 0;

        if ($idLoket) {
            $activeQueueRaw = Queue::with('service')
                ->where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())
                ->whereDate('queue_date', today())
                ->whereIn('queue_status', ['called', 'serving'])
                ->where('service_counter_id', $idLoket)
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($activeQueueRaw) {
                $activeQueue = [
                    'id'      => $activeQueueRaw->id,
                    'nomor'   => $activeQueueRaw->queue_number,
                    'layanan' => $activeQueueRaw->service ? $activeQueueRaw->service->service_name : 'Lainnya',
                    'tipe'    => $activeQueueRaw->queue_source ?? 'onsite',
                    'status'  => $activeQueueRaw->queue_status
                ];

                if ($activeQueueRaw->queue_status === 'serving' && $activeQueueRaw->service_start_time) {
                    $timerSeconds = \Carbon\Carbon::parse($activeQueueRaw->service_start_time)->diffInSeconds(now());
                }
            }
        }

        $tts_enabled = $userInstance->tts_enabled ?? false;
        $tts_language = $userInstance->tts_language ?? 'id-ID';

        return view('Pages.StaffOperatorLoket.Index', compact('queuesData', 'historyData', 'namaLoket', 'idLoket', 'services', 'activeQueue', 'timerSeconds', 'availableCounters', 'tts_enabled', 'tts_language'));
    }

    public function openSession(Request $request, $instance_slug)
    {
        $request->validate(['counter_id' => 'required|exists:service_counters,id']);
        
        $counterId = $request->counter_id;
        $userId = auth()->id();
        $instanceId = app(\App\Services\TenantManager::class)->getInstanceId();

        // 1. Cek apakah user sudah memiliki sesi aktif
        $userActiveSession = CounterSession::where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if ($userActiveSession) {
            // Jika user klik loket yang sama, izinkan (idempotent)
            if ($userActiveSession->service_counter_id == $counterId) {
                return response()->json(['success' => true, 'message' => 'Sesi sudah aktif.']);
            }
            return response()->json([
                'success' => false, 
                'message' => 'Anda sudah memiliki sesi loket yang aktif. Silakan tutup sesi sebelumnya terlebih dahulu.'
            ], 400);
        }

        // 2. Cek apakah loket sudah digunakan oleh operator lain
        $counterActiveSession = CounterSession::where('service_counter_id', $counterId)
            ->where('status', 'open')
            ->first();

        if ($counterActiveSession) {
            return response()->json([
                'success' => false, 
                'message' => 'Loket ini sedang digunakan oleh operator lain.'
            ], 400);
        }

        // 3. Buat sesi baru
        CounterSession::create([
            'instance_id' => $instanceId,
            'service_counter_id' => $counterId,
            'user_id' => $userId,
            'status' => 'open',
            'started_at' => now(),
        ]);

        // Trigger websocket event to update Supervisor Dashboard (Operator joined)
        event(new \App\Events\QueueUpdated('session_opened', null, $instanceId));

        return response()->json(['success' => true, 'message' => 'Sesi berhasil dibuka.']);
    }

    public function closeSession(Request $request, $instance_slug)
    {
        CounterSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->update([
                'status' => 'closed',
                'ended_at' => now(),
            ]);

        // Trigger websocket event to update Supervisor Dashboard (Operator left)
        event(new \App\Events\QueueUpdated('session_closed', null, app(\App\Services\TenantManager::class)->getInstanceId()));

        return response()->json(['success' => true, 'message' => 'Sesi berhasil ditutup.']);
    }

    public function currentStatus(Request $request, $instance_slug)
    {
        $session = auth()->user()->activeCounterSession;
        
        return response()->json([
            'success' => true,
            'has_session' => $session ? true : false,
            'counter' => $session ? $session->counter : null
        ]);
    }

    public function getQueuesApi(Request $request, $instance_slug)
    {
        $session = auth()->user()->activeCounterSession;
        $counter = $session ? $session->counter : null;
        $serviceId = $counter ? $counter->service_id : null;

        // Get today's waiting queues in JSON format for polling (filtered by instance and service if any)
        $queuesQuery = Queue::with('service')
            ->where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())
            ->whereDate('queue_date', today())
            ->where('queue_status', 'waiting');

        if ($serviceId) {
            $queuesQuery->where('service_id', $serviceId);
        }

        $queuesData = $queuesQuery->orderBy('id', 'asc')
            ->get()
            ->map(function ($q) {
                return [
                    'id'      => $q->id,
                    'nomor'   => $q->queue_number,
                    'layanan' => $q->service ? $q->service->service_name : 'Lainnya',
                    'tipe'    => $q->queue_source ?? 'onsite'
                ];
            });

        $historyData = collect();
        if ($counter) {
            $historyData = Queue::with('service')
                ->where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())
                ->whereDate('queue_date', today())
                ->whereIn('queue_status', ['completed', 'skipped', 'cancelled'])
                ->where('service_counter_id', $counter->id)
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($q) {
                    return [
                        'id'      => $q->id,
                        'nomor'   => $q->queue_number,
                        'status'  => $q->queue_status,
                        'waktu'   => $q->updated_at->format('H:i')
                    ];
                });
        }

        return response()->json([
            'waiting' => $queuesData,
            'history' => $historyData
        ]);
    }

    public function panggilAntrean(Request $request, $instance_slug, $id)
    {
        $queue = Queue::where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())->findOrFail($id);
        $counterId = $request->input('counter_id');
        $userId = auth()->id();

        // Mencegah Race Condition dengan Atomic Update
        if ($queue->queue_status !== 'waiting') {
            // Izinkan jika ini adalah pemanggilan ulang (panggilUlang) dari loket yang sama
            if ($queue->service_counter_id != $counterId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Oops! Antrean ini baru saja diambil oleh loket lain.'
                ], 409);
            }
            // Panggil ulang (hanya update waktu panggil agar di monitor public naik kembali, jika perlu)
            $queue->update(['call_time' => now()]);
        } else {
            // ATOMIC UPDATE: Memastikan hanya 1 loket yang menang
            $berhasilUpdate = Queue::where('id', $id)
                ->where('queue_status', 'waiting')
                ->update([
                    'queue_status'       => 'called',
                    'call_time'          => now(),
                    'service_counter_id' => $counterId,
                    'user_id'            => $userId,
                ]);

            if ($berhasilUpdate === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Oops! Antrean ini baru saja direbut oleh loket lain.'
                ], 409);
            }

            // Refresh model dengan data yang baru diupdate
            $queue->refresh();
        }

        $queue->load('counter', 'service');

        event(new \App\Events\QueueUpdated('called', [
            'id' => $queue->id,
            'queue_number' => $queue->queue_number,
            'counter_number' => $queue->counter ? $queue->counter->counter_number : '-',
            'service_name' => $queue->service ? $queue->service->service_name : 'Layanan'
        ], app(\App\Services\TenantManager::class)->getInstanceId()));

        return response()->json([
            'success' => true,
            'message' => 'Status antrean berhasil diperbarui (Dipanggil)'
        ]);
    }

    public function layaniAntrean(Request $request, $instance_slug, $id)
    {
        $queue = Queue::where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())->findOrFail($id);
        $counterId = $request->input('counter_id');

        $queue->update([
            'queue_status'       => 'serving', // dilayani
            'service_start_time' => now(),
            'service_counter_id' => $counterId,
            'user_id'            => auth()->id(),
        ]);

        event(new \App\Events\QueueUpdated('serving', null, app(\App\Services\TenantManager::class)->getInstanceId()));

        return response()->json(['success' => true, 'message' => 'Status: Dilayani']);
    }

    public function lewatiAntrean(Request $request, $instance_slug, $id)
    {
        $queue = Queue::where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())->findOrFail($id);

        $queue->update([
            'queue_status' => 'skipped', // dilewati
            'user_id'      => auth()->id(),
        ]);

        event(new \App\Events\QueueUpdated('skipped', null, app(\App\Services\TenantManager::class)->getInstanceId()));

        return response()->json(['success' => true, 'message' => 'Status: Dilewati']);
    }

    /*
    // Fitur batalkan antrean (Disembunyikan sementara)
    public function batalkanAntrean(Request $request, $instance_slug, $id)
    {
        $queue = Queue::where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())->findOrFail($id);
        $counterId = $request->input('counter_id');

        $queue->update([
            'queue_status'       => 'cancelled', // dibatalkan karena salah antrean/alasan lain
            'service_counter_id' => $counterId,
            'user_id'            => auth()->id(),
            'service_end_time'   => now(),
            'service_description' => 'Dibatalkan oleh operator',
        ]);

        event(new \App\Events\QueueUpdated('cancelled', null, app(\App\Services\TenantManager::class)->getInstanceId()));

        return response()->json(['success' => true, 'message' => 'Antrean dibatalkan']);
    }
    */

    public function selesaiAntrean(Request $request, $instance_slug, $id)
    {
        $queue = Queue::where('instance_id', app(\App\Services\TenantManager::class)->getInstanceId())->findOrFail($id);

        $startTime = $queue->service_start_time ? \Carbon\Carbon::parse($queue->service_start_time) : now();
        $endTime = now();

        $request->validate([
            'category' => 'required|string|min:1',
            'description' => 'required|string|min:1',
        ]);

        $kategoriLayanan = $request->input('category');
        $catatan = $request->input('description');

        // Menggabungkan kategori dan catatan jika keduanya ada
        $deskripsiFinal = null;
        if ($kategoriLayanan || $catatan) {
            $deskripsiFinal = "Kategori: " . ($kategoriLayanan ?? '-') . "\nCatatan: " . ($catatan ?? '-');
        }

        $queue->update([
            'queue_status'        => 'completed', // selesai
            'service_end_time'    => $endTime,
            'service_duration'    => $startTime->diffInMinutes($endTime), // Durasi dalam menit
            'service_description' => $deskripsiFinal,
            'user_id'             => auth()->id(),
        ]);

        if ($request->has('photo') && !empty($request->input('photo'))) {
            $photoData = $request->input('photo');
            if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                $data = substr($photoData, strpos($photoData, ',') + 1);
                $data = base64_decode($data);

                // [SECURITY PATCH] Lapis 1: Whitelist ekstensi yang diizinkan
                // Mencegah upload file berbahaya seperti .php, .exe, .sh, dll.
                $extensionRaw = strtolower($type[1]);
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

                // Normalisasi 'jpeg' → 'jpg'
                $extension = ($extensionRaw === 'jpeg') ? 'jpg' : $extensionRaw;

                if (!in_array($extension, $allowedExtensions)) {
                    // Diam-diam abaikan file berbahaya tanpa error ke user
                    \Illuminate\Support\Facades\Log::warning('[SECURITY] Upload foto ditolak. Ekstensi tidak diizinkan: ' . $extensionRaw . ' | Queue ID: ' . $queue->id . ' | User: ' . auth()->id());
                } else {
                    // [SECURITY PATCH] Lapis 2: Verifikasi MIME type asli dari binary data
                    // Mencegah file .php yang disamarkan sebagai .jpg
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->buffer($data);
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

                    if (!in_array($mimeType, $allowedMimes)) {
                        \Illuminate\Support\Facades\Log::warning('[SECURITY] Upload foto ditolak. MIME type tidak valid: ' . $mimeType . ' | Queue ID: ' . $queue->id . ' | User: ' . auth()->id());
                    } else {
                        // [SECURITY PATCH] Lapis 3: Nama file berdasarkan Nama Customer + Nomor Antrean
                        // Contoh hasil: Fadil_AMD-001.jpg
                        $queue->loadMissing('customer');
                        $rawCustomerName = $queue->customer ? $queue->customer->name : 'Customer';

                        // Bersihkan suffix "(On-Site)" untuk customer via kiosk
                        $rawCustomerName = str_replace(' (On-Site)', '', $rawCustomerName);

                        // Sanitasi nama: hanya izinkan huruf, angka, dan spasi (cegah path traversal)
                        $cleanName = preg_replace('/[^a-zA-Z0-9 ]/', '', $rawCustomerName);
                        $cleanName = trim(preg_replace('/\s+/', '_', $cleanName));
                        if (empty($cleanName)) {
                            $cleanName = 'Customer';
                        }

                        // Format: NamaCustomer_NomorAntrean.ekstensi (contoh: Fadil_AMD-001.jpg)
                        $safeFileName = 'queue_photos/' . $cleanName . '_' . $queue->queue_number . '.' . $extension;

                        // Simpan ke public/uploads/queue_photos
                        $destinationPath = public_path('uploads/queue_photos');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }

                        file_put_contents(public_path('uploads/' . $safeFileName), $data);

                        // Simpan ke database
                        \App\Models\QueuePhoto::create([
                            'queue_id'   => $queue->id,
                            'photo_path' => 'uploads/' . $safeFileName
                        ]);
                    }
                }
            }
        }
        event(new \App\Events\QueueUpdated('completed', null, app(\App\Services\TenantManager::class)->getInstanceId()));

        return response()->json(['success' => true, 'message' => 'Status: Selesai']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Service;
use App\Models\Customer;
use App\Events\QueueCheckedIn;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class KioskController extends Controller
{
    /**
     * Menampilkan halaman utama Kiosk.
     */
    public function halamanHome(string $instanceSlug)
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();
        $services = Service::where('instance_id', $instance->id)->where('is_active', true)->get();

        $totalKuotaOffline = (int) ($instance->max_offline_bookings_per_day ?? 0);
        $totalTerisiOffline = 0;
        $sisaKuotaOffline = 0;

        if ($totalKuotaOffline > 0) {
            $totalTerisiOffline = \App\Models\Queue::where('instance_id', $instance->id)
                ->whereDate('queue_date', now()->toDateString())
                ->where('queue_source', 'onsite')
                ->count();
            $sisaKuotaOffline = max(0, $totalKuotaOffline - $totalTerisiOffline);
        }

        return view('Pages.On-siteUser.KioskHome', compact('services', 'instance', 'totalKuotaOffline', 'totalTerisiOffline', 'sisaKuotaOffline'));
    }

    /**
     * Menampilkan halaman input data (Offline).
     */
    public function halamanInput(Request $request, string $instanceSlug)
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();
        $slug = $request->query('layanan');
        
        $service = Service::where('instance_id', $instance->id)->where('is_active', true)->get()
            ->first(function ($item) use ($slug) {
                return Str::slug($item->service_name) === $slug;
            });

        if (!$service) {
            return redirect()->route('kiosk.home', ['instance_slug' => $instanceSlug])->withErrors(['layanan' => 'Layanan tidak ditemukan']);
        }

        return view('Pages.On-siteUser.KioskInput', compact('slug', 'service', 'instance'));
    }

    /**
     * Menyimpan data antrean baru dari Kiosk.
     */
    public function simpanAntreanOffline(Request $request, string $instanceSlug)
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();

        $validated = $request->validate([
            'layanan' => 'required|string',
            'nama' => 'required|string|max:255',
        ]);

        $slug = $validated['layanan'];

        // Cari ID layanan dari database berdasarkan Slug
        $service = Service::where('is_active', true)
            ->where('instance_id', $instance->id)
            ->get()
            ->first(function ($item) use ($slug) {
                return Str::slug($item->service_name) === $slug;
            });

        if (!$service) {
            return back()->withErrors(['layanan' => 'Layanan tidak ditemukan atau sedang tidak aktif.']);
        }

        $today = now()->toDateString();
        $totalKuotaOffline = $instance->max_offline_bookings_per_day ?? 0;
        if ($totalKuotaOffline > 0) {
            $totalTerisiOffline = \App\Models\Queue::where('instance_id', $instance->id)
                ->whereDate('queue_date', $today)
                ->where('queue_source', 'onsite')
                ->count();
            
            if ($totalTerisiOffline >= $totalKuotaOffline) {
                return back()->withErrors(['layanan' => 'Mohon maaf, kuota antrean onsite (kiosk) hari ini sudah penuh.']);
            }
        }

        // Buat atau cari customer guest/offline (bisa dengan nomor hp kosong)
        $customer = Customer::create([
            'instance_id' => $service->instance_id,
            'name' => $validated['nama'],
            'phone' => '-', // Tidak ada nomor HP untuk registrasi langsung kiosk ini
            'is_verified' => true,
        ]);

        // Generate Nomor Antrean (Dengan Locking Level Service untuk mencegah duplikasi/race condition)
        $today = now()->toDateString();
        
        // Kunci baris Service agar proses generate nomor urut menjadi antrean linear (satu per satu)
        $lockedService = \App\Models\Service::where('id', $service->id)->lockForUpdate()->first();

        $lastQueue = Queue::query()
            ->where('service_id', $service->id)
            ->whereDate('queue_date', $today)
            ->lockForUpdate()
            ->latest('id')
            ->first();

        if ($lastQueue) {
            $parts = explode('-', $lastQueue->queue_number);
            $urutan = isset($parts[1]) ? (int) $parts[1] : 0;
            $nextSequence = $urutan + 1;
        } else {
            $nextSequence = 1;
        }

        $queuePrefix = strtoupper($service->queue_prefix ?: substr($service->service_name, 0, 1));
        $queueNumber = $queuePrefix . '-' . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);

        // Langsung simpan ke Queue (sebagai checked_in)
        $queue = Queue::create([
            'instance_id' => $service->instance_id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'queue_number' => $queueNumber,
            'queue_date' => $today,
            'taken_time' => now()->format('H:i:s'),
            'queue_status' => 'waiting',
            'queue_source' => 'onsite',
        ]);

        // Catat Check-in time juga
        $queue->update([
            'check_in_time' => now()->format('H:i:s'),
        ]);

        // Broadcast otomatis untuk update operator
        try {
            broadcast(new QueueCheckedIn($queue))->toOthers();
        } catch (\Exception $e) {}

        // Set session
        session(['kiosk_last_queue_id' => $queue->id]);

        return redirect()->route('kiosk.cetak', ['instance_slug' => $instanceSlug]);
    }

    /**
     * Menampilkan halaman cetak struk.
     */
    public function halamanCetak(string $instanceSlug)
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();
        $queueId = session('kiosk_last_queue_id');
        
        if (!$queueId) {
            return redirect()->route('kiosk.home', ['instance_slug' => $instanceSlug]); // Jika dicoba akses sembarangan
        }

        $queue = Queue::where('instance_id', $instance->id)->with(['service', 'customer'])->find($queueId);
        
        if (!$queue) {
            return redirect()->route('kiosk.home', ['instance_slug' => $instanceSlug]);
        }

        $currentServed = Queue::where('instance_id', $instance->id)
            ->where('service_id', $queue->service_id)
            ->whereDate('queue_date', $queue->queue_date)
            ->whereNotIn('queue_status', ['waiting', 'skipped'])
            ->orderBy('id', 'desc')
            ->first();

        $lastQueue = Queue::where('instance_id', $instance->id)
            ->where('service_id', $queue->service_id)
            ->whereDate('queue_date', $queue->queue_date)
            ->orderBy('id', 'desc')
            ->first();

        return view('Pages.On-siteUser.KioskCetak', [
            'instance' => $instance,
            'queue' => $queue,
            'layanan' => $queue->service->service_name ?? '-',
            'kode' => $queue->service->queue_prefix ?? '-',
            'nomor' => $queue->queue_number,
            'nama' => str_replace(' (On-Site)', '', $queue->customer->name ?? ''),
            'tanggal' => \Carbon\Carbon::parse($queue->queue_date)->format('d M Y') . ' — ' . \Carbon\Carbon::parse($queue->taken_time)->format('H:i'),
            'sedang_dilayani' => $currentServed ? $currentServed->queue_number : '-',
            'total_antrean' => $lastQueue ? $lastQueue->queue_number : $queue->queue_number,
        ]);
    }

    /**
     * Mengunduh file PDF Struk Antrean.
     */
    public function unduhStruk(string $instanceSlug)
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();
        $queueId = session('kiosk_last_queue_id');
        
        if (!$queueId) {
            return redirect()->route('kiosk.home', ['instance_slug' => $instanceSlug]);
        }

        $queue = Queue::where('instance_id', $instance->id)->with(['service', 'customer'])->find($queueId);
        
        if (!$queue) {
            return redirect()->route('kiosk.home', ['instance_slug' => $instanceSlug]);
        }

        $currentServed = Queue::where('instance_id', $instance->id)
            ->where('service_id', $queue->service_id)
            ->whereDate('queue_date', $queue->queue_date)
            ->whereNotIn('queue_status', ['waiting', 'skipped'])
            ->orderBy('id', 'desc')
            ->first();

        $lastQueue = Queue::where('instance_id', $instance->id)
            ->where('service_id', $queue->service_id)
            ->whereDate('queue_date', $queue->queue_date)
            ->orderBy('id', 'desc')
            ->first();

        $data = [
            'instance' => $instance,
            'layanan' => $queue->service->service_name ?? '-',
            'nomor' => $queue->queue_number,
            'nama' => str_replace(' (On-Site)', '', $queue->customer->name ?? ''),
            'tanggal' => \Carbon\Carbon::parse($queue->queue_date)->format('d M Y') . ' — ' . \Carbon\Carbon::parse($queue->taken_time)->format('H:i'),
            'sedang_dilayani' => $currentServed ? $currentServed->queue_number : '-',
            'total_antrean' => $lastQueue ? $lastQueue->queue_number : $queue->queue_number,
        ];

        // Load PDF using dompdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Pages.On-siteUser.KioskStrukPdf', $data);
        
        // Atur ukuran kertas struk thermal: Lebar ~80mm (226pt) tinggi disesuaikan
        $pdf->setPaper([0, 0, 226.77, 350], 'portrait'); 

        return $pdf->download('Struk-Antrean-' . $queue->queue_number . '.pdf');
    }

    /**
     * Menampilkan halaman Scanner QR Code.
     */
    public function halamanScan(string $instanceSlug)
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();
        return view('Pages.On-siteUser.KioskScan', compact('instance'));
    }

    /**
     * Memverifikasi data QR Code yang di-scan.
     */
    public function verifyScan(Request $request, string $instanceSlug): JsonResponse
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();
        $validated = $request->validate([
            'qr_data' => ['required', 'string'],
        ]);

        $qrData = $validated['qr_data']; // Contoh: "BKG-00000001"

        // Ambil ID dari string (Anggap format BKG-00000001)
        if (!preg_match('/BKG-(\d+)/', $qrData, $matches)) {
            return response()->json([
                'success' => false,
                'message' => 'Format QR Code tidak valid.'
            ], 422);
        }

        $queueId = (int) $matches[1];

        // Cari antrean yang sesuai dan pastikan berasal dari instansi ini
        $queue = Queue::query()
            ->with(['service', 'customer'])
            ->where('instance_id', $instance->id)
            ->where('id', $queueId)
            ->whereDate('queue_date', now()->toDateString())
            ->first();

        if (!$queue) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan atau bukan untuk hari ini.'
            ], 404);
        }

        if ($queue->queue_status === 'skipped') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket sudah kedaluwarsa (hangus).'
            ], 422);
        }

        try {
            // Update waktu Check-in
            $queue->update([
                'check_in_time' => now()->format('H:i:s'),
            ]);

            // Kirim sinyal Real-time ke Monitor/Admin
            broadcast(new QueueCheckedIn($queue))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Check-in Berhasil! Silakan menuju ruang tunggu.',
                'queue_number' => $queue->queue_number,
                'service_name' => optional($queue->service)->service_name
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Kiosk Scan - Broadcast Error (Ignore): ' . $e->getMessage());
            
            // Meskipun sinyal monitor gagal, kita tetap anggap check-in BERHASIL di sistem
            return response()->json([
                'success' => true,
                'message' => 'Check-in Berhasil! (Notifikasi monitor tertunda).',
                'queue_number' => $queue->queue_number,
                'service_name' => optional($queue->service)->service_name
            ]);
        }
    }
}

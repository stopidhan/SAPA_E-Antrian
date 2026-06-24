<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Queue;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BookingOnlineController extends Controller
{
    // Berapa menit grace period setelah jam slot dimulai
    private const ONLINE_TICKET_EXPIRATION_MINUTES = 30;

    // Default fallback jika Admin belum isi kolom di service
    private const DEFAULT_SLOT_DURATION_MINUTES = 60;
    private const DEFAULT_SLOT_CAPACITY          = 10;

    // Berapa hari ke depan yang bisa dipilih saat booking
    private const BOOKING_OPEN_DAYS = 2;

    // =========================================================================
    // HALAMAN DASHBOARD — Pilih Layanan
    // =========================================================================

    public function halamanDashboard()
    {
        /** @var Customer|null $authCustomer */
        $authCustomer = Auth::guard('customer')->user();

        $namaUser = $authCustomer ? ($authCustomer->name ?? $authCustomer->nama) : 'Pengguna';

        // Ambil layanan + tambahkan info slot per layanan
        $layanans = Service::query()
            ->where('instance_id', $authCustomer->instance_id)
            ->where('is_active', true)
            ->get()
            ->map(function ($svc) {
                $svc->_slot_duration = $this->getSlotDuration($svc);
                $svc->_slot_capacity = $this->getSlotCapacity($svc);
                return $svc;
            });

        // Mapping warna per prefix
        $colorMap = [
            'A' => [
                'warna'  => 'blue',
                'bg'     => 'bg-blue-600',
                'bgLight'=> 'bg-blue-50',
                'border' => 'border-blue-200',
                'text'   => 'text-blue-600',
                'ring'   => 'ring-blue-100',
                'btnBg'  => 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800',
                'shadow' => 'shadow-blue-100',
            ],
            'B' => [
                'warna'  => 'emerald',
                'bg'     => 'bg-emerald-600',
                'bgLight'=> 'bg-emerald-50',
                'border' => 'border-emerald-200',
                'text'   => 'text-emerald-600',
                'ring'   => 'ring-emerald-100',
                'btnBg'  => 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800',
                'shadow' => 'shadow-emerald-100',
            ],
            'C' => [
                'warna'  => 'amber',
                'bg'     => 'bg-amber-500',
                'bgLight'=> 'bg-amber-50',
                'border' => 'border-amber-200',
                'text'   => 'text-amber-600',
                'ring'   => 'ring-amber-100',
                'btnBg'  => 'bg-amber-500 hover:bg-amber-600 active:bg-amber-700',
                'shadow' => 'shadow-amber-100',
            ],
        ];

        $defaultColor = [
            'warna'  => 'gray',
            'bg'     => 'bg-gray-600',
            'bgLight'=> 'bg-gray-50',
            'border' => 'border-gray-200',
            'text'   => 'text-gray-600',
            'ring'   => 'ring-gray-100',
            'btnBg'  => 'bg-gray-600 hover:bg-gray-700 active:bg-gray-800',
            'shadow' => 'shadow-gray-100',
        ];

        // Cek antrean aktif hari ini (belum di-scan)
        $today = now()->toDateString();

        $activeQueue = Queue::query()
            ->where('customer_id', $authCustomer->id ?? 0)
            ->whereDate('queue_date', $today)
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->whereNull('check_in_time')
            ->with('service')
            ->first();

        $hasActiveQueue = (bool) $activeQueue;
        $nomorAntrean   = $activeQueue ? $activeQueue->queue_number : '-';
        $kodeBooking    = $activeQueue ? 'BKG-' . str_pad((string) $activeQueue->id, 8, '0', STR_PAD_LEFT) : '-';

        // Daftar tanggal yang bisa dipilih (hari ini + N hari ke depan)
        $availableDates = $this->buildAvailableDates();

        return view('Pages.Remoteuser.Dashboard', compact(
            'layanans',
            'namaUser',
            'hasActiveQueue',
            'nomorAntrean',
            'kodeBooking',
            'activeQueue',
            'colorMap',
            'defaultColor',
            'availableDates',
        ));
    }

    // =========================================================================
    // API: GET SLOTS — Dipakai modal Alpine.js di Dashboard
    // =========================================================================

    public function getSlots(Request $request): JsonResponse
    {
        /** @var Customer|null $authCustomer */
        $authCustomer = Auth::guard('customer')->user();

        if (!$authCustomer) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $serviceSlug = $request->query('service_slug');
        $dateStr     = $request->query('date');

        if (!$serviceSlug || !$dateStr) {
            return response()->json(['success' => false, 'message' => 'Parameter tidak lengkap'], 422);
        }

        // Validasi tanggal tidak boleh di masa lalu
        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateStr);
            if ($date->startOfDay()->lt(now()->startOfDay())) {
                return response()->json(['success' => false, 'message' => 'Tanggal tidak valid'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Format tanggal tidak valid'], 422);
        }

        // Cari service berdasarkan slug
        $service = Service::query()
            ->where('instance_id', $authCustomer->instance_id)
            ->where('is_active', true)
            ->get()
            ->first(fn($s) => Str::slug($s->service_name) === $serviceSlug);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Layanan tidak ditemukan'], 404);
        }

        $slots = $this->buildSlots($service, $date);

        return response()->json([
            'success' => true,
            'slots'   => $slots,
        ]);
    }

    // =========================================================================
    // HALAMAN KONFIRMASI — Review sebelum submit
    // =========================================================================

    public function halamanKonfirmasi(Request $request)
    {
        /** @var Customer|null $authCustomer */
        $authCustomer = Auth::guard('customer')->user();

        if (!$authCustomer) {
            return redirect()->route('booking.register');
        }

        $slug        = $request->query('layanan');
        $dateStr     = $request->query('tanggal');
        $selectedSlot = $request->query('slot');

        // Validasi parameter wajib
        if (!$slug || !$dateStr || !$selectedSlot) {
            return redirect()->route('booking.dashboard')
                ->withErrors(['limit_booking' => 'Pilih layanan, tanggal, dan slot waktu terlebih dahulu.']);
        }

        // Cari service
        $service = Service::query()
            ->where('is_active', true)
            ->where('instance_id', $authCustomer->instance_id)
            ->get()
            ->first(fn($item) => Str::slug($item->service_name) === $slug);

        if (!$service) {
            return redirect()->route('booking.dashboard')
                ->withErrors(['limit_booking' => 'Layanan tidak ditemukan.']);
        }

        // Hitung estimasi nomor antrean untuk slot + tanggal ini
        $today = now()->toDateString();
        $queuePrefix = $service->queue_prefix ?: strtoupper(substr($service->service_name, 0, 1));

        $lastQueue = Queue::query()
            ->where('service_id', $service->id)
            ->whereDate('queue_date', $dateStr)
            ->latest('id')
            ->first();

        if ($lastQueue) {
            $parts  = explode('-', $lastQueue->queue_number);
            $urutan = isset($parts[1]) ? (int) $parts[1] : 0;
        } else {
            $urutan = 0;
        }
        $estimatedQueueNumber = $queuePrefix . '-' . str_pad((string) ($urutan + 1), 3, '0', STR_PAD_LEFT);

        // Hitung sisa kapasitas slot
        $slotCapacity = $this->getSlotCapacity($service);
        $slotFilled   = Queue::query()
            ->where('service_id', $service->id)
            ->where('queue_source', 'online')
            ->whereDate('scheduled_date', $dateStr)
            ->where('scheduled_slot', $selectedSlot)
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->count();
        $slotSisa = max(0, $slotCapacity - $slotFilled);

        // Hitung jam akhir slot
        $slotDuration = $this->getSlotDuration($service);
        $slotEndTime  = Carbon::createFromFormat('H:i', $selectedSlot)
            ->addMinutes($slotDuration)
            ->format('H:i');

        return view('Pages.Remoteuser.Konfirmasi', [
            'service'              => $service,
            'customer'             => $authCustomer,
            'slug'                 => $slug,
            'selectedDate'         => $dateStr,
            'selectedSlot'         => $selectedSlot,
            'slotEndTime'          => $slotEndTime,
            'slotDuration'         => $slotDuration,
            'slotSisa'             => $slotSisa,
            'estimatedQueueNumber' => $estimatedQueueNumber,
            'estimasiWaktu'        => $slotDuration,
        ]);
    }

    // =========================================================================
    // PROSES AMBIL ANTREAN — Form submit dari Konfirmasi
    // =========================================================================

    public function prosesAmbilAntrean(Request $request)
    {
        $validated = $request->validate([
            'layanan' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'slot'    => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
        ]);

        /** @var Customer|null $authCustomer */
        $authCustomer = Auth::guard('customer')->user();

        if (!$authCustomer) {
            return redirect()->route('booking.register')
                ->withErrors(['booking_register' => 'Silakan login terlebih dahulu.']);
        }

        // Cek antrean aktif yang belum discan
        $activeQueue = Queue::query()
            ->where('customer_id', $authCustomer->id)
            ->whereDate('queue_date', now()->toDateString())
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->whereNull('check_in_time')
            ->exists();

        if ($activeQueue) {
            return back()->withErrors([
                'limit_booking' => 'Anda masih memiliki antrean aktif yang belum di-scan. Selesaikan terlebih dahulu.',
            ])->withInput();
        }

        // Cari service
        $slug    = $validated['layanan'];
        $service = Service::query()
            ->where('is_active', true)
            ->get()
            ->first(fn($item) => Str::slug($item->service_name) === $slug);

        if (!$service) {
            return back()->withErrors(['limit_booking' => 'Layanan tidak ditemukan.'])->withInput();
        }

        if ((int) $authCustomer->instance_id !== (int) $service->instance_id) {
            return back()->withErrors(['limit_booking' => 'Akun tidak terdaftar pada instansi ini.'])->withInput();
        }

        $slotCapacity = $this->getSlotCapacity($service);
        $lockKey = 'slot_lock_' . $service->id . '_' . $validated['tanggal'] . '_' . $validated['slot'];

        $queue = Cache::lock($lockKey, 10)->block(5, function () use ($service, $authCustomer, $validated, $slotCapacity) {
            // Cek kapasitas slot (atomic)
            $slotFilled = Queue::query()
                ->where('service_id', $service->id)
                ->where('queue_source', 'online')
                ->whereDate('scheduled_date', $validated['tanggal'])
                ->where('scheduled_slot', $validated['slot'])
                ->whereIn('queue_status', ['waiting', 'called', 'serving'])
                ->count();

            if ($slotFilled >= $slotCapacity) {
                return null;
            }

            // Database-level lock
            Service::where('id', $service->id)->lockForUpdate()->first();

            $queueDate   = $validated['tanggal'];
            $prefix      = $service->queue_prefix ?: strtoupper(substr($service->service_name, 0, 1));

            $lastQueue = Queue::query()
                ->where('instance_id', $service->instance_id)
                ->where('service_id', $service->id)
                ->whereDate('queue_date', $queueDate)
                ->latest('id')
                ->first();

            $urutan = $lastQueue
                ? ((int) explode('-', $lastQueue->queue_number)[1] ?? 0) + 1
                : 1;

            $queueNumber = $prefix . '-' . str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);

            return Queue::create([
                'instance_id'    => $service->instance_id,
                'customer_id'    => $authCustomer->id,
                'service_id'     => $service->id,
                'queue_number'   => $queueNumber,
                'queue_date'     => $queueDate,
                'scheduled_date' => $validated['tanggal'],
                'scheduled_slot' => $validated['slot'],
                'taken_time'     => now()->format('H:i:s'),
                'queue_status'   => 'waiting',
                'queue_source'   => 'online',
            ]);
        });

        if (!$queue) {
            return back()->withErrors([
                'limit_booking' => 'Slot waktu yang dipilih baru saja penuh. Silakan pilih slot lain.',
            ])->withInput();
        }

        session([
            'booking_last_queue_id'     => $queue->id,
            'booking_last_queue_number' => $queue->queue_number,
            'booking_last_service_name' => $service->service_name,
        ]);

        return redirect()->route('booking.tiket');
    }

    // =========================================================================
    // HALAMAN TIKET
    // =========================================================================

    public function setHalamanTiket(Request $request)
    {
        $queueId = $request->input('queue_id');
        if ($queueId) {
            session(['booking_last_queue_id' => $queueId]);
        }
        return redirect()->route('booking.tiket');
    }

    public function halamanTiket(Request $request)
    {
        $this->expireStaleOnlineWaitingQueues();

        /** @var Customer|null $authCustomer */
        $authCustomer = Auth::guard('customer')->user();

        if (!$authCustomer) {
            return redirect()->route('booking.register')
                ->withErrors(['booking_register' => 'Silakan login terlebih dahulu.']);
        }

        $queueId = (int) $request->query('queue_id', session('booking_last_queue_id'));

        if ($queueId <= 0) {
            return redirect()->route('booking.inventory')
                ->withErrors(['ticket_not_found' => 'Tiket tidak ditemukan.']);
        }

        $queue = Queue::query()
            ->with(['service', 'customer'])
            ->where('id', $queueId)
            ->where('queue_source', 'online')
            ->where('customer_id', $authCustomer->id)
            ->first();

        if (!$queue) {
            return redirect()->route('booking.inventory')
                ->withErrors(['ticket_not_found' => 'Tiket tidak ditemukan atau tidak sesuai akun Anda.']);
        }

        // Batas waktu: jam slot + grace period (atau fallback ke created_at + 30 mnt)
        if ($queue->scheduled_date && $queue->scheduled_slot) {
            $slotStart  = Carbon::parse($queue->scheduled_date->toDateString() . ' ' . $queue->scheduled_slot);
            $batasWaktu = $slotStart->copy()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES);
        } else {
            $batasWaktu = $queue->created_at
                ? $queue->created_at->copy()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)
                : now()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES);
        }

        $isExpired = $queue->queue_status === 'skipped'
            || (is_null($queue->check_in_time) && now()->greaterThanOrEqualTo($batasWaktu));

        if ($isExpired && $queue->queue_status === 'waiting') {
            $queue->update(['queue_status' => 'skipped']);
            $queue->refresh();
        }

        return view('Pages.Remoteuser.Tiket', [
            'queue'        => $queue,
            'queueId'      => $queue->id,
            'nama'         => (string) optional($queue->customer)->name,
            'whatsapp'     => (string) optional($queue->customer)->phone,
            'layanan'      => (string) optional($queue->service)->service_name,
            'nomorAntrean' => (string) $queue->queue_number,
            'kodeBooking'  => 'BKG-' . str_pad((string) $queue->id, 8, '0', STR_PAD_LEFT),
            'batasWaktu'   => $batasWaktu->toIso8601String(),
            'isExpired'    => $queue->queue_status === 'skipped',
        ]);
    }

    // =========================================================================
    // HALAMAN RIWAYAT
    // =========================================================================

    public function halamanRiwayat()
    {
        $customer = Auth::guard('customer')->user();

        $riwayatAntrean = Queue::with('service')
            ->where('customer_id', $customer->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('Pages.Remoteuser.Riwayat', compact('riwayatAntrean'));
    }

    // =========================================================================
    // HALAMAN INVENTORY
    // =========================================================================

    public function halamanInventory()
    {
        $this->expireStaleOnlineWaitingQueues();

        /** @var Customer|null $authCustomer */
        $authCustomer = Auth::guard('customer')->user();

        if (!$authCustomer) {
            return redirect()->route('booking.register')
                ->withErrors(['booking_register' => 'Silakan login terlebih dahulu.']);
        }

        $savedTickets = Queue::query()
            ->with(['service', 'customer'])
            ->where('queue_source', 'online')
            ->where('customer_id', $authCustomer->id)
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->whereNull('check_in_time')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function (Queue $queue) {
                $prefix = strtoupper((string) optional($queue->service)->queue_prefix);

                $colorMap = [
                    'A' => ['bg-blue-600',    'text-blue-600',    'bg-blue-50',    'border-blue-100'],
                    'B' => ['bg-emerald-600', 'text-emerald-600', 'bg-emerald-50', 'border-emerald-100'],
                    'C' => ['bg-amber-500',   'text-amber-600',   'bg-amber-50',   'border-amber-100'],
                ];

                $colors = $colorMap[$prefix] ?? ['bg-gray-600', 'text-gray-600', 'bg-gray-50', 'border-gray-100'];

                $statusMap = [
                    'waiting'   => 'Menunggu',
                    'called'    => 'Dipanggil',
                    'serving'   => 'Dilayani',
                    'completed' => 'Selesai',
                    'skipped'   => 'Terlewat',
                ];

                // Hitung batas waktu berdasarkan slot jika ada
                if ($queue->scheduled_date && $queue->scheduled_slot) {
                    $slotStart  = Carbon::parse($queue->scheduled_date->toDateString() . ' ' . $queue->scheduled_slot);
                    $batasWaktu = $slotStart->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)->toIso8601String();
                } else {
                    $batasWaktu = $queue->created_at
                        ? $queue->created_at->copy()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)->toIso8601String()
                        : now()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)->toIso8601String();
                }

                return (object) [
                    'queueId'     => $queue->id,
                    'nomor'       => $queue->queue_number,
                    'kode'        => 'BKG-' . str_pad((string) $queue->id, 8, '0', STR_PAD_LEFT),
                    'layanan'     => optional($queue->service)->service_name ?? 'Layanan',
                    'kodeHuruf'   => $prefix !== '' ? $prefix : 'Q',
                    'tanggal'     => optional($queue->queue_date)->format('d M Y') ?? now()->format('d M Y'),
                    'slot'        => $queue->scheduled_slot ?? null,
                    'status'      => $queue->queue_status === 'skipped'
                        ? 'Hangus'
                        : ($statusMap[$queue->queue_status] ?? ucfirst((string) $queue->queue_status)),
                    'isExpired'   => $queue->queue_status === 'skipped',
                    'batasWaktu'  => $batasWaktu,
                    'warnaBg'     => $colors[0],
                    'warnaText'   => $colors[1],
                    'warnaLight'  => $colors[2],
                    'warnaBorder' => $colors[3],
                ];
            });

        return view('Pages.Remoteuser.Inventory', compact('savedTickets'));
    }

    // =========================================================================
    // TANDAI TIKET HANGUS (API)
    // =========================================================================

    public function tandaiTiketHangus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'queue_id' => ['required', 'integer'],
        ]);

        /** @var Customer|null $authCustomer */
        $authCustomer = Auth::guard('customer')->user();

        if (!$authCustomer) {
            return response()->json(['success' => false, 'message' => 'Sesi customer tidak ditemukan.'], 401);
        }

        $queue = Queue::query()
            ->where('id', $validated['queue_id'])
            ->where('queue_source', 'online')
            ->where('customer_id', $authCustomer->id)
            ->first();

        if (!$queue) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan.'], 404);
        }

        if ($queue->queue_status !== 'waiting') {
            return response()->json([
                'success' => true,
                'expired' => $queue->queue_status === 'skipped',
                'status'  => $queue->queue_status,
                'message' => 'Status tiket sudah diproses sebelumnya.',
            ]);
        }

        if (!is_null($queue->check_in_time)) {
            return response()->json([
                'success' => false,
                'expired' => false,
                'message' => 'Tiket sudah di-scan, tidak dapat ditandai hangus.',
            ], 422);
        }

        // Hitung batas waktu hangus
        if ($queue->scheduled_date && $queue->scheduled_slot) {
            $slotStart  = Carbon::parse($queue->scheduled_date->toDateString() . ' ' . $queue->scheduled_slot);
            $expiredAt  = $slotStart->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES);
        } else {
            $expiredAt = $queue->created_at
                ? $queue->created_at->copy()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)
                : now()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES);
        }

        if (now()->lt($expiredAt)) {
            return response()->json([
                'success' => false,
                'expired' => false,
                'message' => 'Tiket belum melewati batas waktu.',
            ], 422);
        }

        $queue->update(['queue_status' => 'skipped']);

        return response()->json([
            'success' => true,
            'expired' => true,
            'status'  => 'skipped',
            'message' => 'Tiket berhasil ditandai hangus.',
        ]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Bangun daftar tanggal yang bisa dipilih untuk booking
     */
    private function buildAvailableDates(): array
    {
        $dates = [];
        for ($i = 0; $i < self::BOOKING_OPEN_DAYS; $i++) {
            $date     = now()->addDays($i);
            $dates[]  = [
                'value'    => $date->toDateString(),
                'label'    => $i === 0 ? 'Hari Ini' : 'Besok',
                'sublabel' => $date->translatedFormat('l, d M'),
            ];
        }
        return $dates;
    }

    /**
     * Bangun daftar slot waktu untuk tanggal & service tertentu
     */
    private function buildSlots(Service $service, Carbon $date): array
    {
        $duration = $this->getSlotDuration($service);
        $capacity = $this->getSlotCapacity($service);

        // Ambil jam operasional dari instance (default 08:00–16:00)
        $instance  = \App\Models\Instance::find($service->instance_id);
        $openTime  = '08:00';
        $closeTime = '16:00';

        if ($instance && $instance->operational_hours) {
            $dayName = $date->locale('en')->dayName; // e.g. "Monday"
            $opHours = collect($instance->operational_hours)
                ->first(fn($d) => isset($d['name']) && strtolower($d['name']) === strtolower($dayName));

            if ($opHours && ($opHours['isOpen'] ?? false)) {
                $openTime  = $opHours['openTime']  ?? '08:00';
                $closeTime = $opHours['closeTime'] ?? '16:00';
            } elseif ($opHours && !($opHours['isOpen'] ?? true)) {
                return []; // Hari tutup
            }
        }

        // Hitung jumlah booking per slot untuk tanggal ini
        $existingCounts = Queue::query()
            ->where('service_id', $service->id)
            ->where('queue_source', 'online')
            ->whereDate('scheduled_date', $date->toDateString())
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->whereNotNull('scheduled_slot')
            ->get()
            ->groupBy('scheduled_slot')
            ->map(fn($g) => $g->count());

        $slots      = [];
        $current    = Carbon::createFromFormat('H:i', $openTime, config('app.timezone', 'Asia/Jakarta'));
        $close      = Carbon::createFromFormat('H:i', $closeTime, config('app.timezone', 'Asia/Jakarta'));

        // Jangan tampilkan slot yang sudah lewat (untuk hari ini)
        $now = now();

        while ($current->copy()->addMinutes($duration)->lte($close)) {
            $slotKey  = $current->format('H:i');
            $filled   = $existingCounts->get($slotKey, 0);
            $sisa     = max(0, $capacity - $filled);
            $isFull   = $sisa === 0;

            // Lewati slot yang sudah lewat (hanya untuk hari ini)
            $isPast = $date->isToday() && $current->lte($now);

            if (!$isPast) {
                $slots[] = [
                    'slot'  => $slotKey,
                    'sisa'  => $sisa,
                    'full'  => $isFull,
                ];
            }

            $current->addMinutes($duration);
        }

        return $slots;
    }

    /**
     * Ambil durasi slot dari service (atau default fallback)
     */
    private function getSlotDuration(Service $service): int
    {
        if (isset($service->slot_duration) && (int) $service->slot_duration > 0) {
            return (int) $service->slot_duration;
        }
        return self::DEFAULT_SLOT_DURATION_MINUTES;
    }

    /**
     * Ambil kapasitas slot dari service (atau default fallback)
     */
    private function getSlotCapacity(Service $service): int
    {
        if (isset($service->slot_capacity) && (int) $service->slot_capacity > 0) {
            return (int) $service->slot_capacity;
        }
        return self::DEFAULT_SLOT_CAPACITY;
    }

    /**
     * Hanguskan antrean online yang melewati batas waktu & belum di-scan
     */
    private function expireStaleOnlineWaitingQueues(): void
    {
        Queue::query()
            ->where('queue_source', 'online')
            ->where('queue_status', 'waiting')
            ->whereNull('check_in_time')
            ->where('created_at', '<=', now()->subMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES))
            ->update(['queue_status' => 'skipped']);
    }
}

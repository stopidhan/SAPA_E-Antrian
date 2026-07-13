<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Queue;
use App\Models\Service;
use App\Models\ServiceSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingOnlineController extends Controller
{
    // Berapa menit grace period setelah jam slot dimulai
    private const ONLINE_TICKET_EXPIRATION_MINUTES = 30;


    // Berapa hari ke depan yang bisa dipilih saat booking
    private const BOOKING_OPEN_DAYS = 30;

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
            ->get();

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

        // Cek antrean aktif hari ini (untuk lock tombol di dashboard)
        $today = now()->toDateString();
        $hasActiveQueue = Queue::query()
            ->where('customer_id', $authCustomer->id ?? 0)
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->exists();

        // Cek antrean aktif hari ini yang belum di-scan (untuk tampilkan QR tiket di dashboard)
        $activeQueue = Queue::query()
            ->where('customer_id', $authCustomer->id ?? 0)
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->whereNull('check_in_time')
            ->orderBy('queue_date', 'asc')
            ->with('service')
            ->first();

        // Hitung total booking hari ini (maksimal 2)
        $bookingTodayCount = Queue::query()
            ->where('customer_id', $authCustomer->id ?? 0)
            ->whereDate('queue_date', $today)
            ->count();

        $nomorAntrean   = $activeQueue ? $activeQueue->queue_number : '-';
        $kodeBooking    = $activeQueue ? 'BKG-' . str_pad((string) $activeQueue->id, 8, '0', STR_PAD_LEFT) : '-';

        // Daftar tanggal yang bisa dipilih (hari ini + N hari ke depan)
        $availableDates = $this->buildAvailableDates();

        $instansi = \App\Models\Instance::find($authCustomer->instance_id);
        $operationalHours = $instansi ? ($instansi->operational_hours ?? []) : [];

        $instanceId = $authCustomer->instance_id ?? 0;
        $slotsCount = \App\Models\InstanceSlot::where('instance_id', $instanceId)->count();
        $hasSlotsConfigured = $slotsCount > 0;

        $totalDailyCapacity = \App\Models\InstanceSlot::where('instance_id', $instanceId)->sum('capacity') ?: 0;

        // Hitung 30 Hari Kerja Ke Depan
        $workingDaysMap = [];
        $jsDayMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        foreach ($operationalHours as $op) {
            $workingDaysMap[$op['name']] = $op['isOpen'] ?? false;
        }

        $validDates = [];
        $dateWalker = now();
        $daysFound = 0;

        while ($daysFound < self::BOOKING_OPEN_DAYS) {
            $dayName = $jsDayMap[$dateWalker->dayOfWeek];
            $isOpen = $workingDaysMap[$dayName] ?? true;
            if ($isOpen) {
                $validDates[] = $dateWalker->toDateString();
                $daysFound++;
            }
            if ($daysFound < self::BOOKING_OPEN_DAYS) {
                $dateWalker->addDay();
            }
        }
        $maxBookingDate = count($validDates) > 0 ? end($validDates) : now()->toDateString();

        // Hitung antrean per tanggal
        $dailyBookings = Queue::query()
            ->where('instance_id', $instanceId)
            ->where('queue_source', 'online')
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->whereBetween('scheduled_date', [now()->toDateString(), $maxBookingDate])
            ->selectRaw('scheduled_date, count(*) as count')
            ->groupBy('scheduled_date')
            ->get()
            ->pluck('count', 'scheduled_date');

        $dayAvailability = [];
        $current = now();
        while ($current->toDateString() <= $maxBookingDate) {
            $dStr = $current->toDateString();
            $booked = $dailyBookings->get($dStr, 0);
            $sisa = max(0, $totalDailyCapacity - $booked);
            $dayAvailability[$dStr] = [
                'sisa' => $sisa,
                'capacity' => $totalDailyCapacity,
                'full' => $sisa === 0
            ];
            $current->addDay();
        }

        return view('Pages.Remoteuser.Dashboard', compact(
            'layanans',
            'namaUser',
            'hasActiveQueue',
            'bookingTodayCount',
            'nomorAntrean',
            'kodeBooking',
            'activeQueue',
            'colorMap',
            'defaultColor',
            'availableDates',
            'operationalHours',
            'dayAvailability',
            'hasSlotsConfigured',
            'instansi',
            'maxBookingDate'
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

        $tz = config('app.timezone', 'Asia/Jakarta');
        
        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateStr, $tz)->startOfDay();
            if ($date->lt(now($tz)->startOfDay())) {
                return response()->json(['success' => false, 'message' => 'Tanggal tidak valid'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Format tanggal tidak valid'], 422);
        }

        $service = Service::query()
            ->where('instance_id', $authCustomer->instance_id)
            ->where('is_active', true)
            ->get()
            ->first(fn($s) => Str::slug($s->service_name) === $serviceSlug);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Layanan tidak ditemukan'], 404);
        }

        // Ambil instance slot yang sudah didefinisikan Admin
        $instanceSlots = \App\Models\InstanceSlot::query()
            ->where('instance_id', $authCustomer->instance_id)
            ->orderBy('start_time')
            ->get();

        // Hitung jumlah booking per slot untuk tanggal ini di seluruh instansi (gabungan semua layanan)
        $existingCounts = Queue::query()
            ->where('instance_id', $authCustomer->instance_id)
            ->where('queue_source', 'online')
            ->whereDate('scheduled_date', $date->toDateString())
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->whereNotNull('scheduled_slot')
            ->get()
            ->groupBy('scheduled_slot')
            ->map(fn($g) => $g->count());

        $slots = [];
        $now = now($tz);

        foreach ($instanceSlots as $islot) {
            $startStr = $islot->start_time;
            $endStr   = $islot->end_time;
            $capacity = $islot->capacity;

            $filled = $existingCounts->get($startStr, 0);
            $sisa   = max(0, $capacity - $filled);
            $isFull = $sisa === 0;

            try {
                $slotTime = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . $startStr, $tz);
                $isPast   = $date->isSameDay($now) && $slotTime->lte($now);
            } catch (\Exception $e) {
                $isPast   = false;
            }

            if (!$isPast) {
                $slots[] = [
                    'slot'    => $startStr,
                    'display' => $startStr . ' - ' . $endStr,
                    'sisa'    => $sisa,
                    'full'    => $isFull,
                    'capacity'=> $capacity,
                ];
            }
        }

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

        $instanceSlot = \App\Models\InstanceSlot::query()
            ->where('instance_id', $authCustomer->instance_id)
            ->where('start_time', $selectedSlot)
            ->first();

        if (!$instanceSlot) {
            return redirect()->route('booking.dashboard')
                ->withErrors(['limit_booking' => 'Slot waktu tidak valid.']);
        }

        $slotCapacity = $instanceSlot->capacity;
        $slotEndTime  = $instanceSlot->end_time;
        // Default estimasi waktu pelayanan, bisa di-set hardcode misal 30 menit atau dibiarkan sama seperti durasi slot
        $slotDuration = Carbon::parse($selectedSlot)->diffInMinutes(Carbon::parse($slotEndTime));

        // Hitung sisa kapasitas slot untuk instansi ini (seluruh layanan)
        $slotFilled   = Queue::query()
            ->where('instance_id', $authCustomer->instance_id)
            ->where('queue_source', 'online')
            ->whereDate('scheduled_date', $dateStr)
            ->where('scheduled_slot', $selectedSlot)
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->count();
        $slotSisa = max(0, $slotCapacity - $slotFilled);

        // Hitung interval rata-rata pelayanan berdasarkan kapasitas slot
        $interval = $slotCapacity > 0 ? (int) floor($slotDuration / $slotCapacity) : 30;

        // Tentukan urutan kustomer dalam slot ini (customer saat ini menempati indeks ke- $slotFilled)
        $tz = config('app.timezone', 'Asia/Jakarta');
        $estimatedServiceStart = Carbon::createFromFormat('Y-m-d H:i', $dateStr . ' ' . $selectedSlot, $tz)
            ->addMinutes($slotFilled * $interval);

        // Wajib Hadir Sebelum = estimasi mulai pelayanan - 30 menit
        $arrivalLimitTime = $estimatedServiceStart->copy()->subMinutes(30)->format('H:i');

        $instansi = \App\Models\Instance::find($authCustomer->instance_id);

        return view('Pages.Remoteuser.Konfirmasi', [
            'service'              => $service,
            'customer'             => $authCustomer,
            'slug'                 => $slug,
            'selectedDate'         => $dateStr,
            'selectedSlot'         => $selectedSlot,
            'slotEndTime'          => $slotEndTime,
            'arrivalLimitTime'     => $arrivalLimitTime,
            'slotDuration'         => $slotDuration,
            'slotSisa'             => $slotSisa,
            'estimatedQueueNumber' => $estimatedQueueNumber,
            'estimasiWaktu'        => $slotDuration,
            'instansi'             => $instansi,
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

        // 1. Cek limit 2 antrean per hari
        $bookingTodayCount = Queue::query()
            ->where('customer_id', $authCustomer->id)
            ->whereDate('queue_date', $validated['tanggal'])
            ->count();

        if ($bookingTodayCount >= 2) {
            return back()->withErrors([
                'limit_booking' => 'Limit antrean Anda telah habis. Maksimal 2 kali pengambilan antrean online per hari.',
            ])->withInput();
        }

        // 2. Cek jika ada antrean aktif yang sedang berjalan (waiting, called, serving)
        $activeQueue = Queue::query()
            ->where('customer_id', $authCustomer->id)
            ->whereIn('queue_status', ['waiting', 'called', 'serving'])
            ->exists();

        if ($activeQueue) {
            return back()->withErrors([
                'limit_booking' => 'Anda masih memiliki antrean aktif yang sedang berjalan. Selesaikan terlebih dahulu sebelum mengambil antrean baru.',
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

        $instanceSlot = \App\Models\InstanceSlot::query()
            ->where('instance_id', $authCustomer->instance_id)
            ->where('start_time', $validated['slot'])
            ->first();

        if (!$instanceSlot) {
            return back()->withErrors(['limit_booking' => 'Slot waktu tidak valid.'])->withInput();
        }

        $slotCapacity = $instanceSlot->capacity;

        $lockKey = 'slot_lock_' . $authCustomer->instance_id . '_' . $validated['tanggal'] . '_' . $validated['slot'];

        $queue = Cache::lock($lockKey, 10)->block(5, function () use ($service, $authCustomer, $validated, $slotCapacity) {
            // Cek kapasitas slot di seluruh instansi
            $slotFilled = Queue::query()
                ->where('instance_id', $authCustomer->instance_id)
                ->where('queue_source', 'online')
                ->whereDate('scheduled_date', $validated['tanggal'])
                ->where('scheduled_slot', $validated['slot'])
                ->whereIn('queue_status', ['waiting', 'called', 'serving'])
                ->count();

            if ($slotFilled >= $slotCapacity) {
                return null;
            }

            // Database-level lock (Race condition fix)
            return DB::transaction(function () use ($service, $authCustomer, $validated) {
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

        $batasWaktu = $this->calculateBatasWaktu($queue);

        $isExpired = $queue->queue_status === 'skipped'
            || (is_null($queue->check_in_time) && now(config('app.timezone', 'Asia/Jakarta'))->greaterThanOrEqualTo($batasWaktu));

        if ($isExpired && $queue->queue_status === 'waiting') {
            $queue->update(['queue_status' => 'skipped']);
            $queue->refresh();
        }

        $instansi = \App\Models\Instance::find($authCustomer->instance_id);

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
            'instansi'     => $instansi,
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

        $instansi = \App\Models\Instance::find($customer->instance_id);

        return view('Pages.Remoteuser.Riwayat', compact('riwayatAntrean', 'instansi'));
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

                $batasWaktuObj = $this->calculateBatasWaktu($queue);
                $batasWaktu = $batasWaktuObj->toIso8601String();

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

        $instansi = \App\Models\Instance::find($authCustomer->instance_id);

        return view('Pages.Remoteuser.Inventory', compact('savedTickets', 'instansi'));
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

        $expiredAt = $this->calculateBatasWaktu($queue);

        if (now(config('app.timezone', 'Asia/Jakarta'))->lt($expiredAt)) {
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
            $label    = $i === 0 ? 'Hari Ini' : ($i === 1 ? 'Besok' : $date->translatedFormat('l'));
            $sublabel = $date->translatedFormat('d M');

            $dates[]  = [
                'value'    => $date->toDateString(),
                'label'    => $label,
                'sublabel' => $sublabel,
            ];
        }
        return $dates;
    }

    /**
     * Hitung batas waktu tiket (kapan tiket hangus)
     */
    private function calculateBatasWaktu(Queue $queue): \Carbon\Carbon
    {
        $tz = config('app.timezone', 'Asia/Jakarta');

        if ($queue->scheduled_date && $queue->scheduled_slot) {
            $scheduledDateStr = Carbon::parse($queue->scheduled_date, $tz)->toDateString();

            $instanceSlot = \App\Models\InstanceSlot::query()
                ->where('instance_id', $queue->instance_id)
                ->where('start_time', $queue->scheduled_slot)
                ->first();

            if ($instanceSlot) {
                $slotCapacity = $instanceSlot->capacity;
                $slotEndTime  = $instanceSlot->end_time;
                $slotDuration = Carbon::createFromFormat('H:i', $queue->scheduled_slot, $tz)->diffInMinutes(Carbon::createFromFormat('H:i', $slotEndTime, $tz));
                $interval     = $slotCapacity > 0 ? (int) floor($slotDuration / $slotCapacity) : 30;

                $slotFilledBefore = Queue::query()
                    ->where('instance_id', $queue->instance_id)
                    ->where('queue_source', 'online')
                    ->whereDate('scheduled_date', $scheduledDateStr)
                    ->where('scheduled_slot', $queue->scheduled_slot)
                    ->where('id', '<', $queue->id)
                    ->count();

                $estimatedServiceStart = Carbon::createFromFormat('Y-m-d H:i', $scheduledDateStr . ' ' . $queue->scheduled_slot, $tz)
                    ->addMinutes($slotFilledBefore * $interval);

                return $estimatedServiceStart->copy()->subMinutes(30);
            }

            $slotStart = Carbon::createFromFormat('Y-m-d H:i', $scheduledDateStr . ' ' . $queue->scheduled_slot, $tz);
            return $slotStart->copy()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES);
        }

        return $queue->created_at
            ? $queue->created_at->copy()->setTimezone($tz)->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)
            : now($tz)->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES);
    }

    /**
     * Hanguskan antrean online yang melewati batas waktu & belum di-scan
     */
    private function expireStaleOnlineWaitingQueues(): void
    {
        $tz = config('app.timezone', 'Asia/Jakarta');

        // 1. Expire yang tidak memiliki slot
        Queue::query()
            ->where('queue_source', 'online')
            ->where('queue_status', 'waiting')
            ->whereNull('check_in_time')
            ->whereNull('scheduled_slot')
            ->where('created_at', '<=', now($tz)->subMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES))
            ->update(['queue_status' => 'skipped']);

        // 2. Expire yang memiliki slot (evaluasi di PHP untuk menghitung antrean dinamis)
        // Hanya cek untuk antrean hari ini atau sebelumnya (antrean besok belum bisa hangus)
        $queuesWithSlot = Queue::query()
            ->where('queue_source', 'online')
            ->where('queue_status', 'waiting')
            ->whereNull('check_in_time')
            ->whereNotNull('scheduled_slot')
            ->whereDate('scheduled_date', '<=', now($tz)->toDateString())
            ->get();

        $expiredIds = [];
        foreach ($queuesWithSlot as $q) {
            $batasWaktu = $this->calculateBatasWaktu($q);
            if (now($tz)->greaterThanOrEqualTo($batasWaktu)) {
                $expiredIds[] = $q->id;
            }
        }

        if (!empty($expiredIds)) {
            Queue::whereIn('id', $expiredIds)->update(['queue_status' => 'skipped']);
        }
    }
}

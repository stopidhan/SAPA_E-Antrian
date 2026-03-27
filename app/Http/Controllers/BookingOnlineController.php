<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Queue;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingOnlineController extends Controller
{
    private const ONLINE_TICKET_EXPIRATION_MINUTES = 30;

    public function halamanDashboard()
    {
        $layanans = Service::all();

        return view('Pages.Remoteuser.Dashboard', compact('layanans'));
    }

    public function prosesAmbilAntrean(Request $request)
    {
        $validated = $request->validate([
            'layanan' => ['required', 'string'],
        ]);

        /** @var Customer|null $authCustomer */
        $authCustomer = Auth::guard('customer')->user();

        if (!$authCustomer) {
            return redirect()->route('booking.register')
                ->withErrors(['booking_register' => 'Silakan login terlebih dahulu sebelum mengambil antrean.']);
        }

        $bookingTodayCount = Queue::query()
            ->whereDate('queue_date', now()->toDateString())
            ->where('queue_source', 'online')
            ->where('customer_id', $authCustomer->id)
            ->count();

        if ($bookingTodayCount >= 2) {
            return back()
                ->withErrors(['limit_booking' => 'Limit antrean Anda telah habis. Maksimal 2 kali pengambilan antrean online per hari.'])
                ->withInput();
        }

        $slug = $validated['layanan'];

        if (Service::query()->count() === 0) {
            return back()
                ->withErrors(['limit_booking' => 'Data layanan belum tersedia. Hubungi petugas untuk menyiapkan layanan terlebih dahulu.'])
                ->withInput();
        }

        $service = Service::query()
            ->where('is_active', true)
            ->get()
            ->first(function ($item) use ($slug) {
                return Str::slug($item->service_name) === $slug;
            });

        if (!$service) {
            return back()
                ->withErrors(['limit_booking' => 'Layanan tidak ditemukan atau tidak aktif.'])
                ->withInput();
        }

        if ((int) $authCustomer->instance_id !== (int) $service->instance_id) {
            return back()
                ->withErrors(['limit_booking' => 'Akun customer tidak terdaftar pada instansi layanan ini.'])
                ->withInput();
        }

        $today = now()->toDateString();
        $todayQueueSequence = Queue::query()
            ->where('instance_id', $service->instance_id)
            ->where('service_id', $service->id)
            ->whereDate('queue_date', $today)
            ->count() + 1;

        $queuePrefix = $service->queue_prefix ?: strtoupper(substr($service->service_name, 0, 1));
        $queueNumber = $queuePrefix . '-' . str_pad((string) $todayQueueSequence, 3, '0', STR_PAD_LEFT);

        $queue = Queue::create([
            'instance_id' => $service->instance_id,
            'customer_id' => $authCustomer->id,
            'service_id' => $service->id,
            'queue_number' => $queueNumber,
            'queue_date' => $today,
            'taken_time' => now()->format('H:i:s'),
            'queue_status' => 'waiting',
            'queue_source' => 'online',
        ]);

        session([
            'booking_last_queue_id' => $queue->id,
            'booking_last_queue_number' => $queue->queue_number,
            'booking_last_service_name' => $service->service_name,
        ]);

        return redirect()->route('booking.tiket', ['queue_id' => $queue->id]);
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
                ->withErrors(['ticket_not_found' => 'Tiket tidak ditemukan. Silakan ambil antrean terlebih dahulu.']);
        }

        $queueQuery = Queue::query()
            ->with(['service', 'customer'])
            ->where('id', $queueId)
            ->where('queue_source', 'online')
            ->where('customer_id', $authCustomer->id);

        $queue = $queueQuery->first();

        if (!$queue) {
            return redirect()->route('booking.inventory')
                ->withErrors(['ticket_not_found' => 'Tiket tidak ditemukan atau tidak sesuai dengan akun Anda.']);
        }

        $batasWaktu = $queue->created_at
            ? $queue->created_at->copy()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)
            : now()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES);

        $isExpired = $queue->queue_status === 'skipped' || now()->greaterThanOrEqualTo($batasWaktu);

        if ($isExpired && $queue->queue_status === 'waiting') {
            $queue->update(['queue_status' => 'skipped']);
            $queue->refresh();
        }

        return view('Pages.Remoteuser.Tiket', [
            'queue' => $queue,
            'queueId' => $queue->id,
            'nama' => (string) optional($queue->customer)->name,
            'whatsapp' => (string) optional($queue->customer)->phone,
            'layanan' => (string) optional($queue->service)->service_name,
            'nomorAntrean' => (string) $queue->queue_number,
            'kodeBooking' => 'BKG-' . str_pad((string) $queue->id, 8, '0', STR_PAD_LEFT),
            'batasWaktu' => $batasWaktu->toIso8601String(),
            'isExpired' => $queue->queue_status === 'skipped',
        ]);
    }

    public function tandaiTiketHangus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'queue_id' => ['required', 'integer'],
        ]);

        /** @var Customer|null $authCustomer */
        $authCustomer = Auth::guard('customer')->user();

        if (!$authCustomer) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi customer tidak ditemukan.',
            ], 401);
        }

        $queueQuery = Queue::query()
            ->where('id', $validated['queue_id'])
            ->where('queue_source', 'online')
            ->where('customer_id', $authCustomer->id);

        $queue = $queueQuery->first();

        if (!$queue) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        if ($queue->queue_status !== 'waiting') {
            return response()->json([
                'success' => true,
                'expired' => $queue->queue_status === 'skipped',
                'status' => $queue->queue_status,
                'message' => 'Status tiket sudah diproses sebelumnya.',
            ]);
        }

        $expiredAt = $queue->created_at
            ? $queue->created_at->copy()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)
            : now()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES);

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
            'status' => 'skipped',
            'message' => 'Tiket berhasil ditandai hangus.',
        ]);
    }

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
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function (Queue $queue) {
                $prefix = strtoupper((string) optional($queue->service)->queue_prefix);

                $colorMap = [
                    'A' => ['bg-blue-600', 'text-blue-600', 'bg-blue-50', 'border-blue-100'],
                    'B' => ['bg-emerald-600', 'text-emerald-600', 'bg-emerald-50', 'border-emerald-100'],
                    'C' => ['bg-amber-500', 'text-amber-600', 'bg-amber-50', 'border-amber-100'],
                ];

                $colors = $colorMap[$prefix] ?? ['bg-gray-600', 'text-gray-600', 'bg-gray-50', 'border-gray-100'];

                $statusMap = [
                    'waiting' => 'Menunggu',
                    'called' => 'Dipanggil',
                    'serving' => 'Dilayani',
                    'completed' => 'Selesai',
                    'skipped' => 'Terlewat',
                ];

                return (object) [
                    'queueId' => $queue->id,
                    'nomor' => $queue->queue_number,
                    'kode' => 'BKG-' . str_pad((string) $queue->id, 8, '0', STR_PAD_LEFT),
                    'layanan' => optional($queue->service)->service_name ?? 'Layanan',
                    'kodeHuruf' => $prefix !== '' ? $prefix : 'Q',
                    'tanggal' => optional($queue->queue_date)->format('d M Y') ?? now()->format('d M Y'),
                    'status' => $queue->queue_status === 'skipped'
                        ? 'Hangus'
                        : ($statusMap[$queue->queue_status] ?? ucfirst((string) $queue->queue_status)),
                    'isExpired' => $queue->queue_status === 'skipped',
                    'batasWaktu' => $queue->created_at
                        ? $queue->created_at->copy()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)->toIso8601String()
                        : now()->addMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES)->toIso8601String(),
                    'warnaBg' => $colors[0],
                    'warnaText' => $colors[1],
                    'warnaLight' => $colors[2],
                    'warnaBorder' => $colors[3],
                ];
            });

        return view('Pages.Remoteuser.Inventory', compact('savedTickets'));
    }

    private function expireStaleOnlineWaitingQueues(): void
    {
        Queue::query()
            ->where('queue_source', 'online')
            ->where('queue_status', 'waiting')
            ->where('created_at', '<=', now()->subMinutes(self::ONLINE_TICKET_EXPIRATION_MINUTES))
            ->update(['queue_status' => 'skipped']);
    }
}

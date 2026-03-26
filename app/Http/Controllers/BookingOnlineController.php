<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Queue;
use App\Models\ServiceCategory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BookingOnlineController extends Controller
{
    private const ONLINE_TICKET_EXPIRATION_MINUTES = 30;

    public function halamanRegister()
    {
        return view('booking.login');
    }

    public function prosesRegister(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required'],
            'whatsapp' => ['required'],
            'g-recaptcha-response' => ['required'],
        ]);

        $captchaSecret = env('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');
        $allowLocalRecaptchaBypass = app()->environment('local') && env('RECAPTCHA_BYPASS_ON_LOCAL', true);

        try {
            $captchaVerify = Http::asForm()->timeout(10)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $captchaSecret,
                'response' => $validated['g-recaptcha-response'],
                'remoteip' => $request->ip(),
            ]);
        } catch (ConnectionException $e) {
            if (!$allowLocalRecaptchaBypass) {
                return back()
                    ->withErrors(['captcha' => 'Gagal terhubung ke server reCAPTCHA. Silakan coba lagi.'])
                    ->withInput();
            }

            $captchaVerify = null;
        }

        $captchaSuccess = $captchaVerify
            ? data_get($captchaVerify->json(), 'success', false)
            : $allowLocalRecaptchaBypass;

        if (!$captchaSuccess) {
            return back()
                ->withErrors(['captcha' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'])
                ->withInput();
        }

        $whatsapp = preg_replace('/\D+/', '', $validated['whatsapp']);

        session([
            'nama' => $validated['nama'],
            'whatsapp' => $whatsapp,
        ]);

        return redirect()->route('booking.dashboard');
    }

    public function halamanDashboard()
    {
        if (request()->hasAny(['nama', 'whatsapp'])) {
            session([
                'nama' => request('nama', session('nama')),
                'whatsapp' => request('whatsapp', session('whatsapp')),
            ]);

            return redirect()->route('booking.dashboard');
        }

        $layanans = ServiceCategory::all();

        return view('booking.dashboard', compact('layanans'));
    }

    public function prosesAmbilAntrean(Request $request)
    {
        $validated = $request->validate([
            'layanan' => ['required', 'string'],
        ]);

        $nama = (string) session('nama', '');
        $whatsapp = preg_replace('/\D+/', '', (string) session('whatsapp', ''));

        if ($nama === '' || $whatsapp === '') {
            return redirect()->route('booking.register')
                ->withErrors(['booking_register' => 'Silakan register terlebih dahulu sebelum mengambil antrean.']);
        }

        $bookingTodayCount = Queue::query()
            ->whereDate('queue_date', now()->toDateString())
            ->where('queue_source', 'online')
            ->whereHas('customer', function ($query) use ($whatsapp) {
                $query->where('phone', $whatsapp);
            })
            ->count();

        if ($bookingTodayCount >= 2) {
            return back()
                ->withErrors(['limit_booking' => 'Limit antrean Anda telah habis. Maksimal 2 kali pengambilan antrean online per hari.'])
                ->withInput();
        }

        $slug = $validated['layanan'];

        if (ServiceCategory::query()->count() === 0) {
            return back()
                ->withErrors(['limit_booking' => 'Data layanan belum tersedia. Hubungi petugas untuk menyiapkan layanan terlebih dahulu.'])
                ->withInput();
        }

        $serviceCategory = ServiceCategory::query()
            ->where('is_active', true)
            ->get()
            ->first(function ($item) use ($slug) {
                return Str::slug($item->category_name) === $slug;
            });

        if (!$serviceCategory) {
            return back()
                ->withErrors(['limit_booking' => 'Layanan tidak ditemukan atau tidak aktif.'])
                ->withInput();
        }

        $customer = Customer::firstOrCreate(
            [
                'instance_id' => $serviceCategory->instance_id,
                'phone' => $whatsapp,
            ],
            [
                'name' => $nama,
            ]
        );

        if ($customer->name !== $nama) {
            $customer->update(['name' => $nama]);
        }

        $today = now()->toDateString();
        $todayQueueSequence = Queue::query()
            ->where('instance_id', $serviceCategory->instance_id)
            ->where('service_category_id', $serviceCategory->id)
            ->whereDate('queue_date', $today)
            ->count() + 1;

        $queuePrefix = $serviceCategory->queue_prefix ?: strtoupper(substr($serviceCategory->category_name, 0, 1));
        $queueNumber = $queuePrefix . '-' . str_pad((string) $todayQueueSequence, 3, '0', STR_PAD_LEFT);

        $queue = Queue::create([
            'instance_id' => $serviceCategory->instance_id,
            'customer_id' => $customer->id,
            'service_category_id' => $serviceCategory->id,
            'queue_number' => $queueNumber,
            'queue_date' => $today,
            'taken_time' => now()->format('H:i:s'),
            'queue_status' => 'waiting',
            'queue_source' => 'online',
        ]);

        session([
            'booking_last_queue_id' => $queue->id,
            'booking_last_queue_number' => $queue->queue_number,
            'booking_last_service_name' => $serviceCategory->category_name,
        ]);

        return redirect()->route('booking.tiket', ['queue_id' => $queue->id]);
    }

    public function halamanTiket(Request $request)
    {
        $this->expireStaleOnlineWaitingQueues();

        $queueId = (int) $request->query('queue_id', session('booking_last_queue_id'));

        if ($queueId <= 0) {
            return redirect()->route('booking.inventory')
                ->withErrors(['ticket_not_found' => 'Tiket tidak ditemukan. Silakan ambil antrean terlebih dahulu.']);
        }

        $whatsapp = preg_replace('/\D+/', '', (string) session('whatsapp', ''));

        $queueQuery = Queue::query()
            ->with(['category', 'customer'])
            ->where('id', $queueId)
            ->where('queue_source', 'online');

        if ($whatsapp !== '') {
            $queueQuery->whereHas('customer', function ($query) use ($whatsapp) {
                $query->where('phone', $whatsapp);
            });
        }

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

        return view('booking.tiket', [
            'queue' => $queue,
            'nama' => (string) optional($queue->customer)->name,
            'whatsapp' => (string) optional($queue->customer)->phone,
            'layanan' => (string) optional($queue->category)->category_name,
            'nomorAntrean' => (string) $queue->queue_number,
            'kodeBooking' => 'BKG-' . str_pad((string) $queue->id, 8, '0', STR_PAD_LEFT),
            'batasWaktu' => $batasWaktu->toIso8601String(),
            'isExpired' => $queue->queue_status === 'skipped',
        ]);
    }

    public function halamanInventory()
    {
        $this->expireStaleOnlineWaitingQueues();

        $whatsapp = preg_replace('/\D+/', '', (string) session('whatsapp', ''));

        $savedTickets = collect();

        if ($whatsapp !== '') {
            $savedTickets = Queue::query()
                ->with(['category', 'customer'])
                ->where('queue_source', 'online')
                ->whereHas('customer', function ($query) use ($whatsapp) {
                    $query->where('phone', $whatsapp);
                })
                ->orderByDesc('created_at')
                ->limit(20)
                ->get()
                ->map(function (Queue $queue) {
                    $prefix = strtoupper((string) optional($queue->category)->queue_prefix);

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
                        'layanan' => optional($queue->category)->category_name ?? 'Layanan',
                        'kodeHuruf' => $prefix !== '' ? $prefix : 'Q',
                        'tanggal' => optional($queue->queue_date)->format('d M Y') ?? now()->format('d M Y'),
                        'status' => $queue->queue_status === 'skipped'
                            ? 'Hangus'
                            : ($statusMap[$queue->queue_status] ?? ucfirst((string) $queue->queue_status)),
                        'isExpired' => $queue->queue_status === 'skipped',
                        'batasWaktu' => $queue->created_at ? $queue->created_at->copy()->addMinutes(30)->toIso8601String() : now()->addMinutes(30)->toIso8601String(),
                        'warnaBg' => $colors[0],
                        'warnaText' => $colors[1],
                        'warnaLight' => $colors[2],
                        'warnaBorder' => $colors[3],
                    ];
                });
        }

        return view('booking.inventory', compact('savedTickets'));
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

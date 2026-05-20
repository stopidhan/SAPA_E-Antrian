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
    public function halamanHome()
    {
        $services = Service::where('instance_id', 1)->where('is_active', true)->get();
        return view('Pages.On-siteUser.KioskHome', compact('services'));
    }

    /**
     * Menampilkan halaman input data (Offline).
     */
    public function halamanInput(Request $request)
    {
        $slug = $request->query('layanan');
        
        $service = Service::where('instance_id', 1)->where('is_active', true)->get()
            ->first(function ($item) use ($slug) {
                return Str::slug($item->service_name) === $slug;
            });

        if (!$service) {
            return redirect()->route('kiosk.home')->withErrors(['layanan' => 'Layanan tidak ditemukan']);
        }

        return view('Pages.On-siteUser.KioskInput', compact('slug', 'service'));
    }

    /**
     * Menyimpan data antrean baru dari Kiosk.
     */
    public function simpanAntreanOffline(Request $request)
    {
        $validated = $request->validate([
            'layanan' => 'required|string',
            'nama' => 'required|string|max:255',
        ]);

        $slug = $validated['layanan'];

        // Cari ID layanan dari database berdasarkan Slug (Asumsi Kiosk Instansi ID = 1)
        $service = Service::where('is_active', true)
            ->where('instance_id', 1)
            ->get()
            ->first(function ($item) use ($slug) {
                return Str::slug($item->service_name) === $slug;
            });

        if (!$service) {
            return back()->withErrors(['layanan' => 'Layanan tidak ditemukan atau sedang tidak aktif.']);
        }

        // Buat atau cari customer guest/offline (bisa dengan nomor hp kosong)
        $customer = Customer::create([
            'instance_id' => $service->instance_id,
            'name' => $validated['nama'] . ' (On-Site)',
            'phone' => '-', // Tidak ada nomor HP untuk registrasi langsung kiosk ini
            'is_verified' => true,
        ]);

        // Generate Nomor Antrean 
        $today = now()->toDateString();
        $lastQueue = Queue::query()
            ->where('service_id', $service->id)
            ->whereDate('queue_date', $today)
            ->lockForUpdate()
            ->latest('id')
            ->first();

        $urutan = $lastQueue ? ((int) substr($lastQueue->queue_number, 2)) + 1 : 1;
        $queueNumber = strtoupper($service->queue_prefix) . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);

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

        return redirect()->route('kiosk.cetak');
    }

    /**
     * Menampilkan halaman cetak struk.
     */
    public function halamanCetak()
    {
        $queueId = session('kiosk_last_queue_id');
        
        if (!$queueId) {
            return redirect()->route('kiosk.home'); // Jika dicoba akses sembarangan
        }

        $queue = Queue::with(['service', 'customer'])->find($queueId);

        return view('Pages.On-siteUser.KioskCetak', [
            'queue' => $queue,
            'layanan' => $queue->service->service_name ?? '-',
            'kode' => $queue->service->queue_prefix ?? '-',
            'nomor' => $queue->queue_number,
            'nama' => str_replace(' (On-Site)', '', $queue->customer->name ?? ''),
            'tanggal' => \Carbon\Carbon::parse($queue->queue_date)->format('d M Y') . ' — ' . \Carbon\Carbon::parse($queue->taken_time)->format('H:i'),
        ]);
    }

    /**
     * Menampilkan halaman Scanner QR Code.
     */
    public function halamanScan()
    {
        return view('Pages.On-siteUser.KioskScan');
    }

    /**
     * Memverifikasi data QR Code yang di-scan.
     */
    public function verifyScan(Request $request): JsonResponse
    {
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

        // Cari antrean yang sesuai
        $queue = Queue::query()
            ->with(['service', 'customer'])
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

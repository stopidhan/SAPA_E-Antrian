<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Events\QueueCheckedIn;
use Illuminate\Http\JsonResponse;

class KioskController extends Controller
{
    /**
     * Menampilkan halaman utama Kiosk.
     */
    public function halamanHome()
    {
        return view('Pages.On-siteUser.KioskHome');
    }

    /**
     * Menampilkan halaman input data (Offline).
     */
    public function halamanInput(Request $request)
    {
        $slug = $request->query('layanan');
        return view('Pages.On-siteUser.KioskInput', compact('slug'));
    }

    /**
     * Menampilkan halaman cetak struk.
     */
    public function halamanCetak()
    {
        return view('Pages.On-siteUser.KioskCetak');
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\ServiceCounter;
use App\Models\Service;

class OperatorController extends Controller
{
    public function index(Request $request)
    {
        // Cari loket yang ditugaskan untuk user ini
        $counter = ServiceCounter::where('user_id', auth()->id())->first();     
        $namaLoket = $counter ? $counter->counter_number : 'Loket Default';     
        $idLoket   = $counter ? $counter->id : null;

        // Ambil daftar layanan yang aktif
        $services = Service::where('is_active', true)->get();

        // Get today's waiting queues
        $queuesData = Queue::with('service')
            ->whereDate('queue_date', today())
            ->where('queue_status', 'waiting')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($q) {
                return [
                    'id'      => $q->id,
                    'nomor'   => $q->queue_number,
                    'layanan' => $q->service ? $q->service->service_name : 'Lainnya',
                    'tipe'    => $q->queue_source ?? 'onsite'
                ];
            });

        return view('Pages.StaffOperatorLoket.Index', compact('queuesData', 'namaLoket', 'idLoket', 'services'));
    }

    public function getQueuesApi(Request $request)
    {
        // Get today's waiting queues in JSON format for polling
        $queuesData = Queue::with('service')
            ->whereDate('queue_date', today())
            ->where('queue_status', 'waiting')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($q) {
                return [
                    'id'      => $q->id,
                    'nomor'   => $q->queue_number,
                    'layanan' => $q->service ? $q->service->service_name : 'Lainnya',
                    'tipe'    => $q->queue_source ?? 'onsite'
                ];
            });

        return response()->json($queuesData);
    }

    public function panggilAntrean(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        $counterId = $request->input('counter_id');

        // Update status menjadi called (dipanggil) dan mencatat waktu panggil
        $queue->update([
            'queue_status'       => 'called',
            'call_time'          => now(),
            'service_counter_id' => $counterId,
        ]);

        event(new \App\Events\QueueUpdated('called'));

        return response()->json([
            'success' => true,
            'message' => 'Status antrean berhasil diperbarui (Dipanggil)'
        ]);
    }

    public function layaniAntrean(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        $counterId = $request->input('counter_id');

        $queue->update([
            'queue_status'       => 'serving', // dilayani
            'service_start_time' => now(),
            'service_counter_id' => $counterId,
        ]);

        event(new \App\Events\QueueUpdated('serving'));

        return response()->json(['success' => true, 'message' => 'Status: Dilayani']);
    }

    public function lewatiAntrean(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);

        $queue->update([
            'queue_status' => 'skipped', // dilewati
        ]);

        event(new \App\Events\QueueUpdated('skipped'));

        return response()->json(['success' => true, 'message' => 'Status: Dilewati']);
    }

    public function selesaiAntrean(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);

        $startTime = $queue->service_start_time ? \Carbon\Carbon::parse($queue->service_start_time) : now();
        $endTime = now();

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
            'service_description' => $deskripsiFinal
        ]);

        event(new \App\Events\QueueUpdated('completed'));

        return response()->json(['success' => true, 'message' => 'Status: Selesai']);
    }
}

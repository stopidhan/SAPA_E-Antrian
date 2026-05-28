<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\ServiceCounter;
use App\Models\Service;

class OperatorController extends Controller
{
    public function index(Request $request, $instance_code)
    {
        $userInstance = auth()->user()->instance;

        // Pastikan user mengakses instance-nya sendiri
        if (!$userInstance || $userInstance->instance_code !== $instance_code) {
            abort(403, 'Unauthorized access to this instance.');
        }

        // Cari loket yang ditugaskan untuk user ini
        $counter = ServiceCounter::where('user_id', auth()->id())->first();     
        $namaLoket = $counter ? $counter->counter_number : 'Loket Default';     
        $idLoket   = $counter ? $counter->id : null;
        $serviceId = $counter ? $counter->service_id : null;

        // Ambil daftar layanan yang aktif sesuai instance user
        $services = Service::where('is_active', true)
            ->where('instance_id', auth()->user()->instance_id)
            ->get();

        // Get today's waiting queues for the user's instance and specific service if assigned
        $queuesQuery = Queue::with('service')
            ->where('instance_id', auth()->user()->instance_id)
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
        $historyData = Queue::with('service')
            ->where('instance_id', auth()->user()->instance_id)
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

        return view('Pages.StaffOperatorLoket.Index', compact('queuesData', 'historyData', 'namaLoket', 'idLoket', 'services'));
    }

    public function getQueuesApi(Request $request, $instance_code)
    {
        $counter = ServiceCounter::where('user_id', auth()->id())->first();
        $serviceId = $counter ? $counter->service_id : null;

        // Get today's waiting queues in JSON format for polling (filtered by instance and service if any)
        $queuesQuery = Queue::with('service')
            ->where('instance_id', auth()->user()->instance_id)
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

        $historyData = Queue::with('service')
            ->where('instance_id', auth()->user()->instance_id)
            ->whereDate('queue_date', today())
            ->whereIn('queue_status', ['completed', 'skipped', 'cancelled'])
            ->where('service_counter_id', $counter ? $counter->id : null)
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

        return response()->json([
            'waiting' => $queuesData,
            'history' => $historyData
        ]);
    }

    public function panggilAntrean(Request $request, $instance_code, $id)
    {
        $queue = Queue::where('instance_id', auth()->user()->instance_id)->findOrFail($id);
        $counterId = $request->input('counter_id');

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
        ], auth()->user()->instance_id));

        return response()->json([
            'success' => true,
            'message' => 'Status antrean berhasil diperbarui (Dipanggil)'
        ]);
    }

    public function layaniAntrean(Request $request, $instance_code, $id)
    {
        $queue = Queue::where('instance_id', auth()->user()->instance_id)->findOrFail($id);
        $counterId = $request->input('counter_id');

        $queue->update([
            'queue_status'       => 'serving', // dilayani
            'service_start_time' => now(),
            'service_counter_id' => $counterId,
        ]);

        event(new \App\Events\QueueUpdated('serving', null, auth()->user()->instance_id));

        return response()->json(['success' => true, 'message' => 'Status: Dilayani']);
    }

    public function lewatiAntrean(Request $request, $instance_code, $id)
    {
        $queue = Queue::where('instance_id', auth()->user()->instance_id)->findOrFail($id);

        $queue->update([
            'queue_status' => 'skipped', // dilewati
        ]);

        event(new \App\Events\QueueUpdated('skipped', null, auth()->user()->instance_id));

        return response()->json(['success' => true, 'message' => 'Status: Dilewati']);
    }

    public function batalkanAntrean(Request $request, $instance_code, $id)
    {
        $queue = Queue::where('instance_id', auth()->user()->instance_id)->findOrFail($id);
        $counterId = $request->input('counter_id');

        $queue->update([
            'queue_status'       => 'cancelled', // dibatalkan karena salah antrean/alasan lain
            'service_counter_id' => $counterId,
            'service_end_time'   => now(),
            'service_description' => 'Dibatalkan oleh operator',
        ]);

        event(new \App\Events\QueueUpdated('cancelled', null, auth()->user()->instance_id));

        return response()->json(['success' => true, 'message' => 'Antrean dibatalkan']);
    }

    public function selesaiAntrean(Request $request, $instance_code, $id)
    {
        $queue = Queue::where('instance_id', auth()->user()->instance_id)->findOrFail($id);

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

        event(new \App\Events\QueueUpdated('completed', null, auth()->user()->instance_id));

        return response()->json(['success' => true, 'message' => 'Status: Selesai']);
    }
}

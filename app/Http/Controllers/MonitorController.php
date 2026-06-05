<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\ServiceCounter;

class MonitorController extends Controller
{
    public function index($instance_code)
    {
        $instance = \App\Models\Instance::where('instance_code', $instance_code)->firstOrFail();
        return view('Pages.MonitorPublic.monitor', compact('instance'));
    }

    public function getMonitorApi($instance_code)
    {
        $instance = \App\Models\Instance::where('instance_code', $instance_code)->firstOrFail();
        $instanceId = $instance->id;

        $currentCall = Queue::with('counter')
            ->whereDate('queue_date', today())
            ->where('instance_id', $instanceId)
            ->whereIn('queue_status', ['called', 'serving'])
            ->orderBy('updated_at', 'desc')
            ->first();

        $counters = ServiceCounter::with(['queues' => function($query) {
            $query->whereDate('queue_date', today())
                  ->whereIn('queue_status', ['called', 'serving'])
                  ->orderBy('updated_at', 'desc');
        }])
        ->where('instance_id', $instanceId)
        ->orderBy('counter_number', 'asc')
        ->get()
        ->map(function ($c) {
            $currentQueue = $c->queues->first();

            $queueNumber = '-';
            $statusName = 'Menunggu';
            $statusBg = 'bg-gray-50';
            $iconBg = 'bg-gray-300';
            
            if ($currentQueue) {
                $queueNumber = $currentQueue->queue_number;
                if ($currentQueue->queue_status === 'called') {
                    $statusName = 'Memanggil';
                    $statusBg = 'bg-blue-50';
                    $iconBg = 'bg-blue-600';
                } elseif ($currentQueue->queue_status === 'serving') {
                    $statusName = 'Dilayani';
                    $statusBg = 'bg-emerald-50';
                    $iconBg = 'bg-emerald-600';
                }
            } else {
                if (!$c->is_active) {
                    $statusName = 'Tutup';
                    $statusBg = 'bg-red-50';
                    $iconBg = 'bg-red-600';
                }
            }

            return [
                'id' => $c->id,
                'counter_number' => $c->counter_number,
                'status' => $statusName,
                'queue_number' => $queueNumber,
                'status_bg' => $statusBg,
                'icon_bg' => $iconBg
            ];
        });

        $activeCounters = ServiceCounter::where('instance_id', $instanceId)->where('is_active', true)->count();
        $totalCounters = ServiceCounter::where('instance_id', $instanceId)->count();

        // Validasi counter saat ini (jika ada) untuk pemicu notifikasi bunyi
        $latestCalledId = $currentCall && $currentCall->queue_status === 'called' ? $currentCall->id : null;

        return response()->json([
            'current_call' => $currentCall ? [
                'queue_number' => $currentCall->queue_number,
                'counter_number' => $currentCall->counter ? $currentCall->counter->counter_number : '-',
                'service_name' => $currentCall->service ? $currentCall->service->service_name : 'Layanan'
            ] : null,
            'latest_called_id' => $latestCalledId,
            'counters' => $counters,
            'counters_stats' => [
                'active' => $activeCounters,
                'total' => $totalCounters
            ],
        ]);
    }
}

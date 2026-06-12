<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\ServiceCounter;

class MonitorController extends Controller
{
    public function index(string $instanceSlug)
    {
        $instance = \App\Models\Instance::where('instance_slug', $instanceSlug)->firstOrFail();
        
        $mediaContents = \App\Models\MediaContent::where('instance_id', $instance->id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($media) {
                return [
                    'type' => $media->media_type,
                    'url' => asset('storage/' . $media->file_path),
                    'duration' => ($media->duration ?? 10) * 1000,
                ];
            });

        return view('Pages.MonitorPublic.monitor', compact('instance', 'mediaContents'));
    }

    public function getMonitorApi(string $instanceSlug)
    {
        $instance = \App\Models\Instance::where('instance_slug', $instanceSlug)->firstOrFail();
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

        // Cari jam operasional hari ini
        $todayName = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
        $jamBuka = '08:00';
        $jamTutup = '16:00';
        $isOpenToday = false;

        if (is_array($instance->operational_hours)) {
            foreach ($instance->operational_hours as $oh) {
                if (strtolower($oh['name']) === strtolower($todayName)) {
                    $jamBuka = $oh['openTime'] ?? '08:00';
                    $jamTutup = $oh['closeTime'] ?? '16:00';
                    $isOpenToday = $oh['isOpen'] ?? false;
                    break;
                }
            }
        }

        $now = \Carbon\Carbon::now()->format('H:i');
        $isCurrentlyOpen = $instance->is_active && $isOpenToday && ($now >= $jamBuka && $now <= $jamTutup);

        // Validasi counter saat ini (jika ada) untuk pemicu notifikasi bunyi
        $latestCalledId = $currentCall && $currentCall->queue_status === 'called' ? $currentCall->id : null;

        $mediaContents = \App\Models\MediaContent::where('instance_id', $instanceId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($media) {
                return [
                    'type' => $media->media_type,
                    'url' => asset('storage/' . $media->file_path),
                    'duration' => ($media->duration ?? 10) * 1000,
                ];
            });

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
            'operational_info' => [
                'jam_operasional' => $isOpenToday ? $jamBuka . ' - ' . $jamTutup : 'Libur',
                'status' => $isCurrentlyOpen ? 'Operasional' : 'Tutup',
                'is_open' => $isCurrentlyOpen
            ],
            'media_contents' => $mediaContents
        ]);
    }
}

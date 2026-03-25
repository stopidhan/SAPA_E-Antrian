<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display the report page.
     */
    public function index(Request $request)
    {
        // Get current user's instance
        $instance = Auth::user()->instance;

        // Get today's queues (or filter date if provided)
        $today = $request->query('date', date('Y-m-d'));

        $queuesQuery = Queue::where('instance_id', $instance->id)
            ->where('queue_date', $today);

        // Get statistics
        $totalQueue = $queuesQuery->count();
        $completedQueue = (clone $queuesQuery)->where('queue_status', 'completed')->count();
        $completionRate = $totalQueue > 0 ? round(($completedQueue / $totalQueue) * 100) : 0;

        // Average service duration in minutes
        $avgServiceTime = $totalQueue > 0
            ? round((clone $queuesQuery)->whereNotNull('service_duration')->avg('service_duration') / 60, 1)
            : 0;

        $waitingQueue = (clone $queuesQuery)->where('queue_status', 'waiting')->count();
        $servingQueue = (clone $queuesQuery)->where('queue_status', 'serving')->count();

        // Get queue data for table display
        $queueData = Queue::where('instance_id', $instance->id)
            ->where('queue_date', $today)
            ->with(['service', 'customer', 'counter.user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($queue) {
                return (object) [
                    'queue_number' => $queue->queue_number,
                    'service_name' => $queue->service->name ?? '-',
                    'registration_type' => $queue->queue_source,
                    'registered_at' => $queue->created_at->format('Y-m-d H:i:s'),
                    'completed_at' => $queue->service_end_time ? date('Y-m-d H:i:s', strtotime($queue->queue_date . ' ' . $queue->service_end_time)) : null,
                    'service_time' => $queue->service_duration ? round($queue->service_duration / 60) : null,
                    'operator_name' => $queue->logs()->latest('action_time')->first()?->user?->name ?? '-',
                    'status' => $queue->queue_status,
                ];
            });

        // Prepare stat cards
        $statCards = [
            [
                'label' => 'Total Antrean',
                'value' => $totalQueue,
                'color' => 'text-gray-800',
                'sub' => $this->getPercentageChange('total_queue', $totalQueue),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
            ],
            [
                'label' => 'Selesai Dilayani',
                'value' => $completedQueue,
                'color' => 'text-green-600',
                'sub' => $completionRate . '% dari total',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            ],
            [
                'label' => 'Rata-rata Waktu',
                'value' => number_format($avgServiceTime, 1) . ' mnt',
                'color' => 'text-blue-600',
                'sub' => 'Per pelayanan',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            ],
            [
                'label' => 'Sedang Menunggu',
                'value' => $waitingQueue,
                'color' => 'text-orange-600',
                'sub' => $servingQueue . ' sedang dilayani',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>',
            ],
        ];

        return view('Pages.AdminInstansi.report', [
            'statCards' => $statCards,
            'queueData' => $queueData,
            'totalQueue' => $totalQueue,
            'completedQueue' => $completedQueue,
            'completionRate' => $completionRate,
            'avgServiceTime' => $avgServiceTime,
            'waitingQueue' => $waitingQueue,
            'servingQueue' => $servingQueue,
            'today' => $today,
        ]);
    }

    /**
     * Helper to calculate percentage change (for future implementation)
     */
    private function getPercentageChange($key, $currentValue)
    {
        // This can be enhanced to compare with yesterday's data
        return '+0% dari kemarin';
    }
}

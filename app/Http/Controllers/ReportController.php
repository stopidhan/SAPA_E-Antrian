<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $auth = Auth::user();
        $instance = $auth->instance;

        // Get all queues (unfiltered for statistics)
        $allQueues = Queue::where('instance_id', $instance->id)->get();

        // Calculate statistics
        $totalQueue = $allQueues->count();
        $completedQueue = $allQueues->where('queue_status', 'completed')->count();
        $completionRate = $totalQueue > 0 ? round(($completedQueue / $totalQueue) * 100) : 0;
        $avgServiceTime = $allQueues->avg('service_duration') ?? 0;
        $waitingQueue = $allQueues->where('queue_status', 'waiting')->count();
        $servingQueue = $allQueues->where('queue_status', 'serving')->count();

        // Calculate growth percentage (comparison with yesterday)
        $yesterday = $allQueues->where('queue_date', now()->subDay()->format('Y-m-d'))->count();
        $growth = $yesterday > 0 ? round((($totalQueue - $yesterday) / $yesterday) * 100) : 0;

        // Get paginated queues for table display
        $queues = Queue::where('instance_id', $instance->id)
            ->with(['service', 'counter.user', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $queues->getCollection()->transform(function ($queue) {
            return (object) [
                'queue_number' => $queue->queue_number,
                'service_name' => $queue->service->service_name,
                'registration_type' => $queue->queue_source,
                'start_at' => $queue->service_start_time,
                'completed_at' => $queue->service_end_time,
                'service_time' => $queue->service_duration,
                'operator_name' => $queue->counter?->user?->name ?? '-',
                'status' => $queue->queue_status,
            ];
        });

        // Get services & operators for filter
        $services = Service::where('instance_id', $instance->id)->get();
        $serviceOptions = [['value' => 'all', 'label' => 'Semua Layanan']];
        foreach ($services as $service) {
            $serviceOptions[] = ['value' => $service->id, 'label' => $service->service_name];
        }

        // Get operators for filter
        $operators = User::where('instance_id', $instance->id)->where('role', 'staff_operator')->get();
        $operatorOptions = [['value' => 'all', 'label' => 'Semua Operator']];
        foreach ($operators as $operator) {
            $operatorOptions[] = ['value' => $operator->id, 'label' => $operator->name];
        }

        return view('Pages.AdminInstansi.report', [
            'queueData' => $queues,
            'serviceOptions' => $serviceOptions,
            'operatorOptions' => $operatorOptions,
            'totalQueue' => $totalQueue,
            'completedQueue' => $completedQueue,
            'completionRate' => $completionRate,
            'avgServiceTime' => $avgServiceTime,
            'waitingQueue' => $waitingQueue,
            'servingQueue' => $servingQueue,
            'growth' => $growth,
        ]);
    }
}

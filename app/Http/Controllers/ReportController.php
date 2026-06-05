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
    private function getFilteredQueues(Request $request, $instance)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $serviceId = $request->input('service_id', 'all');
        $operatorId = $request->input('operator', 'all');

        $queryBase = Queue::where('instance_id', $instance->id)
            ->with(['service', 'counter.user', 'customer']);

        if ($startDate) {
            $queryBase->where('queue_date', '>=', $startDate);
        }
        if ($endDate) {
            $queryBase->where('queue_date', '<=', $endDate);
        }
        if ($serviceId !== 'all') {
            $queryBase->where('service_id', $serviceId);
        }
        if ($operatorId !== 'all') {
            $queryBase->whereHas('counter', function ($q) use ($operatorId) {
                $q->where('user_id', $operatorId);
            });
        }

        return $queryBase;
    }

    private function getStatistics($allQueues, Request $request, $instance)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $serviceId = $request->input('service_id', 'all');
        $operatorId = $request->input('operator', 'all');

        $totalQueue = $allQueues->count();
        $completedQueue = $allQueues->where('queue_status', 'completed')->count();
        $completionRate = $totalQueue > 0 ? round(($completedQueue / $totalQueue) * 100) : 0;

        // Menghitung antrean yang batal atau dilewati
        $cancelledQueue = $allQueues->whereIn('queue_status', ['cancelled', 'skipped'])->count();

        // Rata-rata waktu pelayanan (dari database)
        $avgServiceTime = $allQueues->avg('service_duration') ?? 0;

        // Menghitung Rata-rata Waktu Tunggu (Waktu dilayani - Waktu cetak tiket)
        $avgWaitTime = $allQueues->whereNotNull('service_start_time')->map(function ($q) {
            return \Carbon\Carbon::parse($q->created_at)->diffInMinutes(\Carbon\Carbon::parse($q->service_start_time));
        })->avg() ?? 0;

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $diffDays = $start->diffInDays($end) + 1;

        $yesterdayQuery = Queue::where('instance_id', $instance->id);
        if ($serviceId !== 'all') {
            $yesterdayQuery->where('service_id', $serviceId);
        }
        if ($operatorId !== 'all') {
            $yesterdayQuery->whereHas('counter', function ($q) use ($operatorId) {
                $q->where('user_id', $operatorId);
            });
        }
        $yesterdayQuery->whereBetween('queue_date', [
            $start->copy()->subDays($diffDays)->format('Y-m-d'),
            $end->copy()->subDays($diffDays)->format('Y-m-d')
        ]);

        $yesterdayCount = $yesterdayQuery->count();
        $growth = $yesterdayCount > 0 ? round((($totalQueue - $yesterdayCount) / $yesterdayCount) * 100) : 0;
        if ($yesterdayCount == 0 && $totalQueue > 0) {
            $growth = 100;
        }

        return compact(
            'totalQueue',
            'completedQueue',
            'completionRate',
            'cancelledQueue',
            'avgServiceTime',
            'avgWaitTime',
            'growth'
        );
    }

    public function index(Request $request)
    {
        $auth = Auth::user();
        $instance = $auth->instance;

        // Get filters for growth calculation
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $serviceId = $request->input('service_id', 'all');
        $operatorId = $request->input('operator', 'all');

        $queryBase = $this->getFilteredQueues($request, $instance);

        // Get all queues for statistics and charts
        $allQueues = (clone $queryBase)->get();

        // Calculate statistics
        $stats = $this->getStatistics($allQueues, $request, $instance);

        // Prepare Chart Data
        $chartServiceLabels = [];
        $chartServiceData = [];
        foreach ($allQueues->groupBy('service_id') as $group) {
            $chartServiceLabels[] = $group->first()->service->service_name ?? 'Unknown';
            $chartServiceData[] = $group->count();
        }

        $chartHourlyLabels = [];
        $chartHourlyData = [];
        $hourlyGrouped = $allQueues->groupBy(function ($q) {
            return \Carbon\Carbon::parse($q->created_at)->format('H:00');
        });
        $hourlyGrouped = $hourlyGrouped->sortBy(function ($item, $key) {
            return $key;
        });
        foreach ($hourlyGrouped as $hour => $group) {
            $chartHourlyLabels[] = $hour;
            $chartHourlyData[] = $group->count();
        }

        $chartRegTypeLabels = [];
        $chartRegTypeData = [];
        foreach ($allQueues->groupBy('queue_source') as $type => $group) {
            $chartRegTypeLabels[] = ucfirst($type);
            $chartRegTypeData[] = $group->count();
        }

        $chartData = [
            'service' => ['labels' => $chartServiceLabels, 'data' => $chartServiceData],
            'hourly' => ['labels' => $chartHourlyLabels, 'data' => $chartHourlyData],
            'regType' => ['labels' => $chartRegTypeLabels, 'data' => $chartRegTypeData],
        ];

        // Get paginated queues for table display
        $queues = (clone $queryBase)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $queues->getCollection()->transform(function ($queue) {
            return (object) [
                'id' => $queue->id, // Add ID for the modal
                'queue_number' => $queue->queue_number,
                'service_name' => $queue->service->service_name ?? '-',
                'registration_type' => $queue->queue_source,
                'start_at' => $queue->service_start_time,
                'completed_at' => $queue->service_end_time,
                'service_time' => $queue->service_duration,
                'operator_name' => $queue->counter?->user?->name ?? '-',
                'status' => $queue->queue_status,
                // Additional data for detail modal
                'customer_name' => $queue->customer->name ?? '-',
                'customer_phone' => $queue->customer->phone ?? '-',
                'created_at' => $queue->created_at,
                'photos' => $queue->photos->map(function ($photo) {
                    $path = $photo->photo_path;
                    return str_starts_with($path, 'http') ? $path : asset('storage/' . $path);
                })->toArray(),
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

        return view('Pages.AdminInstansi.report', array_merge([
            'queueData' => $queues,
            'serviceOptions' => $serviceOptions,
            'operatorOptions' => $operatorOptions,
            'chartData' => $chartData,
        ], $stats));
    }

    public function exportPdf(Request $request)
    {
        $auth = Auth::user();
        $instance = $auth->instance;

        $queryBase = $this->getFilteredQueues($request, $instance);
        $queues = $queryBase->orderBy('created_at', 'asc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Pages.AdminInstansi.export_table', [
            'queues' => $queues,
            'stats' => $this->getStatistics($queues, $request, $instance)
        ]);

        return $pdf->download('laporan-antrean-' . date('Y-m-d-His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $auth = Auth::user();
        $instance = $auth->instance;

        $queryBase = $this->getFilteredQueues($request, $instance);
        $queues = $queryBase->orderBy('created_at', 'asc')->get();

        $stats = $this->getStatistics($queues, $request, $instance);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\QueueExport($queues, $stats), 'laporan-antrean-' . date('Y-m-d-His') . '.xlsx');
    }
}

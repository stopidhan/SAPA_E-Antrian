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
        $counterId = $request->input('counter_id', 'all');
        $source = $request->input('source', 'all');
        $search = $request->input('search');

        $queryBase = Queue::where('instance_id', $instance->id)
            ->with(['service', 'counter', 'user', 'customer']);

        if ($search) {
            $queryBase->where(function ($q) use ($search) {
                $q->where('queue_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }
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
            $queryBase->where('user_id', $operatorId);
        }
        if ($counterId && $counterId !== 'all') {
            $queryBase->where('service_counter_id', $counterId);
        }
        if ($source && $source !== 'all') {
            $queryBase->where('queue_source', $source);
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

        // Menghitung Rata-rata Waktu Tunggu yang Akurat
        $avgWaitTime = $allQueues->whereNotNull('service_start_time')
            ->filter(function ($q) {
                // Abaikan antrean online yang anomali (tidak punya check_in_time tapi dilayani)
                if ($q->queue_source === 'online' && empty($q->check_in_time)) {
                    return false;
                }
                return true;
            })
            ->map(function ($q) {
                // Tentukan waktu mulai mengantre berdasarkan sumber antrean
                if ($q->queue_source === 'online' && !empty($q->check_in_time)) {
                    $startWaitingAt = \Carbon\Carbon::parse($q->check_in_time);
                } else {
                    $startWaitingAt = \Carbon\Carbon::parse($q->taken_time);
                }

                $servedAt = \Carbon\Carbon::parse($q->service_start_time);

                // Pastikan tidak minus (jika ada anomali data)
                $diff = $startWaitingAt->diffInMinutes($servedAt);
                return $diff > 0 ? $diff : 0;
            })->avg() ?? 0;

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $diffDays = $start->diffInDays($end) + 1;

        $yesterdayQuery = Queue::where('instance_id', $instance->id);
        if ($serviceId !== 'all') {
            $yesterdayQuery->where('service_id', $serviceId);
        }
        if ($operatorId !== 'all') {
            $yesterdayQuery->where('user_id', $operatorId);
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
        $instance = app(\App\Services\TenantManager::class)->getInstance();

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
            return \Carbon\Carbon::parse($q->taken_time)->format('H:00');
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
                'id' => $queue->id,
                'queue_number' => $queue->queue_number,
                'customer_name' => $queue->customer->name ?? '-',
                'queue_source' => $queue->queue_source,
                'service_name' => $queue->service->service_name ?? '-',
                'service_category' => $queue->service->description ?? '-',
                'description' => $queue->service_description,
                'status' => $queue->queue_status,
                'completed_at' => $queue->service_end_time
                    ? \Carbon\Carbon::parse($queue->queue_date . ' ' . $queue->service_end_time)->format('Y-m-d H:i:s')
                    : null,
                'started_at' => $queue->service_start_time
                    ? \Carbon\Carbon::parse($queue->queue_date . ' ' . $queue->service_start_time)->format('Y-m-d H:i:s')
                    : null,
                'taken_at' => $queue->taken_time
                    ? \Carbon\Carbon::parse($queue->queue_date . ' ' . $queue->taken_time)->translatedFormat('d M Y, H:i')
                    : \Carbon\Carbon::parse($queue->queue_date)->translatedFormat('d M Y'),
                'counter_id' => $queue->service_counter_id,
                'counter_name' => $queue->counter
                    ? 'Loket ' . $queue->counter->counter_number
                    : '-',
                'operator_name' => $queue->user?->name ?? '-',
                'photo_path' => $queue->photos->isNotEmpty()
                    ? $queue->photos->first()->photo_path
                    : null,
                'photos' => $queue->photos->map(function ($photo) {
                    $path = $photo->photo_path;
                    return str_starts_with($path, 'http') ? $path : asset('uploads/' . $path);
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

        $counterOptions = \App\Models\ServiceCounter::where('instance_id', $instance->id)
            ->get(['id', 'counter_number'])
            ->map(fn($c) => ['value' => (string)$c->id, 'label' => "Loket $c->counter_number"])
            ->prepend(['value' => 'all', 'label' => 'Semua Loket'])
            ->toArray();

        $sourceOptions = [
            ['value' => 'all', 'label' => 'Semua Sumber'],
            ['value' => 'online', 'label' => 'Online'],
            ['value' => 'onsite', 'label' => 'Onsite'],
        ];

        return view('Pages.AdminInstansi.report', array_merge([
            'queueData' => $queues,
            'serviceOptions' => $serviceOptions,
            'operatorOptions' => $operatorOptions,
            'counterOptions' => $counterOptions,
            'sourceOptions' => $sourceOptions,
            'chartData' => $chartData,
        ], $stats));
    }

    public function queueDetail(string $instance_slug, Queue $queue)
    {
        $auth = Auth::user();
        $instance = app(\App\Services\TenantManager::class)->getInstance();

        // Ensure the queue belongs to this instance
        if ($queue->instance_id !== $instance->id) {
            abort(403, 'Unauthorized');
        }

        $queue->load(['service', 'counter', 'user', 'customer', 'photos']);

        return response()->json([
            'id' => $queue->id,
            'queue_number' => $queue->queue_number,
            'service_name' => $queue->service->service_name ?? '-',
            'service_category' => $queue->service->description ?? '-',
            'customer_name' => $queue->customer->name ?? '-',
            'customer_phone' => $queue->customer->phone ?? '-',
            'queue_source' => $queue->queue_source,
            'queue_date' => $queue->queue_date,
            'taken_time' => $queue->taken_time,
            'call_time' => $queue->call_time,
            'service_start_time' => $queue->service_start_time,
            'service_end_time' => $queue->service_end_time,
            'service_duration' => $queue->service_duration,
            'service_description' => $queue->service_description,
            'queue_status' => $queue->queue_status,
            'counter_name' => $queue->counter
                ? 'Loket ' . $queue->counter->counter_number
                : '-',
            'operator_name' => $queue->user?->name ?? '-',
            'photos' => $queue->photos->map(function ($photo) {
                $path = $photo->photo_path;
                return str_starts_with($path, 'http') ? $path : asset('uploads/' . $path);
            })->toArray(),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $auth = Auth::user();
        $instance = app(\App\Services\TenantManager::class)->getInstance();

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
        $instance = app(\App\Services\TenantManager::class)->getInstance();

        $queryBase = $this->getFilteredQueues($request, $instance);
        $queues = $queryBase->orderBy('created_at', 'asc')->get();

        $stats = $this->getStatistics($queues, $request, $instance);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\QueueExport($queues, $stats), 'laporan-antrean-' . date('Y-m-d-His') . '.xlsx');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service;
use App\Models\ServiceCounter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperVisorController extends Controller
{
    /**
     * Get the authenticated user's instance, or abort if not found.
     */
    private function getInstance()
    {
        $user = Auth::user();
        $instance = app(\App\Services\TenantManager::class)->getInstance();

        if (!$instance) {
            abort(403, 'Anda tidak terdaftar di instansi manapun.');
        }

        return $instance;
    }

    /**
     * Compute stat cards data for today.
     */
    private function getStatCards($instanceId, $date = null)
    {
        $date = $date ?? today()->toDateString();

        $baseQuery = Queue::where('instance_id', $instanceId)
            ->where('queue_date', $date);

        $total = (clone $baseQuery)->count();
        $completed = (clone $baseQuery)->where('queue_status', 'completed')->count();
        $serving = (clone $baseQuery)->where('queue_status', 'serving')->count();
        $waiting = (clone $baseQuery)->where('queue_status', 'waiting')->count();

        // Average service duration in minutes for completed queues
        $avgDuration = (clone $baseQuery)
            ->where('queue_status', 'completed')
            ->whereNotNull('service_duration')
            ->avg('service_duration');

        $avgFormatted = $avgDuration !== null ? round($avgDuration) . 'm' : '0m';

        return [
            [
                'label' => 'Total Antrean',
                'value' => $total,
                'color' => 'text-gray-800',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM15 20H9m6 0h6M9 20H3" /></svg>',
            ],
            [
                'label' => 'Selesai',
                'value' => $completed,
                'color' => 'text-green-600',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            ],
            [
                'label' => 'Sedang Dilayani',
                'value' => $serving,
                'color' => 'text-blue-600',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>',
            ],
            [
                'label' => 'Menunggu',
                'value' => $waiting,
                'color' => 'text-orange-600',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            ],
            [
                'label' => 'Avg. Waktu',
                'value' => $avgFormatted,
                'color' => 'text-purple-600',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3l-4 4m0 4l4 4v-7m8-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            ],
        ];
    }

    /**
     * Get operator performance data for all active counters.
     */
    private function getOperatorPerformance($instanceId, $date = null)
    {
        $date = $date ?? today()->toDateString();

        $operators = User::where('instance_id', $instanceId)
            ->where('role', 'staff_operator')
            ->get();

        $performance = [];

        foreach ($operators as $operator) {
            // All completed queues for this operator (all time)
            $totalServed = Queue::where('user_id', $operator->id)
                ->where('queue_status', 'completed')
                ->count();

            // Today's completed queues
            $todayServed = Queue::where('user_id', $operator->id)
                ->where('queue_status', 'completed')
                ->where('queue_date', $date)
                ->count();

            // Average service time for this operator's completed queues
            $avgServiceTime = Queue::where('user_id', $operator->id)
                ->where('queue_status', 'completed')
                ->whereNotNull('service_duration')
                ->avg('service_duration') ?? 0;

            // Service time distribution (based on service_duration in minutes)
            $fastServices = Queue::where('user_id', $operator->id)
                ->where('queue_status', 'completed')
                ->whereNotNull('service_duration')
                ->where('service_duration', '<=', 2)
                ->count();

            $mediumServices = Queue::where('user_id', $operator->id)
                ->where('queue_status', 'completed')
                ->whereNotNull('service_duration')
                ->whereBetween('service_duration', [3, 5])
                ->count();

            $slowServices = Queue::where('user_id', $operator->id)
                ->where('queue_status', 'completed')
                ->whereNotNull('service_duration')
                ->where('service_duration', '>=', 6)
                ->count();

            // Find current active session to get the counter name, or N/A
            $session = $operator->activeCounterSession;
            $counterName = $session ? 'Loket ' . $session->counter->counter_number : 'Offline';

            // Only include if they have served something today or are currently online
            if ($todayServed > 0 || $session) {
                $performance[] = (object) [
                    'counter_name' => $counterName,
                    'operator_name' => $operator->name,
                    'avg_service_time' => round($avgServiceTime, 1),
                    'total_served' => $totalServed,
                    'today_served' => $todayServed,
                    'fast_services' => $fastServices,
                    'medium_services' => $mediumServices,
                    'slow_services' => $slowServices,
                ];
            }
        }

        return collect($performance)->sortByDesc('today_served')->values()->all();
    }

    /**
     * Get real-time counter statuses.
     */
    private function getCounterStatuses($instanceId)
    {
        $counters = ServiceCounter::where('instance_id', $instanceId)
            ->where('is_active', true)
            ->with(['currentSession.user'])
            ->get();

        $statuses = [];

        foreach ($counters as $counter) {
            // Find the currently active queue for this counter (called or serving)
            $activeQueue = Queue::where('service_counter_id', $counter->id)
                ->whereDate('queue_date', today())
                ->whereIn('queue_status', ['called', 'serving'])
                ->orderBy('updated_at', 'desc')
                ->first();

            $status = 'idle';
            $currentQueue = null;

            if ($activeQueue) {
                $status = $activeQueue->queue_status === 'serving' ? 'serving' : 'calling';
                $currentQueue = $activeQueue->queue_number;
            } elseif (!$counter->currentSession) {
                $status = 'offline';
            }

            $statuses[] = (object) [
                'name' => 'Loket ' . $counter->counter_number,
                'operatorName' => $counter->currentSession->user->name ?? null,
                'status' => $status,
                'current_queue' => $currentQueue,
            ];
        }

        return $statuses;
    }

    /**
     * Get registration type distribution (online vs onsite).
     */
    private function getRegistrationTypes($instanceId, $date = null)
    {
        $date = $date ?? today()->toDateString();

        $online = Queue::where('instance_id', $instanceId)
            ->where('queue_date', $date)
            ->where('queue_source', 'online')
            ->count();

        $onsite = Queue::where('instance_id', $instanceId)
            ->where('queue_date', $date)
            ->where('queue_source', 'onsite')
            ->count();

        return ['online' => $online, 'onsite' => $onsite];
    }

    /**
     * Get analytics chart data.
     */
    private function getChartData($instanceId, $date = null)
    {
        $date = $date ?? today()->toDateString();

        $queues = Queue::where('instance_id', $instanceId)
            ->where('queue_date', $date)
            ->with('service')
            ->get();

        // Service distribution: completed vs waiting per service
        $services = $queues->groupBy('service_id');
        $serviceData = [];
        foreach ($services as $serviceId => $group) {
            $serviceName = $group->first()->service->service_name ?? 'Unknown';
            $serviceData[] = [
                'name' => $serviceName,
                'completed' => $group->where('queue_status', 'completed')->count(),
                'waiting' => $group->where('queue_status', 'waiting')->count(),
            ];
        }

        // Hourly trend
        $hourlyGrouped = $queues->groupBy(function ($q) {
            return Carbon::parse($q->taken_time)->format('H:00');
        })->sortKeys();

        $hourlyData = [];
        foreach ($hourlyGrouped as $hour => $group) {
            $hourlyData[] = [
                'hour' => $hour,
                'count' => $group->count(),
            ];
        }

        return [
            'service' => $serviceData,
            'hourly' => $hourlyData,
        ];
    }

    /**
     * Get filtered completed queues for history.
     */
    private function getFilteredHistory(Request $request, $instanceId)
    {
        return Queue::where('instance_id', $instanceId)
            ->where('queue_status', 'completed')
            ->with(['service', 'counter.user', 'photos', 'customer'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('queue_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->start_date, function ($query, $date) {
                $query->whereDate('queue_date', '>=', $date);
            })
            ->when($request->end_date, function ($query, $date) {
                $query->whereDate('queue_date', '<=', $date);
            })
            ->when($request->service_id && $request->service_id !== 'all', function ($query) use ($request) {
                $query->where('service_id', $request->service_id);
            })
            ->when($request->operator && $request->operator !== 'all', function ($query) use ($request) {
                $query->whereHas('counter', function ($q) use ($request) {
                    $q->where('user_id', $request->operator);
                });
            })
            ->when($request->counter_id && $request->counter_id !== 'all', function ($query) use ($request) {
                $query->where('service_counter_id', $request->counter_id);
            })
            ->when($request->source && $request->source !== 'all', function ($query) use ($request) {
                $query->where('queue_source', $request->source);
            })
            ->orderBy('queue_date', 'desc')
            ->orderBy('service_end_time', 'desc');
    }

    /**
     * Main dashboard page.
     */
    public function index(Request $request)
    {
        $instance = $this->getInstance();
        $instanceId = $instance->id;

        // Stat cards
        $statCards = $this->getStatCards($instanceId);

        // Operator performance
        $operatorPerformance = $this->getOperatorPerformance($instanceId);

        // Counter statuses (real-time)
        $counters = $this->getCounterStatuses($instanceId);

        // Registration type distribution
        $registrationTypes = $this->getRegistrationTypes($instanceId);

        // Chart data for analytics tab
        $chartData = $this->getChartData($instanceId);

        // Filter Options for History Tab
        $serviceOptions = Service::where('instance_id', $instanceId)
            ->get(['id', 'service_name'])
            ->map(fn($s) => ['value' => (string)$s->id, 'label' => $s->service_name])
            ->prepend(['value' => 'all', 'label' => 'Semua Layanan'])
            ->toArray();

        $operatorOptions = User::where('instance_id', $instanceId)
            ->where('role', 'staff_operator')
            ->get(['id', 'name'])
            ->map(fn($u) => ['value' => (string)$u->id, 'label' => $u->name])
            ->prepend(['value' => 'all', 'label' => 'Semua Operator'])
            ->toArray();

        $counterOptions = ServiceCounter::where('instance_id', $instanceId)
            ->get(['id', 'counter_number'])
            ->map(fn($c) => ['value' => (string)$c->id, 'label' => "Loket $c->counter_number"])
            ->prepend(['value' => 'all', 'label' => 'Semua Loket'])
            ->toArray();

        $sourceOptions = [
            ['value' => 'all', 'label' => 'Semua Sumber'],
            ['value' => 'online', 'label' => 'Online'],
            ['value' => 'onsite', 'label' => 'Onsite'],
        ];

        // History tab: filtered & paginated completed queues
        $completedQueues = $this->getFilteredHistory($request, $instanceId)
            ->paginate(10)
            ->withQueryString();

        // Transform for the view
        $completedQueues->getCollection()->transform(function ($queue) {
            return (object) [
                'id' => $queue->id,
                'queue_number' => $queue->queue_number,
                'customer_name' => $queue->customer->name ?? '-',
                'queue_source' => $queue->queue_source,
                'service_name' => $queue->service->service_name ?? '-',
                'service_category' => $queue->service->description ?? '-',
                'description' => $queue->service_description,
                'completed_at' => $queue->service_end_time
                    ? Carbon::parse($queue->queue_date . ' ' . $queue->service_end_time)->format('Y-m-d H:i:s')
                    : null,
                'started_at' => $queue->service_start_time
                    ? Carbon::parse($queue->queue_date . ' ' . $queue->service_start_time)->format('Y-m-d H:i:s')
                    : null,
                'taken_at' => $queue->taken_time
                    ? Carbon::parse($queue->queue_date . ' ' . $queue->taken_time)->translatedFormat('d M Y, H:i')
                    : Carbon::parse($queue->queue_date)->translatedFormat('d M Y'),
                'counter_id' => $queue->service_counter_id,
                'counter_name' => $queue->counter
                    ? 'Loket ' . $queue->counter->counter_number
                    : '-',
                'operator_name' => $queue->counter?->user?->name ?? '-',
                'photo_path' => $queue->photos->isNotEmpty()
                    ? $queue->photos->first()->photo_path
                    : null,
                'photos' => $queue->photos->map(function ($photo) {
                    $path = $photo->photo_path;
                    return str_starts_with($path, 'http') ? $path : asset('uploads/' . $path);
                })->toArray(),
            ];
        });

        return view('Pages.KepalaLayanan.superVisor', compact(
            'statCards',
            'operatorPerformance',
            'counters',
            'registrationTypes',
            'chartData',
            'completedQueues',
            'serviceOptions',
            'operatorOptions',
            'counterOptions',
            'sourceOptions'
        ));
    }

    /**
     * AJAX endpoint for live tracking data (polled every 10s).
     */
    public function liveApi(Request $request)
    {
        $instance = $this->getInstance();
        $instanceId = $instance->id;

        return response()->json([
            'statCards' => $this->getStatCards($instanceId),
            'operatorPerformance' => $this->getOperatorPerformance($instanceId),
            'counters' => $this->getCounterStatuses($instanceId),
            'registrationTypes' => $this->getRegistrationTypes($instanceId),
        ]);
    }

    /**
     * AJAX endpoint for queue detail (for history detail modal).
     */
    public function queueDetail(string $instanceSlug, Queue $queue)
    {
        $instance = $this->getInstance();

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

    /**
     * Export history data as PDF.
     */
    public function exportHistoryPdf(Request $request)
    {
        $instance = $this->getInstance();

        $queues = $this->getFilteredHistory($request, $instance->id)->get();

        $stats = [
            'totalQueue' => $queues->count(),
            'completedQueue' => $queues->count(),
            'avgServiceTime' => $queues->avg('service_duration') ?? 0,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Pages.AdminInstansi.export_table', [
            'queues' => $queues,
            'stats' => $stats,
        ]);

        return $pdf->download('supervisor-riwayat-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export history data as Excel.
     */
    public function exportHistoryExcel(Request $request)
    {
        $instance = $this->getInstance();

        $queues = $this->getFilteredHistory($request, $instance->id)->get();

        $stats = [
            'totalQueue' => $queues->count(),
            'completedQueue' => $queues->count(),
            'avgServiceTime' => $queues->avg('service_duration') ?? 0,
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\QueueExport($queues, $stats),
            'supervisor-riwayat-' . date('Y-m-d-His') . '.xlsx'
        );
    }

    /**
     * Export live tracking data as PDF.
     */
    public function exportLivePdf(Request $request)
    {
        $instance = $this->getInstance();
        $instanceId = $instance->id;

        $operatorPerformance = $this->getOperatorPerformance($instanceId);
        $counters = $this->getCounterStatuses($instanceId);
        $statCards = $this->getStatCards($instanceId);
        $registrationTypes = $this->getRegistrationTypes($instanceId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Pages.KepalaLayanan.export_live', [
            'operatorPerformance' => $operatorPerformance,
            'counters' => $counters,
            'statCards' => $statCards,
            'registrationTypes' => $registrationTypes,
            'instance' => $instance,
            'date' => today()->translatedFormat('d F Y'),
        ]);

        return $pdf->download('supervisor-live-tracking-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export live tracking data as Excel.
     */
    public function exportLiveExcel(Request $request)
    {
        $instance = $this->getInstance();
        $instanceId = $instance->id;

        $operatorPerformance = $this->getOperatorPerformance($instanceId);
        $statCards = $this->getStatCards($instanceId);

        // Build a simple collection for export
        $rows = collect($operatorPerformance)->map(function ($perf) {
            return (object) [
                'queue_number' => $perf->counter_name,
                'service' => (object) ['service_name' => $perf->operator_name],
                'counter' => (object) ['user' => (object) ['name' => $perf->operator_name]],
                'queue_source' => '-',
                'taken_time' => null,
                'queue_date' => null,
                'service_start_time' => null,
                'service_end_time' => null,
                'service_duration' => $perf->avg_service_time,
                'queue_status' => 'Total: ' . $perf->total_served . ' | Hari ini: ' . $perf->today_served,
                'customer' => (object) ['name' => '-', 'phone' => '-'],
            ];
        });

        $stats = [
            'totalQueue' => collect($statCards)->firstWhere('label', 'Total Antrean')['value'] ?? 0,
            'completedQueue' => collect($statCards)->firstWhere('label', 'Selesai')['value'] ?? 0,
            'avgServiceTime' => 0,
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\QueueExport($rows, $stats),
            'supervisor-live-tracking-' . date('Y-m-d-His') . '.xlsx'
        );
    }
}

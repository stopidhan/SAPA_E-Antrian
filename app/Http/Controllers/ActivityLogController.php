<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display the Activity Log dashboard with filters and stats.
     */
    public function index(Request $request)
    {
        $instanceId = app(\App\Services\TenantManager::class)->getInstanceId();
        
        $query = Activity::where('instance_id', $instanceId)->with('causer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhereJsonContains('properties->action_label', $search)
                  ->orWhereHasMorph('causer', [\App\Models\User::class], function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('log_name', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('properties->status', $request->status);
        }

        // Limit results to prevent memory exhaustion, paginate would be better but let's map what we have
        $logs = $query->latest('id')->limit(500)->get()->map(function($activity) {
            $props = $activity->properties ?? [];
            return (object) [
                'id' => $activity->id,
                'action' => $props['action_label'] ?? 'Aktivitas Sistem',
                'category' => $activity->log_name ?? 'default',
                'status' => $props['status'] ?? 'info',
                'description' => $activity->description,
                'ip_address' => $props['ip_address'] ?? null,
                'logged_at' => $activity->created_at,
                'properties' => (object) ($props['attributes'] ?? []),
                'raw_properties' => $props,
                'user' => $activity->causer ? (object)[
                    'name' => $activity->causer->name,
                    'username' => $activity->causer->username,
                    'role' => $activity->causer->role ?? 'user',
                ] : null,
            ];
        });

        // Calculate stats based on today for the current instance
        $todayQuery = Activity::where('instance_id', $instanceId)->whereDate('created_at', today());
        
        $totalLogs = $todayQuery->count();
        $successCount = (clone $todayQuery)->whereJsonContains('properties->status', 'success')->count();
        $warningCount = (clone $todayQuery)->whereJsonContains('properties->status', 'warning')->count();
        $errorCount = (clone $todayQuery)->whereJsonContains('properties->status', 'error')->count();
        $infoCount = (clone $todayQuery)->whereJsonContains('properties->status', 'info')->count();
        $successRate = $totalLogs > 0 ? round(($successCount / $totalLogs) * 100) : 0;

        return view('Pages.AdminInstansi.activityLog', compact(
            'logs', 'totalLogs', 'successCount', 'warningCount', 'errorCount', 'infoCount', 'successRate'
        ));
    }
}

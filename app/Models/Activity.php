<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends SpatieActivity
{
    /**
     * Get the instance that owns the activity.
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    protected static function booted()
    {
        static::creating(function ($activity) {
            if (!$activity->instance_id) {
                // Try to get instance_id from TenantManager
                $instanceId = app(\App\Services\TenantManager::class)->getInstanceId();
                if ($instanceId) {
                    $activity->instance_id = $instanceId;
                } elseif (auth()->check() && auth()->user()->instance_id) {
                    // Fallback to user's instance_id if available
                    $activity->instance_id = auth()->user()->instance_id;
                }
            }
        });
    }
}

<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToInstance
{
    protected static function bootBelongsToInstance()
    {
        // AUTO isi instance_id saat create
        static::creating(function ($model) {
            $tenantManager = app(\App\Services\TenantManager::class);
            $instanceId = $tenantManager->getInstanceId();
            
            if ($instanceId && empty($model->instance_id)) {
                $model->instance_id = $instanceId;
            }
        });

        // AUTO filter berdasarkan instance login
        static::addGlobalScope('instance', function (Builder $builder) {
            $tenantManager = app(\App\Services\TenantManager::class);
            $instanceId = $tenantManager->getInstanceId();

            if ($instanceId) {
                $builder->where(
                    $builder->getModel()->getTable().'.instance_id',
                    $instanceId
                );
            }
        });
    }
}

<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToInstance
{
    protected static function bootBelongsToInstance()
    {
        // AUTO isi instance_id saat create
        static::creating(function ($model) {
            if (Auth::check() && empty($model->instance_id)) {
                $model->instance_id = Auth::user()->instance_id;
            }
        });

        // AUTO filter berdasarkan instance login
        static::addGlobalScope('instance', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where(
                    $builder->getModel()->getTable().'.instance_id',
                    Auth::user()->instance_id
                );
            }
        });
    }
}

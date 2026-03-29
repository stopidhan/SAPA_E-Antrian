<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstance;

class Queue extends Model
{
    use HasFactory, BelongsToInstance;

    protected $fillable = [
        'instance_id',
        'service_counter_id',
        'customer_id',
        'service_id',
        'service_id',
        'queue_number',
        'queue_date',
        'taken_time',
        'call_time',
        'service_start_time',
        'service_end_time',
        'service_duration',
        'service_description',
        'queue_status',
        'queue_source'
    ];

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function category()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function counter()
    {
        return $this->belongsTo(ServiceCounter::class, 'service_counter_id');
    }

    public function logs()
    {
        return $this->hasMany(QueueLog::class);
    }

    public function photos()
    {
        return $this->hasMany(QueuePhoto::class);
    }
}


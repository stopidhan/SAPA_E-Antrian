<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'instance_id',
        'service_id',
        'user_id',
        'counter_number',
        'is_active'
    ];

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}

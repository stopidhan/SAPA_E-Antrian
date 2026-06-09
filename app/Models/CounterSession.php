<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToInstance;

class CounterSession extends Model
{
    use HasFactory, BelongsToInstance;

    protected $fillable = [
        'instance_id',
        'service_counter_id',
        'user_id',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function counter()
    {
        return $this->belongsTo(ServiceCounter::class, 'service_counter_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

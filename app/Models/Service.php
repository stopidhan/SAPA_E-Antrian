<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstance;

class Service extends Model
{
    use HasFactory, BelongsToInstance;

    protected $table = 'services';

    protected $fillable = [
        'instance_id',
        'service_name',
        'queue_prefix',
        'description',
        'is_active'
    ];

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function counters()
    {
        return $this->hasMany(ServiceCounter::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}

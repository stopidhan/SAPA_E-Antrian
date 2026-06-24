<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstance;
use App\Models\Traits\LogsActivity;

class Service extends Model
{
    use HasFactory;
    use BelongsToInstance;
    use LogsActivity;

    protected $table = 'services';

    protected $fillable = [
        'instance_id',
        'service_name',
        'queue_prefix',
        'description',
        'is_active',
        'performance_standards'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'performance_standards' => 'array',
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

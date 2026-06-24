<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstance;
use App\Models\Traits\LogsActivity;

class Service extends Model
{
    use HasFactory, BelongsToInstance, LogsActivity;

    protected $table = 'services';

    protected $fillable = [
        'instance_id',
        'service_name',
        'queue_prefix',
        'description',
        'slot_duration',   // Durasi per slot (menit) — diatur Admin Instansi
        'slot_capacity',   // Kapasitas per slot (orang) — diatur Admin Instansi
        'is_active',
    ];

    protected $casts = [
        'slot_duration' => 'integer',
        'slot_capacity' => 'integer',
        'is_active'     => 'boolean',
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

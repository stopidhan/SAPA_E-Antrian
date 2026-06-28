<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSlot extends Model
{
    use HasFactory;

    protected $table = 'service_slots';

    protected $fillable = [
        'service_id',
        'start_time',
        'end_time',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Relasi ke model Service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

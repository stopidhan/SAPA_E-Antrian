<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\LogsActivity;

class InstanceSlot extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'instance_slots';

    protected $fillable = [
        'instance_id',
        'start_time',
        'end_time',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }
}

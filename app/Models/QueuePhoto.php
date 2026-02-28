<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueuePhoto extends Model
{
    protected $fillable = [
        'queue_id',
        'photo_path'
    ];

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }
}

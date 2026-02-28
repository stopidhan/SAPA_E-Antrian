<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueLog extends Model
{
    protected $fillable = [
        'queue_id',
        'user_id',
        'action',
        'action_time',
        'notes'
    ];

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

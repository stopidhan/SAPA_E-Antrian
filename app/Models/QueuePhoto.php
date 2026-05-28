<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueuePhoto extends Model
{
    use HasFactory;

    protected $table = 'queue_photos';

    protected $fillable = [
        'queue_id',
        'photo_path'
    ];

    public function queue()
    {
        return $this->belongsTo(Queue::class, 'queue_id');
    }
}

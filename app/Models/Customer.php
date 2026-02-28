<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstance;

class Customer extends Model
{
    use HasFactory, BelongsToInstance;

    protected $fillable = [
        'instance_id',
        'name',
        'phone'
    ];

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}

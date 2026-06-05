<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'instance_id',
        'name',
        'phone',
        'whatsapp_verified_at',
        'last_login_at',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'whatsapp_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}

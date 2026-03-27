<?php

namespace App\Models;

use App\Models\Traits\BelongsToInstance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory, BelongsToInstance;

    protected $fillable = [
        'instance_id',
        'name',
        'phone',
        'whatsapp_verified_at',
        'otp_code_hash',
        'otp_expires_at',
        'otp_attempts',
        'otp_last_sent_at',
        'last_login_at',
    ];

    protected $hidden = [
        'otp_code_hash',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'otp_last_sent_at' => 'datetime',
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

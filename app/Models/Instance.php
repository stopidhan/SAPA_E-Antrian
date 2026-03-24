<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instance extends Model
{
    use HasFactory;

    protected $fillable = [
        'instance_code',
        'instance_name',
        'address',
        'phone',
        'email',
        'logo'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function serviceCounters()
    {
        return $this->hasMany(ServiceCounter::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function mediaContents()
    {
        return $this->hasMany(MediaContent::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    // Opsional: Mutator untuk generate UUID otomatis saat create
    // protected static function booted()
    // {
    //     static::creating(function ($instance) {
    //         if (empty($instance->instance_code)) {
    //             $instance->instance_code = Str::uuid();
    //         }
    //     });
    // }
}

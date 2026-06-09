<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instance extends Model
{
    use HasFactory;

    protected $fillable = [
        'instance_code',
        'instance_slug',
        'instance_name',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'tts_enabled',
        'max_bookings_per_day',
        'is_active',
        'brand_color',
        'timezone',
        'settings',
    ];

    protected $casts = [
        'tts_enabled' => 'boolean',
        'max_bookings_per_day' => 'integer',
        'is_active' => 'boolean',
        'settings' => 'array',
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

    protected static function booted()
    {
        static::saving(function ($instance) {
            if (empty($instance->instance_slug) && !empty($instance->instance_name)) {
                $instance->instance_slug = \Illuminate\Support\Str::slug($instance->instance_name);
            }
        });
    }
}

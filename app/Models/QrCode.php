<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'instance_id',
        'qr_data',
    ];

    // Relasi: QR code belongs to an instance
    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }
}

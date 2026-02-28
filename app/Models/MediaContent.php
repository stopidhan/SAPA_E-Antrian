<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstance;

class MediaContent extends Model
{
    use HasFactory, BelongsToInstance;

    protected $fillable = [
        'instance_id',
        'title',
        'media_type',
        'file_path',
        'duration',
        'is_active'
    ];

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }
}

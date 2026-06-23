<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Traits\BelongsToInstance;
use App\Models\Traits\LogsActivity;

class MediaContent extends Model
{
    use HasFactory, BelongsToInstance, LogsActivity;

    protected $fillable = [
        "instance_id",
        "title",
        "media_type",
        "file_path",
        "duration",
        "fit_mode",
        "sort_order",
        "is_active",
    ];

    protected $casts = [
        "is_active" => "boolean",
        "duration" => "integer",
    ];

    /**
     * Get the instance that owns the media content.
     */
    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Get the full URL of the media file.
     */
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Scope a query to only include active media.
     */
    public function scopeActive($query)
    {
        return $query->where("is_active", true);
    }

    /**
     * Scope a query to filter by media type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where("media_type", $type);
    }
}

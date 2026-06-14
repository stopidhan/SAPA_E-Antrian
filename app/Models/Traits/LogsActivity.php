<?php

namespace App\Models\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getLogCategoryName());
    }

    protected function getLogCategoryName(): string
    {
        $className = class_basename(static::class);
        return match($className) {
            'Queue' => 'queue',
            'Service', 'ServiceCounter' => 'service',
            'User' => 'user',
            'MediaContent' => 'content',
            'Instance' => 'config',
            default => 'default',
        };
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $className = class_basename(static::class);
        $indonesianNames = [
            'Queue' => 'Antrean',
            'Service' => 'Layanan',
            'ServiceCounter' => 'Loket',
            'User' => 'Pengguna',
            'MediaContent' => 'Konten Media',
            'Instance' => 'Pengaturan Instansi',
        ];

        $name = $indonesianNames[$className] ?? $className;
        
        // Coba ambil nama/judul spesifik dari data yang diubah
        $itemName = $this->name ?? $this->username ?? $this->service_name ?? $this->counter_number ?? $this->title ?? $this->instance_name ?? '';
        $identifier = $itemName ? " '{$itemName}'" : '';

        return match ($eventName) {
            'created' => "Data {$name}{$identifier} baru berhasil ditambahkan.",
            'updated' => "Data {$name}{$identifier} berhasil diperbarui.",
            'deleted' => "Data {$name}{$identifier} telah dihapus.",
            default => "Aktivitas {$eventName} pada {$name}{$identifier}.",
        };
    }

    public function tapActivity(\Spatie\Activitylog\Models\Activity $activity, string $eventName)
    {
        $className = class_basename(static::class);
        $indonesianNames = [
            'Queue' => 'Antrean',
            'Service' => 'Layanan',
            'ServiceCounter' => 'Loket',
            'User' => 'Pengguna',
            'MediaContent' => 'Konten',
            'Instance' => 'Instansi',
        ];
        
        $name = $indonesianNames[$className] ?? $className;
        
        $actionLabels = [
            'created' => 'Tambah ' . $name,
            'updated' => 'Update ' . $name,
            'deleted' => 'Hapus ' . $name,
        ];

        $activity->properties = $activity->properties->merge([
            'status' => $eventName === 'deleted' ? 'warning' : 'success',
            'action_label' => $actionLabels[$eventName] ?? ucfirst($eventName),
            'ip_address' => request()->ip(),
        ]);
    }
}

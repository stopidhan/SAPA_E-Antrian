@props([
    'value' => '',
])

@php
    $value = strtolower($value);

    // Determine color based on value
    if ($value === 'completed' || $value === 'verified' || $value === 'active' || $value === 'supervisor') {
        $bgColor = 'bg-green-100';
        $textColor = 'text-green-700';
        $displayText = match ($value) {
            'completed' => 'Selesai',
            'verified' => 'Terverifikasi',
            'active' => 'Aktif',
            'supervisor' => 'Supervisor',
        };
    } elseif ($value === 'pending' || $value === 'waiting') {
        $bgColor = 'bg-amber-100';
        $textColor = 'text-amber-600';
        $displayText = $value === 'pending' ? 'Pending' : 'Menunggu';
    } elseif ($value === 'rejected' || $value === 'cancelled' || $value === 'inactive' || $value === 'admin') {
        $bgColor = 'bg-red-100';
        $textColor = 'text-red-600';
        $displayText = match ($value) {
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'inactive' => 'Nonaktif',
            'admin' => 'Admin',
        };
    } elseif ($value === 'online' || $value === 'serving' || $value === 'operator') {
        $bgColor = 'bg-blue-100';
        $textColor = 'text-blue-700';
        $displayText = match ($value) {
            'online' => 'Online',
            'serving' => 'Dilayani',
            'operator' => 'Operator',
        };
    } elseif ($value === 'staff-content') {
        $bgColor = 'bg-purple-100';
        $textColor = 'text-purple-800';
        $displayText = 'Staff Konten';
    } else {
        $bgColor = 'bg-gray-100';
        $textColor = 'text-gray-600';
        $displayText = ucfirst($value);
    }
@endphp

<span class="px-3 py-1 rounded-full text-xs font-medium {{ $bgColor }} {{ $textColor }}">
    {{ $displayText }}
</span>

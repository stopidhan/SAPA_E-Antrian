{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — konfirmasi.blade.php
| Tahap 3: Validasi Kehadiran
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konfirmasi Kehadiran — SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="bg-gray-200 antialiased">

@php
    $prefix = strtoupper($service->queue_prefix ?? 'Q');
    
    $colorMap = [
        'A' => [
            'headerBg' => 'bg-blue-600',
            'headerTextSub' => 'text-blue-200',
            'text' => 'text-blue-600',
            'bgLight' => 'bg-blue-50',
            'border' => 'border-blue-100',
        ],
        'B' => [
            'headerBg' => 'bg-emerald-600',
            'headerTextSub' => 'text-emerald-200',
            'text' => 'text-emerald-600',
            'bgLight' => 'bg-emerald-50',
            'border' => 'border-emerald-100',
        ],
        'C' => [
            'headerBg' => 'bg-amber-500',
            'headerTextSub' => 'text-amber-200',
            'text' => 'text-amber-600',
            'bgLight' => 'bg-amber-50',
            'border' => 'border-amber-100',
        ]
    ];

    $c = $colorMap[$prefix] ?? [
        'headerBg' => 'bg-gray-600',
        'headerTextSub' => 'text-gray-200',
        'text' => 'text-gray-600',
        'bgLight' => 'bg-gray-50',
        'border' => 'border-gray-100',
    ];
@endphp

<div class="w-full max-w-screen-2xl mx-auto min-h-screen bg-gray-50 relative flex flex-col">

    {{-- ====== HEADER (Warna Sesuai Layanan) ====== --}}
    <div class="px-5 pt-5">
        <div class="{{ $c['headerBg'] }} rounded-2xl px-5 pt-4 pb-4 shadow-md">
            <p class="{{ $c['headerTextSub'] }} text-[10px] font-semibold uppercase tracking-widest">Layanan dipilih:</p>
            <h1 class="text-white text-xl font-extrabold mt-0.5">{{ $service->service_name }}</h1>
        </div>
    </div>

    {{-- ====== CONTENT ====== --}}
    <div class="flex-1 px-4 sm:px-5 pb-28 relative z-10">

        {{-- Judul Konfirmasi --}}
        <div class="mt-4 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Konfirmasi Kehadiran</h2>
                    <p class="text-[11px] text-gray-400 leading-relaxed mt-0.5">Apakah Anda yakin dapat menghadiri layanan ini sesuai jadwal?</p>
                </div>
            </div>
        </div>

        @if ($errors->has('limit_booking'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
                {{ $errors->first('limit_booking') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div class="{{ $c['bgLight'] }} border {{ $c['border'] }} rounded-2xl p-4">
                <p class="text-[10px] font-semibold {{ $c['text'] }} uppercase tracking-widest mb-2">Detail Layanan</p>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Layanan</span>
                        <span class="text-xs font-bold text-gray-900">{{ $service->service_name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Estimasi Waktu</span>
                        <span class="text-xs font-bold text-gray-900">± 15 menit</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Tanggal</span>
                        <span class="text-xs font-bold text-gray-900">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4">
                <p class="text-[10px] font-semibold text-emerald-500 uppercase tracking-widest mb-2">Data Pendaftar</p>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Nama</span>
                        <span class="text-xs font-bold text-gray-900">{{ $customer->name ?? $customer->nama }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">WhatsApp</span>
                        <span class="text-xs font-bold text-gray-900">{{ $customer->phone ?? $customer->no_hp }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Warning Box --}}
        <div class="bg-amber-50 border border-amber-300 rounded-2xl p-4">
            <p class="text-[10px] font-semibold text-amber-700 uppercase tracking-widest mb-2.5">⚠️ Perhatian</p>
            <ul class="space-y-2">
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 bg-amber-200 text-amber-700 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
                    </span>
                    <span class="text-xs text-amber-800 leading-relaxed">Anda <strong>wajib hadir</strong> sesuai jadwal yang ditentukan.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 bg-amber-200 text-amber-700 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </span>
                    <span class="text-xs text-amber-800 leading-relaxed">Bawa <strong>semua dokumen asli</strong> yang dipersyaratkan.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 bg-amber-200 text-amber-700 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span class="text-xs text-amber-800 leading-relaxed">Ketidakhadiran <strong>tanpa pembatalan</strong> dapat mempengaruhi akses Anda di kemudian hari.</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- ====== TOMBOL AKSI (STICKY BOTTOM) ====== --}}
    <div class="sticky bottom-0 z-30 bg-white border-t border-gray-100 px-4 sm:px-5 py-4 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ route('booking.dashboard') }}"
               class="flex items-center justify-center gap-1.5 py-3.5 border-2 border-red-300 text-red-600 text-xs font-bold rounded-xl hover:bg-red-50 active:bg-red-100 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Tidak Bisa Hadir
            </a>
            <form action="{{ route('booking.ambil-antrean') }}" method="POST">
                @csrf
                <input type="hidden" name="layanan" value="{{ $slug }}">
                <button type="submit"
                        class="w-full flex items-center justify-center gap-1.5 py-3.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Ya, Saya Datang
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>

{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — riwayat.blade.php
| Halaman Ekstra: History Kunjungan
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat — SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="bg-gray-200 antialiased">

@php
    $riwayat = [
        (object)['tanggal' => '12 Feb 2026', 'layanan' => 'Pelayanan KTP',  'nomor' => 'A-011', 'kode' => 'A', 'status' => 'Selesai',    'warna' => 'blue'],
        (object)['tanggal' => '10 Feb 2026', 'layanan' => 'Konsultasi',     'nomor' => 'C-005', 'kode' => 'C', 'status' => 'Dibatalkan', 'warna' => 'amber'],
        (object)['tanggal' => '03 Feb 2026', 'layanan' => 'Pelayanan KK',   'nomor' => 'B-019', 'kode' => 'B', 'status' => 'Selesai',    'warna' => 'emerald'],
        (object)['tanggal' => '28 Jan 2026', 'layanan' => 'Pelayanan Akta', 'nomor' => 'C-032', 'kode' => 'C', 'status' => 'Selesai',    'warna' => 'amber'],
        (object)['tanggal' => '15 Jan 2026', 'layanan' => 'Pelayanan KTP',  'nomor' => 'A-045', 'kode' => 'A', 'status' => 'Dibatalkan', 'warna' => 'blue'],
    ];
@endphp

<div class="max-w-md mx-auto min-h-screen bg-gray-50 relative flex flex-col">

    {{-- ====== TOP BAR ====== --}}
    <nav class="bg-white sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-2.5 px-4 py-3">
            <a href="{{ route('booking.dashboard') }}"
               class="p-1.5 -ml-1.5 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <h1 class="text-sm font-bold text-gray-900">Riwayat Antrean Saya</h1>
        </div>
    </nav>

    {{-- ====== DAFTAR RIWAYAT ====== --}}
    <div class="flex-1 px-4 py-4">

        {{-- Summary --}}
        <div class="flex items-center gap-2 mb-3">
            <div class="flex-1 bg-white border border-gray-100 rounded-lg px-2.5 py-2 text-center shadow-sm">
                <p class="text-sm font-black text-gray-900">{{ collect($riwayat)->where('status', 'Selesai')->count() }}</p>
                <p class="text-[9px] text-gray-400 font-medium">Selesai</p>
            </div>
            <div class="flex-1 bg-white border border-gray-100 rounded-lg px-2.5 py-2 text-center shadow-sm">
                <p class="text-sm font-black text-gray-900">{{ collect($riwayat)->where('status', 'Dibatalkan')->count() }}</p>
                <p class="text-[9px] text-gray-400 font-medium">Dibatalkan</p>
            </div>
            <div class="flex-1 bg-white border border-gray-100 rounded-lg px-2.5 py-2 text-center shadow-sm">
                <p class="text-sm font-black text-gray-900">{{ count($riwayat) }}</p>
                <p class="text-[9px] text-gray-400 font-medium">Total</p>
            </div>
        </div>

        {{-- List --}}
        <div class="space-y-2">
            @foreach ($riwayat as $item)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-3 py-2.5 hover:shadow-md transition-shadow">
                {{-- Header: Tanggal + Badge --}}
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] text-gray-400 font-medium flex items-center gap-1">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        {{ $item->tanggal }}
                    </span>

                    @if ($item->status === 'Selesai')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[9px] font-semibold rounded-full border border-emerald-100">
                            <span class="w-1 h-1 bg-emerald-500 rounded-full"></span>
                            Selesai
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 text-red-600 text-[9px] font-semibold rounded-full border border-red-200">
                            <span class="w-1 h-1 bg-red-500 rounded-full"></span>
                            Dibatalkan
                        </span>
                    @endif
                </div>

                {{-- Content: Kode + Detail --}}
                <div class="flex items-center gap-2.5">
                    {{-- Kode Badge --}}
                    @php
                        $kodeBg = match($item->warna) {
                            'blue'    => 'bg-blue-600',
                            'emerald' => 'bg-emerald-600',
                            'amber'   => 'bg-amber-500',
                            default   => 'bg-gray-500',
                        };
                    @endphp
                    <div class="w-9 h-9 {{ $kodeBg }} rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                        <span class="text-white text-xs font-black">{{ $item->kode }}</span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs font-bold text-gray-900 truncate">{{ $item->layanan }}</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Nomor: <span class="font-semibold text-gray-500">{{ $item->nomor }}</span></p>
                    </div>

                    <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="mt-4">
            <div class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2.5 text-center">
                <p class="text-[10px] text-gray-400">Menampilkan {{ count($riwayat) }} riwayat terakhir</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>

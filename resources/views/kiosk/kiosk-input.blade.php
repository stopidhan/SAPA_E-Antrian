{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — kiosk-input.blade.php
| Tahap 2: Input Data Pengunjung (Mesin Kiosk Layar Sentuh)
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Input Data — SAPA Kiosk</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="antialiased">

@php
    $slug = request('layanan', 'pelayanan-ktp');

    $layananData = [
        'pelayanan-ktp'  => (object)['kode' => 'A', 'nama' => 'Pelayanan KTP',  'warnaBg' => 'bg-blue-600',    'warnaText' => 'text-blue-600',    'warnaLight' => 'bg-blue-50',    'warnaBorder' => 'border-blue-200'],
        'pelayanan-kk'   => (object)['kode' => 'B', 'nama' => 'Pelayanan KK',   'warnaBg' => 'bg-emerald-600', 'warnaText' => 'text-emerald-600', 'warnaLight' => 'bg-emerald-50', 'warnaBorder' => 'border-emerald-200'],
        'pelayanan-akta' => (object)['kode' => 'C', 'nama' => 'Pelayanan Akta', 'warnaBg' => 'bg-amber-500',   'warnaText' => 'text-amber-600',   'warnaLight' => 'bg-amber-50',   'warnaBorder' => 'border-amber-200'],
        'informasi-umum' => (object)['kode' => 'D', 'nama' => 'Informasi Umum', 'warnaBg' => 'bg-purple-600',  'warnaText' => 'text-purple-600',  'warnaLight' => 'bg-purple-50',  'warnaBorder' => 'border-purple-200'],
    ];

    $current = $layananData[$slug] ?? $layananData['pelayanan-ktp'];
@endphp

<div class="w-full min-h-screen bg-gradient-to-br from-blue-500 to-blue-700 flex flex-col items-center justify-center font-sans select-none relative pb-10">

    {{-- ====== DEKORASI LATAR ====== --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-white/5 rounded-full"></div>
    </div>

    {{-- ====== HEADER ====== --}}
    <div class="text-center mb-8 relative z-10">
        <div class="inline-flex items-center justify-center bg-white rounded-xl px-5 py-2 shadow-lg shadow-blue-900/20 mb-5">
            <span class="text-blue-600 text-lg font-black tracking-tight">SAPA</span>
        </div>
        <h1 class="text-white text-4xl font-extrabold tracking-tight mb-2">Data Pengunjung</h1>
        <p class="text-blue-100 text-lg">Masukkan data Anda untuk mendapatkan nomor antrean</p>
    </div>

    {{-- ====== CARD UTAMA ====== --}}
    <div class="bg-white rounded-2xl shadow-2xl shadow-blue-900/30 w-full max-w-2xl mx-6 overflow-hidden relative z-10">

        {{-- Judul --}}
        <div class="text-center pt-8 pb-2 px-8">
            <h2 class="text-xl font-bold text-gray-900">Masukkan Data Anda</h2>
        </div>

        {{-- Content --}}
        <div class="px-8 pb-8 pt-4">

            {{-- Highlight Layanan Terpilih --}}
            <div class="flex items-center gap-4 {{ $current->warnaLight }} border {{ $current->warnaBorder }} rounded-xl px-5 py-4 mb-6">
                <div class="w-12 h-12 {{ $current->warnaBg }} rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                    <span class="text-white text-lg font-black">{{ $current->kode }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Layanan yang dipilih</p>
                    <p class="text-base font-bold text-gray-900">{{ $current->nama }}</p>
                </div>
                <div class="w-10 h-10 {{ $current->warnaLight }} rounded-full flex items-center justify-center shrink-0 border {{ $current->warnaBorder }}">
                    <svg class="w-5 h-5 {{ $current->warnaText }}" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </div>
            </div>

            {{-- Form Input --}}
            <div class="mb-8">
                <label for="nama" class="block text-sm font-bold text-gray-900 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="nama" placeholder="Masukkan nama lengkap Anda"
                       class="w-full px-5 py-4 text-base bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all outline-none placeholder:text-gray-300">
                <p class="text-xs text-gray-400 mt-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    Nama akan ditampilkan pada struk antrean
                </p>
            </div>

            {{-- Aksi --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('kiosk.home') }}"
                   class="flex-1 flex items-center justify-center gap-2 py-4 border-2 border-gray-200 text-gray-600 text-base font-bold rounded-xl hover:bg-gray-50 active:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                    Ganti Layanan
                </a>
                <a href="{{ route('kiosk.cetak', ['layanan' => $slug]) }}"
                   class="flex-1 flex items-center justify-center gap-2 py-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-base font-bold rounded-xl shadow-lg shadow-blue-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m0 0a48.103 48.103 0 0110.5 0m-10.5 0V5.625c0-.621.504-1.125 1.125-1.125h8.25c.621 0 1.125.504 1.125 1.125v2.009"/></svg>
                    Cetak Nomor Antrean
                </a>
            </div>
        </div>
    </div>

    {{-- ====== FOOTER: Scan QR Code ====== --}}
    <div class="mt-8 text-center relative z-10">
        <p class="text-blue-100 text-sm mb-3">Sudah daftar online?</p>
        <a href="{{ route('kiosk.scan') }}"
           class="inline-flex items-center gap-3 px-8 py-3.5 bg-white/10 hover:bg-white/20 active:bg-white/25 border-2 border-white/30 text-white text-base font-bold rounded-xl backdrop-blur-sm transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/></svg>
            Scan QR Code
        </a>
    </div>

    {{-- ====== WATERMARK ====== --}}
    <div class="absolute bottom-4 text-center w-full">
        <p class="text-blue-200/50 text-xs">SAPA E-Antrian &middot; Kiosk Self-Service</p>
    </div>

</div>

</body>
</html>

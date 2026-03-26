{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — kiosk-cetak.blade.php
| Tahap 3: Struk & Sukses (Mesin Kiosk Layar Sentuh)
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cetak Struk — SAPA Kiosk</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Figtree', sans-serif }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.6; }
            50%      { opacity: 1; }
        }
        @keyframes printSlide {
            0%   { transform: translateY(-8px); opacity: 0.5; }
            50%  { transform: translateY(4px); opacity: 1; }
            100% { transform: translateY(-8px); opacity: 0.5; }
        }
        .animate-slide-down { animation: slideDown 0.5s ease-out; }
        .animate-pulse-glow { animation: pulseGlow 2s ease-in-out infinite; }
        .animate-print-slide { animation: printSlide 2s ease-in-out infinite; }
    </style>
</head>
<body class="antialiased">

@php
    $slug = request('layanan', 'pelayanan-ktp');

    $layananData = [
        'pelayanan-ktp'  => (object)['kode' => 'A', 'nama' => 'Pelayanan KTP',  'nomor' => 'A012', 'warnaBg' => 'bg-blue-600',    'warnaText' => 'text-blue-600'],
        'pelayanan-kk'   => (object)['kode' => 'B', 'nama' => 'Pelayanan KK',   'nomor' => 'B007', 'warnaBg' => 'bg-emerald-600', 'warnaText' => 'text-emerald-600'],
        'pelayanan-akta' => (object)['kode' => 'C', 'nama' => 'Pelayanan Akta', 'nomor' => 'C024', 'warnaBg' => 'bg-amber-500',   'warnaText' => 'text-amber-600'],
        'informasi-umum' => (object)['kode' => 'D', 'nama' => 'Informasi Umum', 'nomor' => 'D003', 'warnaBg' => 'bg-purple-600',  'warnaText' => 'text-purple-600'],
    ];

    $current = $layananData[$slug] ?? $layananData['pelayanan-ktp'];
    $nama = request('nama', 'Budi Santoso');
@endphp

<div class="w-full min-h-screen bg-gradient-to-br from-blue-500 to-blue-700 flex flex-col items-center justify-center font-sans select-none relative pb-10">

    {{-- ====== DEKORASI LATAR ====== --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-white/5 rounded-full"></div>
    </div>

    {{-- ====== TOAST NOTIFIKASI ====== --}}
    <div class="fixed top-6 left-1/2 -translate-x-1/2 z-50 animate-slide-down">
        <div class="bg-white rounded-xl shadow-2xl shadow-blue-900/20 px-6 py-3.5 flex items-center gap-3 border border-emerald-100">
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </div>
            <p class="text-sm font-bold text-gray-900">Struk nomor antrean berhasil dicetak!</p>
        </div>
    </div>

    {{-- ====== CARD UTAMA ====== --}}
    <div class="bg-white rounded-2xl shadow-2xl shadow-blue-900/30 w-full max-w-lg mx-6 overflow-hidden relative z-10">

        {{-- Ikon Sukses --}}
        <div class="pt-10 pb-2 text-center">
            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-emerald-100">
                <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </div>
            <p class="text-gray-400 text-sm font-medium uppercase tracking-widest">Nomor Antrean Anda</p>
        </div>

        {{-- Nomor Antrean Raksasa --}}
        <div class="text-center pb-4">
            <p class="{{ $current->warnaText }} text-7xl font-black tracking-tight leading-none">{{ $current->nomor }}</p>
        </div>

        {{-- Detail Data --}}
        <div class="text-center pb-6 space-y-1">
            <p class="text-base text-gray-900 font-bold">{{ $nama }}</p>
            <p class="text-sm text-gray-400">{{ $current->nama }}</p>
        </div>

        {{-- Garis Pemisah --}}
        <div class="relative mx-6">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-5 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-r-full -ml-6"></div>
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-5 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-l-full -mr-6"></div>
            <div class="border-t-2 border-dashed border-gray-200"></div>
        </div>

        {{-- Animasi Printer --}}
        <div class="px-8 py-6">
            <div class="bg-gray-50 rounded-xl p-6 text-center">
                <div class="animate-print-slide mb-3">
                    <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m0 0a48.103 48.103 0 0110.5 0m-10.5 0V5.625c0-.621.504-1.125 1.125-1.125h8.25c.621 0 1.125.504 1.125 1.125v2.009"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-700 mb-1">Struk sedang dicetak...</p>
                <p class="text-xs text-gray-400">Silakan ambil struk Anda</p>
            </div>
        </div>

        {{-- Instruksi --}}
        <div class="px-8 pb-6 text-center">
            <div class="bg-blue-50 border border-blue-100 rounded-xl px-5 py-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                <p class="text-sm text-blue-700 leading-relaxed text-left">Silakan menuju <strong>Ruang Tunggu</strong>. Nomor Anda akan dipanggil di <strong>TV Monitor</strong>.</p>
            </div>
        </div>

        {{-- Tombol Selesai --}}
        <div class="px-8 pb-8">
            <a href="{{ route('kiosk.home') }}"
               class="w-full flex items-center justify-center gap-2 py-4 border-2 border-gray-200 text-gray-600 text-base font-bold rounded-xl hover:bg-gray-50 active:bg-gray-100 transition">
                Selesai
            </a>
        </div>
    </div>

    {{-- ====== WATERMARK ====== --}}
    <div class="absolute bottom-4 text-center w-full">
        <p class="text-blue-200/50 text-xs">SAPA E-Antrian &middot; Kiosk Self-Service</p>
    </div>

</div>

</body>
</html>

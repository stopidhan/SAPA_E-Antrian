{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — kiosk-scan.blade.php
| Tahap Alternatif: Scan QR Code Online (Mesin Kiosk Layar Sentuh)
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scan QR — SAPA Kiosk</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Figtree', sans-serif }
        @keyframes scanLine {
            0%   { top: 10%; opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { top: 85%; opacity: 0; }
        }
        .scan-line {
            animation: scanLine 2.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="antialiased">

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
        <h1 class="text-white text-4xl font-extrabold tracking-tight mb-2">Validasi Tiket Online</h1>
        <p class="text-blue-100 text-lg">Scan QR Code dari aplikasi booking Anda</p>
    </div>

    {{-- ====== CARD UTAMA ====== --}}
    <div class="bg-white rounded-2xl shadow-2xl shadow-blue-900/30 w-full max-w-lg mx-6 overflow-hidden relative z-10">

        {{-- Judul --}}
        <div class="text-center pt-8 pb-4 px-8">
            <h2 class="text-xl font-bold text-gray-900">Scan QR Code Anda</h2>
        </div>

        {{-- Area Kamera --}}
        <div class="px-8 pb-4">
            <div class="relative w-full aspect-square bg-gray-900 rounded-2xl overflow-hidden shadow-inner">

                {{-- Simulasi Feed Kamera (Gelap) --}}
                <div class="absolute inset-0 bg-gradient-to-b from-gray-800 via-gray-900 to-gray-800"></div>

                {{-- Corner Brackets --}}
                <div class="absolute inset-0 p-6">
                    {{-- Top-Left --}}
                    <div class="absolute top-6 left-6 w-12 h-12 border-t-4 border-l-4 border-emerald-400 rounded-tl-lg"></div>
                    {{-- Top-Right --}}
                    <div class="absolute top-6 right-6 w-12 h-12 border-t-4 border-r-4 border-emerald-400 rounded-tr-lg"></div>
                    {{-- Bottom-Left --}}
                    <div class="absolute bottom-6 left-6 w-12 h-12 border-b-4 border-l-4 border-emerald-400 rounded-bl-lg"></div>
                    {{-- Bottom-Right --}}
                    <div class="absolute bottom-6 right-6 w-12 h-12 border-b-4 border-r-4 border-emerald-400 rounded-br-lg"></div>
                </div>

                {{-- Garis Scanning (Hijau + Glow) --}}
                <div class="scan-line absolute left-6 right-6 h-0.5 z-10">
                    <div class="w-full h-full bg-emerald-400 rounded-full"></div>
                    <div class="w-full h-3 bg-emerald-400/20 rounded-full -mt-1.5 blur-sm"></div>
                    <div class="w-full h-6 bg-emerald-400/10 rounded-full -mt-3 blur-md"></div>
                </div>

                {{-- Center Icon --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-white/20 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="0.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>
                        </svg>
                        <p class="text-white/30 text-xs font-medium">Menunggu QR Code...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instruksi --}}
        <div class="px-8 pb-6 text-center">
            <div class="bg-blue-50 border border-blue-100 rounded-xl px-5 py-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                <p class="text-sm text-blue-700 leading-relaxed text-left">Arahkan <strong>QR Code</strong> yang Anda dapatkan di HP ke arah kamera</p>
            </div>
        </div>

        {{-- Tombol Kembali --}}
        <div class="px-8 pb-8">
            <a href="{{ route('kiosk.home') }}"
               class="w-full flex items-center justify-center gap-2 py-4 border-2 border-gray-200 text-gray-600 text-base font-bold rounded-xl hover:bg-gray-50 active:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                Kembali ke Halaman Utama
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

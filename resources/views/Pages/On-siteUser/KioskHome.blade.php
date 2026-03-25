{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — kiosk-home.blade.php
| Tahap 1: Pilih Layanan (Mesin Kiosk Layar Sentuh)
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Layanan — SAPA Kiosk</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="antialiased">

<div class="w-full min-h-screen bg-gradient-to-br from-blue-500 to-blue-700 flex flex-col items-center justify-center font-sans select-none relative pb-10">

    {{-- ====== DEKORASI LATAR ====== --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-white/5 rounded-full"></div>
        <div class="absolute top-1/3 right-10 w-40 h-40 bg-white/[0.03] rounded-full"></div>
    </div>

    {{-- ====== HEADER ====== --}}
    <div class="text-center mb-8 relative z-10">
        <div class="inline-flex items-center justify-center bg-white rounded-xl px-5 py-2 shadow-lg shadow-blue-900/20 mb-5">
            <span class="text-blue-600 text-lg font-black tracking-tight">SAPA</span>
        </div>
        <h1 class="text-white text-4xl font-extrabold tracking-tight mb-2">Selamat Datang</h1>
        <p class="text-blue-100 text-lg">Silakan Pilih Layanan</p>
    </div>

    {{-- ====== CARD UTAMA ====== --}}
    <div class="bg-white rounded-2xl shadow-2xl shadow-blue-900/30 w-full max-w-2xl mx-6 overflow-hidden relative z-10">

        {{-- Judul --}}
        <div class="text-center pt-8 pb-5 px-8">
            <h2 class="text-xl font-bold text-gray-900">Pilih Jenis Layanan yang Anda Butuhkan</h2>
        </div>

        {{-- Grid Layanan (2x2) --}}
        <div class="px-8 pb-8">
            <div class="grid grid-cols-2 gap-4">

                {{-- Layanan A: Pelayanan KTP --}}
                <a href="{{ route('kiosk.input', ['layanan' => 'pelayanan-ktp']) }}"
                   class="group border-2 border-gray-100 hover:border-blue-400 rounded-xl p-6 text-center transition-all duration-200 hover:shadow-lg hover:shadow-blue-100 hover:scale-[1.02] active:scale-[0.98]">
                    <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-md shadow-blue-200 group-hover:shadow-lg group-hover:shadow-blue-300 transition">
                        <span class="text-white text-xl font-black">A</span>
                    </div>
                    <p class="text-base font-bold text-gray-900 mb-1">Pelayanan KTP</p>
                    <p class="text-sm text-gray-400">Estimasi: ~15 menit</p>
                </a>

                {{-- Layanan B: Pelayanan KK --}}
                <a href="{{ route('kiosk.input', ['layanan' => 'pelayanan-kk']) }}"
                   class="group border-2 border-gray-100 hover:border-emerald-400 rounded-xl p-6 text-center transition-all duration-200 hover:shadow-lg hover:shadow-emerald-100 hover:scale-[1.02] active:scale-[0.98]">
                    <div class="w-14 h-14 bg-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-md shadow-emerald-200 group-hover:shadow-lg group-hover:shadow-emerald-300 transition">
                        <span class="text-white text-xl font-black">B</span>
                    </div>
                    <p class="text-base font-bold text-gray-900 mb-1">Pelayanan KK</p>
                    <p class="text-sm text-gray-400">Estimasi: ~20 menit</p>
                </a>

                {{-- Layanan C: Pelayanan Akta --}}
                <a href="{{ route('kiosk.input', ['layanan' => 'pelayanan-akta']) }}"
                   class="group border-2 border-gray-100 hover:border-amber-400 rounded-xl p-6 text-center transition-all duration-200 hover:shadow-lg hover:shadow-amber-100 hover:scale-[1.02] active:scale-[0.98]">
                    <div class="w-14 h-14 bg-amber-500 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-md shadow-amber-200 group-hover:shadow-lg group-hover:shadow-amber-300 transition">
                        <span class="text-white text-xl font-black">C</span>
                    </div>
                    <p class="text-base font-bold text-gray-900 mb-1">Pelayanan Akta</p>
                    <p class="text-sm text-gray-400">Estimasi: ~25 menit</p>
                </a>

                {{-- Layanan D: Informasi Umum --}}
                <a href="{{ route('kiosk.input', ['layanan' => 'informasi-umum']) }}"
                   class="group border-2 border-gray-100 hover:border-purple-400 rounded-xl p-6 text-center transition-all duration-200 hover:shadow-lg hover:shadow-purple-100 hover:scale-[1.02] active:scale-[0.98]">
                    <div class="w-14 h-14 bg-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-md shadow-purple-200 group-hover:shadow-lg group-hover:shadow-purple-300 transition">
                        <span class="text-white text-xl font-black">D</span>
                    </div>
                    <p class="text-base font-bold text-gray-900 mb-1">Informasi Umum</p>
                    <p class="text-sm text-gray-400">Estimasi: ~10 menit</p>
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

    {{-- ====== WATERMARK BAWAH ====== --}}
    <div class="absolute bottom-4 text-center w-full">
        <p class="text-blue-200/50 text-xs">SAPA E-Antrian &middot; Kiosk Self-Service</p>
    </div>

</div>

</body>
</html>

{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — login.blade.php
| Tahap 1: Login & Keamanan
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register — SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="bg-gray-200 antialiased">

<div class="max-w-md mx-auto min-h-screen bg-gray-50 relative flex flex-col"
     x-data="{ captchaChecked: false }">
     <div class="px-6 mt-8">

    {{-- ====== HEADER BIRU ====== --}}
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl h-14 w-full mt-8 flex items-center justify-center mx-auto">
        <span class="text-white font-bold text-lg">Register dulu ya!</span>
    </div>
        {{-- Dekoratif lingkaran --}
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-6 -left-8 w-24 h-24 bg-white/5 rounded-full"></div>
        <h1 class="text-white text-lg font-extrabold tracking-tight relative z-10">Register dulu ya!</h1>
        <p class="text-blue-200 text-xs mt-1 relative z-10">Daftar untuk mulai mengambil antrean online</p>
    </div>

    {{-- ====== FORM CARD ====== --}}
    <div class="mt-6 flex-1 px-5 -mt-5 pb-8 relative z-10">
        <div class="bg-white border border-gray-100 rounded-2xl px-6 py-8 shadow-xl shadow-gray-200/60">

            {{-- Logo --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl shadow-lg shadow-blue-300/40 mb-3">
                    <span class="text-white text-lg font-black tracking-wider">SAPA</span>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Selamat Datang!</h2>
                <p class="text-xs text-gray-400 mt-0.5">Silakan Registrasi untuk melanjutkan</p>
            </div>

            {{-- Form --}}
            <form action="{{ route('booking.dashboard') }}" method="GET" class="space-y-5">

                {{-- Input: WhatsApp --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nomor WhatsApp</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        </span>
                        <input name="whatsapp" type="tel" inputmode="numeric" maxlength="15" required
                               placeholder="08xx xxxx xxxx"
                               oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                    </div>
                </div>

                {{-- Input: Nama --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </span>
                        <input name="nama" type="text" required
                               placeholder="Masukkan nama lengkap"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                    </div>
                </div>

                {{-- reCAPTCHA Tiruan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Verifikasi Keamanan</label>
                    <div class="flex items-center justify-between border border-gray-200 rounded-xl bg-gray-50 px-4 py-3 cursor-pointer hover:bg-gray-100 transition"
                         @click="captchaChecked = !captchaChecked">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 border-2 rounded flex items-center justify-center transition-all"
                                 :class="captchaChecked ? 'bg-blue-600 border-blue-600' : 'border-gray-300 bg-white'">
                                <svg x-show="captchaChecked" x-cloak class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm text-gray-600">Saya bukan robot</span>
                        </div>
                        <div class="flex flex-col items-center opacity-50">
                            <svg class="w-7 h-7 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            <span class="text-[8px] text-gray-400">reCAPTCHA</span>
                        </div>
                    </div>
                </div>

                {{-- Tombol Masuk --}}
                <button type="submit"
                        :disabled="!captchaChecked"
                        class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-200 transition disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-blue-600">
                    Masuk
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-[10px] text-gray-400 mt-5">SAPA E-Antrian &copy; {{ date('Y') }}</p>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>

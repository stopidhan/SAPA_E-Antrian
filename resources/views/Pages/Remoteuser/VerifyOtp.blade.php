<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi OTP - SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 antialiased min-h-screen flex items-center justify-center p-4" style="font-family:'Figtree',sans-serif">
    <div class="w-full max-w-lg">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl px-6 py-5 shadow-lg">
            <h1 class="text-xl font-extrabold">Verifikasi OTP</h1>
            <p class="text-sm text-blue-100 mt-1">Masukkan 6 digit kode yang dikirim ke {{ $pendingPhone }}.</p>
        </div>

        <div class="bg-white rounded-2xl mt-4 p-6 shadow-xl border border-slate-100">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('booking.otp.verify') }}" method="POST" class="space-y-4">
                @csrf

                <input type="hidden" name="whatsapp" value="{{ old('whatsapp', $pendingPhone) }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode OTP</label>
                    <input
                        name="otp_code"
                        type="text"
                        inputmode="numeric"
                        maxlength="6"
                        required
                        autofocus
                        value="{{ old('otp_code') }}"
                        placeholder="Contoh: 123456"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm tracking-[0.3em] text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('otp_code')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('whatsapp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white hover:bg-blue-700 active:bg-blue-800 transition"
                >
                    Verifikasi dan Login
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('booking.register') }}" class="text-sm text-blue-700 hover:text-blue-800 font-semibold">
                    Kembali ke halaman registrasi
                </a>
            </div>
        </div>
    </div>
</body>
</html>

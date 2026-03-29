<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Customer - SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="bg-slate-100 antialiased min-h-screen flex items-center justify-center p-4" style="font-family:'Figtree',sans-serif">
    <div class="w-full max-w-lg">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-2xl px-6 py-5 shadow-lg">
            <h1 class="text-xl font-extrabold">Login Customer</h1>
            <p class="text-sm text-blue-100 mt-1">Gunakan nomor WhatsApp Anda untuk masuk dengan cepat.</p>
        </div>

        <div class="bg-white rounded-2xl mt-4 p-6 shadow-xl border border-slate-100">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('booking_register'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $errors->first('booking_register') }}
                </div>
            @endif

            <form action="{{ route('booking.login.submit') }}" method="POST" class="space-y-4">
                @csrf


                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                    <input
                        name="whatsapp"
                        type="tel"
                        inputmode="numeric"
                        maxlength="15"
                        required
                        value="{{ old('whatsapp') }}"
                        placeholder="08xxxxxxxxxx"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('whatsapp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}" data-theme="light"></div>
                    @error('cf-turnstile-response')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white hover:bg-blue-700 active:bg-blue-800 transition"
                >
                    Login
                </button>

                <div class="mt-4 text-center">
                    <p class="text-sm text-slate-600">
                        Belum punya akun? 
                        <a href="{{ route('booking.register') }}" class="font-bold text-blue-600 hover:underline">Daftar sekarang</a>
                    </p>
                </div>
            </form>
        </div>

        <p class="mt-4 text-center text-xs text-slate-500">SAPA E-Antrian &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>

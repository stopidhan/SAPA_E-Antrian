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
    <style>
        body {
            font-family: 'Figtree', sans-serif
        }
    </style>
    @include('components.theme')
</head>

<body class="antialiased">

    <div
        class="w-full min-h-screen bg-gradient-to-br from-primary to-primary/80 flex flex-col items-center justify-center font-sans select-none relative pb-10">

        {{-- ====== DEKORASI LATAR ====== --}}
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-white/5 rounded-full"></div>
            <div class="absolute top-1/3 right-10 w-40 h-40 bg-white/[0.03] rounded-full"></div>
        </div>

        {{-- ====== HEADER ====== --}}
        <div class="text-center mb-8 relative z-10">
            <div class="inline-flex items-center justify-center bg-white rounded-xl px-2 py-2 shadow-lg shadow-blue-900/20 mb-5">
                @if (isset($instance) && $instance->logo)
                    <img src="{{ asset('storage/' . $instance->logo) }}" alt="Logo" class="h-10 w-auto object-contain rounded-lg">
                @else
                    <img src="{{ asset('Icon-SAPA.jpeg') }}" alt="Logo SAPA" class="h-10 w-auto object-contain rounded-lg">
                @endif
            </div>
            <h1 class="text-white text-4xl font-extrabold tracking-tight mb-2">Selamat Datang</h1>
            <p class="text-blue-100 text-lg">Silakan Pilih Layanan</p>
        </div>

        {{-- ====== CARD UTAMA ====== --}}
        <div
            class="bg-white rounded-2xl shadow-2xl shadow-blue-900/30 w-full max-w-2xl mx-6 overflow-hidden relative z-10">

            {{-- Judul --}}
            <div class="text-center pt-8 pb-5 px-8">
                <h2 class="text-xl font-bold text-gray-900">Pilih Jenis Layanan yang Anda Butuhkan</h2>
            </div>

            {{-- Info Kuota --}}
            @if ($totalKuotaOffline > 0)
                <div class="px-8 pb-6">
                    <div
                        class="bg-primary/10/50 border border-blue-100 rounded-xl p-4 flex items-center justify-around shadow-sm">
                        <div class="text-center">
                            <p class="text-[10px] text-primary font-bold uppercase tracking-wider mb-1">Total Kuota</p>
                            <p class="text-2xl font-black text-primary">{{ $totalKuotaOffline }}</p>
                        </div>
                        <div class="w-px h-10 bg-blue-200/50"></div>
                        <div class="text-center">
                            <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider mb-1">Sisa
                                Tersedia</p>
                            <p class="text-2xl font-black text-emerald-600">{{ $sisaKuotaOffline }}</p>
                        </div>
                        <div class="w-px h-10 bg-blue-200/50"></div>
                        <div class="text-center">
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Sudah Terisi
                            </p>
                            <p class="text-2xl font-black text-gray-700">{{ $totalTerisiOffline }}</p>
                        </div>
                    </div>

                    @if ($sisaKuotaOffline <= 0)
                        <div class="mt-4 flex items-start gap-2 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                            <span class="shrink-0 text-sm">⚠️</span>
                            <p class="text-[11px] text-red-700 leading-relaxed"><strong>Mohon Maaf.</strong> Kuota
                                antrean On-site (Kiosk) hari ini sudah penuh.</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Grid Layanan --}}
            <div class="px-8 pb-8">
                <div class="grid grid-cols-2 gap-4">
                    @php
                        $colorMap = [
                            'A' => [
                                'bg' => 'bg-primary',
                                'hover' => 'hover:border-blue-400',
                                'shadow' => 'shadow-blue-200',
                                'hoverShadow' => 'group-hover:shadow-blue-300',
                            ],
                            'B' => [
                                'bg' => 'bg-emerald-600',
                                'hover' => 'hover:border-emerald-400',
                                'shadow' => 'shadow-emerald-200',
                                'hoverShadow' => 'group-hover:shadow-emerald-300',
                            ],
                            'C' => [
                                'bg' => 'bg-amber-500',
                                'hover' => 'hover:border-amber-400',
                                'shadow' => 'shadow-amber-200',
                                'hoverShadow' => 'group-hover:shadow-amber-300',
                            ],
                            'D' => [
                                'bg' => 'bg-purple-600',
                                'hover' => 'hover:border-purple-400',
                                'shadow' => 'shadow-purple-200',
                                'hoverShadow' => 'group-hover:shadow-purple-300',
                            ],
                        ];
                        $defaultColor = [
                            'bg' => 'bg-slate-600',
                            'hover' => 'hover:border-slate-400',
                            'shadow' => 'shadow-slate-200',
                            'hoverShadow' => 'group-hover:shadow-slate-300',
                        ];
                    @endphp

                    @forelse($services as $service)
                        @php
                            $prefix = strtoupper($service->queue_prefix);
                            $color = $colorMap[$prefix] ?? $defaultColor;
                            $slug = Str::slug($service->service_name);
                            $isFull = $totalKuotaOffline > 0 && $sisaKuotaOffline <= 0;
                        @endphp
                        <a href="{{ $isFull ? '#' : route('kiosk.input', ['layanan' => $slug]) }}"
                            class="group border-2 border-gray-100 {{ $isFull ? 'bg-gray-50 opacity-60 cursor-not-allowed' : $color['hover'] . ' hover:shadow-lg ' . str_replace('shadow-', 'hover:shadow-', $color['shadow']) . ' hover:scale-[1.02] active:scale-[0.98]' }} rounded-xl p-6 text-center transition-all duration-200">
                            <div
                                class="w-14 h-14 {{ $isFull ? 'bg-gray-400' : $color['bg'] }} rounded-xl flex items-center justify-center mx-auto mb-4 shadow-md {{ $color['shadow'] }} {{ $isFull ? '' : $color['hoverShadow'] }} transition">
                                <span class="text-white text-xl font-black">{{ $prefix }}</span>
                            </div>
                            <p class="text-base font-bold text-gray-900 mb-1">{{ $service->service_name }}</p>
                            <p class="text-sm text-gray-400">{{ $service->description ?? 'Ambil Antrean' }}</p>
                        </a>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <p class="text-gray-500 font-medium">Belum ada layanan yang aktif untuk instansi ini.</p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>

        {{-- ====== FOOTER: Scan QR Code ====== --}}
        <div class="mt-8 text-center relative z-10">
            <p class="text-blue-100 text-sm mb-3">Sudah daftar online?</p>
            <a href="{{ route('kiosk.scan') }}"
                class="inline-flex items-center gap-3 px-8 py-3.5 bg-white/10 hover:bg-white/20 active:bg-white/25 border-2 border-white/30 text-white text-base font-bold rounded-xl backdrop-blur-sm transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                </svg>
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

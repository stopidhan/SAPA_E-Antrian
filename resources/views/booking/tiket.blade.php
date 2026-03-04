{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — tiket.blade.php
| Tahap 4: Tiket Final & QR Code
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tiket Booking — SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="bg-gray-200 antialiased">

@php
    $slug = request('layanan', 'pelayanan-kk');

    $tiketData = [
        'pelayanan-ktp' => (object)[
            'nomorAntrean' => 'A-024',
            'kodeBooking'  => 'BKG-32624504',
            'layanan'      => 'Pelayanan KTP',
        ],
        'pelayanan-kk' => (object)[
            'nomorAntrean' => 'B-007',
            'kodeBooking'  => 'BKG-41283756',
            'layanan'      => 'Pelayanan KK',
        ],
        'pelayanan-akta' => (object)[
            'nomorAntrean' => 'C-012',
            'kodeBooking'  => 'BKG-58493102',
            'layanan'      => 'Pelayanan Akta',
        ],
    ];

    $currentTiket = $tiketData[$slug] ?? $tiketData['pelayanan-kk'];
    $nomorAntrean  = $currentTiket->nomorAntrean;
    $kodeBooking   = $currentTiket->kodeBooking;
    $layanan       = $currentTiket->layanan;
    $nama          = 'Khairuddin Al Fadhilah';
    $whatsapp      = '081234567890';

    // Batas waktu kedatangan: 30 menit dari sekarang (simulasi)
    $batasWaktu    = now()->addMinutes(30)->toIso8601String();
@endphp

<div class="max-w-md mx-auto min-h-screen bg-gray-50 relative flex flex-col">

    {{-- ====== HEADER SUKSES (Hijau) ====== --}}
    <div class="bg-emerald-600 rounded-b-[2.5rem] px-6 pt-10 pb-20 text-center relative overflow-hidden">
        {{-- Dekoratif --}}
        <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-4 -left-6 w-24 h-24 bg-white/5 rounded-full"></div>

        {{-- Ikon Centang --}}
        <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 relative z-10">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
        </div>
        <h1 class="text-white text-xl font-extrabold relative z-10">Booking Berhasil!</h1>
        <p class="text-emerald-200 text-xs mt-1 relative z-10">Tiket tersimpan — scan di kiosk saat tiba di instansi</p>
    </div>

    {{-- ====== CARD UTAMA (Menimpa header) ====== --}}
    <div class="flex-1 px-5 -mt-12 pb-28 relative z-10 space-y-4">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-gray-100 overflow-hidden">

            {{-- Badge Tersimpan --}}
            <div class="flex items-center justify-center gap-2 bg-emerald-50 border-b border-emerald-100 py-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-[11px] font-semibold text-emerald-700">Tiket Tersimpan di Perangkat Anda</p>
            </div>

            {{-- Countdown Timer --}}
            <div class="px-5 pt-4 pb-0" x-data="countdown('{{ $batasWaktu }}')" x-init="startTimer()">
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                    <div class="flex items-center justify-center gap-2 mb-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[11px] font-bold text-amber-800">Batas Waktu Kedatangan</p>
                    </div>
                    <div class="flex items-center justify-center gap-1.5">
                        <div class="bg-white rounded-lg px-2 py-1 border border-amber-200 min-w-[36px] text-center">
                            <p class="text-base font-black text-amber-700" x-text="hours">00</p>
                            <p class="text-[8px] text-amber-500 font-semibold -mt-0.5">Jam</p>
                        </div>
                        <span class="text-amber-400 font-black text-lg">:</span>
                        <div class="bg-white rounded-lg px-2 py-1 border border-amber-200 min-w-[36px] text-center">
                            <p class="text-base font-black text-amber-700" x-text="minutes">00</p>
                            <p class="text-[8px] text-amber-500 font-semibold -mt-0.5">Mnt</p>
                        </div>
                        <span class="text-amber-400 font-black text-lg">:</span>
                        <div class="bg-white rounded-lg px-2 py-1 border border-amber-200 min-w-[36px] text-center">
                            <p class="text-base font-black" :class="seconds <= 10 ? 'text-red-600' : 'text-amber-700'" x-text="seconds">00</p>
                            <p class="text-[8px] text-amber-500 font-semibold -mt-0.5">Dtk</p>
                        </div>
                    </div>
                    <p class="text-[10px] text-amber-600 mt-2 leading-relaxed" x-show="!expired">Segera menuju instansi sebelum waktu habis</p>
                    <p class="text-[10px] text-red-600 font-bold mt-2" x-show="expired" x-cloak>Waktu kedatangan telah habis! Hubungi petugas.</p>
                </div>
            </div>

            {{-- QR Section --}}
            <div class="px-6 pt-6 pb-4 text-center">
                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-widest mb-3">QR Code Anda</p>

                {{-- QR Placeholder --}}
                <div class="w-40 h-40 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto border-2 border-dashed border-gray-200">
                    <div class="text-center">
                        <svg class="w-14 h-14 text-gray-300 mx-auto mb-1.5" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>
                        </svg>
                        <p class="text-[10px] text-gray-400 font-medium">QR Code</p>
                    </div>
                </div>
            </div>

            {{-- Blok Nomor Antrean (Biru Solid) --}}
            <div class="bg-blue-600 mx-4 rounded-xl py-4 px-5 text-center mb-4">
                <p class="text-blue-200 text-[10px] font-semibold uppercase tracking-widest">Nomor Antrean Anda</p>
                <p class="text-white text-4xl font-black tracking-tight mt-1">{{ $nomorAntrean }}</p>
            </div>

            {{-- Kode Booking --}}
            <div class="text-center pb-4">
                <p class="text-[10px] text-gray-400">Kode Booking:</p>
                <p class="text-sm font-bold text-gray-900 tracking-wide">{{ $kodeBooking }}</p>
            </div>

            {{-- Garis Sobekan --}}
            <div class="relative">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-4 h-8 bg-gray-50 rounded-r-full"></div>
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-8 bg-gray-50 rounded-l-full"></div>
                <div class="border-t-2 border-dashed border-gray-200 mx-6"></div>
            </div>

            {{-- Detail Booking --}}
            <div class="px-6 pt-4 pb-6">
                <p class="text-xs font-bold text-gray-900 mb-3">Detail Booking</p>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                            Layanan
                        </span>
                        <span class="text-xs font-bold text-gray-900">{{ $layanan }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            Nama
                        </span>
                        <span class="text-xs font-bold text-gray-900">{{ $nama }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            WhatsApp
                        </span>
                        <span class="text-xs font-bold text-gray-900">{{ $whatsapp }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            Tanggal
                        </span>
                        <span class="text-xs font-bold text-gray-900">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== CARD PETUNJUK CHECK-IN KIOSK ====== --}}
        <div class="bg-blue-50 rounded-2xl border border-blue-100 p-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/></svg>
                </div>
                <p class="text-xs font-bold text-blue-900">Cara Check-in di Kiosk</p>
            </div>
            <div class="space-y-2.5">
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 bg-blue-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5">1</span>
                    <p class="text-[11px] text-blue-800 leading-relaxed">Datang ke instansi pada hari yang ditentukan</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 bg-blue-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5">2</span>
                    <p class="text-[11px] text-blue-800 leading-relaxed">Buka halaman ini atau akses dari <strong>Dashboard → Tiket Tersimpan</strong></p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 bg-blue-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5">3</span>
                    <p class="text-[11px] text-blue-800 leading-relaxed">Arahkan QR Code ke mesin <strong>Kiosk Scanner</strong> untuk check-in otomatis</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== TOMBOL AKSI (STICKY BOTTOM) ====== --}}
    <div class="sticky bottom-0 z-30 bg-white border-t border-gray-100 px-5 py-4 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
        {{-- Unduh QR Full Width --}}
        <button class="w-full flex items-center justify-center gap-2 py-3 mb-2 border-2 border-blue-300 text-blue-600 text-xs font-bold rounded-xl hover:bg-blue-50 active:bg-blue-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            Unduh Gambar QR Code
        </button>
        {{-- Dashboard --}}
        <a href="{{ route('booking.dashboard') }}"
           class="w-full flex items-center justify-center gap-1.5 py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
            Dashboard
        </a>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function countdown(deadline) {
        return {
            hours: '00', minutes: '00', seconds: '00', expired: false, interval: null,
            startTimer() {
                const end = new Date(deadline).getTime();
                this.update(end);
                this.interval = setInterval(() => this.update(end), 1000);
            },
            update(end) {
                const now = Date.now();
                const diff = end - now;
                if (diff <= 0) {
                    this.hours = '00'; this.minutes = '00'; this.seconds = '00'; this.expired = true;
                    clearInterval(this.interval); return;
                }
                this.hours   = String(Math.floor(diff / 3600000)).padStart(2, '0');
                this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
                this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            }
        }
    }
</script>
</body>
</html>

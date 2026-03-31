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
    $nomorAntrean = $nomorAntrean ?? '-';
    $kodeBooking = $kodeBooking ?? '-';
    $layanan = $layanan ?? 'Layanan';
    $nama = $nama ?? session('nama', '-');
    $whatsapp = $whatsapp ?? session('whatsapp', '-');
    $queueId = $queueId ?? null;
    $batasWaktu = $batasWaktu ?? now()->toIso8601String();
    $isExpired = (bool) ($isExpired ?? false);
@endphp

<div class="w-full max-w-screen-2xl mx-auto min-h-screen bg-gray-50 relative flex flex-col">

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
    <div class="flex-1 px-4 sm:px-5 -mt-12 pb-28 relative z-10 space-y-4">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-gray-100 overflow-hidden">
            {{-- Badge Tersimpan --}}
            <div id="badge-capture" class="flex items-center justify-center gap-2 {{ $isExpired ? 'bg-red-50 border-red-100' : 'bg-emerald-50 border-emerald-100' }} border-b py-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-[11px] font-semibold {{ $isExpired ? 'text-red-700' : 'text-emerald-700' }}">
                    {{ $isExpired ? 'Tiket Hangus (Tidak Bisa Digunakan)' : 'Tiket Tersimpan di Perangkat Anda' }}
                </p>
            </div>

            {{-- Countdown Timer --}}
            <div id="timer-capture" class="px-5 pt-4 pb-0" x-data="countdown('{{ $batasWaktu }}', {{ $isExpired ? 'true' : 'false' }}, {{ $queueId ? (int) $queueId : 'null' }}, '{{ route('booking.tiket.expire') }}')" x-init="startTimer()">
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
                    <p class="text-[10px] text-red-600 font-bold mt-2" x-show="expired" x-cloak>Waktu kedatangan habis. Tiket hangus dan tidak bisa dipakai lagi.</p>
                </div>
            </div>

            {{-- Capture Area Starts Here --}}
            <div id="ticket-capture" class="bg-white pb-6">
                {{-- QR Section --}}
                <div class="px-6 pt-6 pb-4 text-center">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-widest mb-3">QR Code Anda</p>

                    {{-- QR Code Asli --}}
                    <div class="w-60 h-60 bg-white rounded-[2rem] flex items-center justify-center mx-auto border-8 border-gray-50 shadow-inner relative overflow-hidden p-4">
                        {!! QrCode::size(220)->generate($kodeBooking) !!}
                        @if($isExpired)
                            <div class="absolute inset-0 bg-red-700/75 backdrop-blur-[1px] flex items-center justify-center px-2">
                                <p class="text-white text-xs font-bold text-center">TIKET HANGUS</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Blok Nomor Antrean --}}
                <div class="bg-blue-600 mx-4 rounded-xl py-4 px-5 text-center mb-4">
                    <p class="text-blue-200 text-[10px] font-semibold uppercase tracking-widest">Nomor Antrean Anda</p>
                    <p class="text-white text-4xl font-black tracking-tight mt-1">{{ $nomorAntrean }}</p>
                </div>

                {{-- Detail Booking --}}
                <div class="px-6 pt-4">
                    <p class="text-xs font-bold text-gray-900 mb-3">Detail Booking</p>
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Layanan</span>
                            <span class="text-xs font-bold text-gray-900">{{ $layanan }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Nama</span>
                            <span class="text-xs font-bold text-gray-900">{{ $nama }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">WhatsApp</span>
                            <span class="text-xs font-bold text-gray-900">{{ $whatsapp }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Tanggal</span>
                            <span class="text-xs font-bold text-gray-900">{{ now()->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== CARD PETUNJUK CHECK-IN KIOSK ====== --}}
        <div id="instruction-capture" class="bg-blue-50 rounded-2xl border border-blue-100 p-4">
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
    <div class="sticky bottom-0 z-30 bg-white border-t border-gray-100 px-4 sm:px-5 py-4 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
        {{-- Unduh QR Full Width --}}
        <button id="btn-download" onclick="downloadFullTicket('{{ $kodeBooking }}')"
                class="w-full flex items-center justify-center gap-2 py-3 mb-2 border-2 text-xs font-bold rounded-xl transition {{ $isExpired ? 'border-gray-200 text-gray-400 cursor-not-allowed opacity-70' : 'border-blue-300 text-blue-600 hover:bg-blue-50 active:bg-blue-100' }}" {{ $isExpired ? 'disabled' : '' }}>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            {{ $isExpired ? 'Tiket Hangus' : 'Unduh Gambar Tiket' }}
        </button>
        {{-- Dashboard --}}
        <a href="{{ route('booking.dashboard') }}"
           class="w-full flex items-center justify-center gap-1.5 py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504-1.125 1.125-1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
            Dashboard
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function downloadFullTicket(kode) {
        const btn = document.getElementById('btn-download');
        const originalHtml = btn.innerHTML;
        
        // State Loading
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';

        const captureArea = document.getElementById('ticket-capture');
        
        // Gunakan html2canvas
        html2canvas(captureArea, {
            scale: 2, // Kualitas HD
            useCORS: true,
            backgroundColor: '#ffffff' // Kartu Putih Bersih
        }).then(canvas => {
            // Trigger Download
            const link = document.createElement('a');
            link.download = `Tiket-SAPA-${kode}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();

            // Kembalikan tombol
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }).catch(err => {
            console.error('Download failed:', err);
            alert('Gagal mengunduh gambar. Silakan coba lagi atau screenshot layar Anda.');
            
            // Kembalikan tombol
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    function countdown(deadline, alreadyExpired = false, queueId = null, expireUrl = '') {
        return {
            hours: '00', minutes: '00', seconds: '00', expired: false, interval: null, expireRequested: false,
            startTimer() {
                if (alreadyExpired) {
                    this.expired = true;
                    return;
                }
                const end = new Date(deadline).getTime();
                this.update(end);
                this.interval = setInterval(() => this.update(end), 1000);
            },
            update(end) {
                const now = Date.now();
                const diff = end - now;
                if (diff <= 0) {
                    this.hours = '00'; this.minutes = '00'; this.seconds = '00'; this.expired = true;
                    this.requestExpire(queueId, expireUrl);
                    clearInterval(this.interval); return;
                }
                this.hours   = String(Math.floor(diff / 3600000)).padStart(2, '0');
                this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
                this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            },
            async requestExpire(queueId, expireUrl) {
                if (this.expireRequested || !queueId || !expireUrl) return;
                this.expireRequested = true;

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                    await fetch(expireUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ queue_id: queueId }),
                    });
                } catch (e) {
                    // Keep UI state expired even if network call fails temporarily.
                }
            }
        }
    }
</script>
</body>
</html>

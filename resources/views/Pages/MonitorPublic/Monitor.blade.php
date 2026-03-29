{{--
|--------------------------------------------------------------------------
| File: monitor/monitor.blade.php
| SAPA E-Antrian — TV Monitor Ruang Tunggu (Public Display 16:9)
| Layar penuh tanpa scroll, untuk digital signage di ruang tunggu.
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitor Antrean — SAPA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak]{display:none!important}body{font-family:'Figtree',sans-serif}
        /* Overlay Style */
        #audio-unlock-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8); backdrop-filter: blur(8px);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            z-index: 9999; color: white; cursor: pointer; transition: opacity 0.5s ease;
        }
    </style>
</head>
<body class="bg-gray-200 antialiased overflow-hidden">

{{-- ====== OVERLAY AKTIFKAN SUARA ====== --}}
<div id="audio-unlock-overlay" onclick="unlockAudio()">
    <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center mb-6 animate-bounce shadow-xl shadow-blue-500/50">
        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/></svg>
    </div>
    <h2 class="text-3xl font-black mb-2">Monitor Sudah Siap</h2>
    <p class="text-gray-300 text-lg">Klik di mana saja untuk mengaktifkan suara notifikasi</p>
</div>

<div class="w-full h-screen bg-gray-50 flex flex-col font-sans overflow-hidden select-none">

    {{-- ====================================================================
    |  1. HEADER (TOP BAR)
    ===================================================================== --}}
    <header class="bg-blue-600 p-6 flex items-center justify-between shrink-0">

        {{-- Kiri: Logo + Judul --}}
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm">
                <span class="text-blue-600 text-lg font-black tracking-tight">SAPA</span>
            </div>
            <div>
                <h1 class="text-white text-2xl font-bold leading-tight">Sistem Antrean Publik</h1>
                <p class="text-white/60 text-sm font-medium">Ruang Tunggu Utama</p>
            </div>
        </div>

        {{-- Kanan: Jam + Tanggal --}}
        <div class="text-right" x-data="monitorClock()" x-init="startClock()">
            <p class="text-white text-5xl font-extrabold tabular-nums leading-none" x-text="jam">14.57</p>
            <p class="text-white/60 text-sm font-medium mt-1" x-text="tanggal">Senin, 2 Maret 2026</p>
        </div>

    </header>

    {{-- ====================================================================
    |  2. KONTEN UTAMA (GRID LAYOUT)
    ===================================================================== --}}
    <div class="p-6 flex-1 min-h-0">
        <div class="grid grid-cols-12 gap-6 h-full">

            {{-- ============================================================
            |  KOLOM KIRI: Multimedia & Info (Span 7)
            ============================================================ --}}
            <div class="col-span-7 flex flex-col">

                {{-- Area Multimedia (Atas) --}}
                <div class="flex-1 relative rounded-3xl overflow-hidden bg-gradient-to-br from-blue-400 to-blue-700">
                    {{-- Placeholder Background --}}
                    <div class="absolute inset-0 bg-cover bg-center"
                         style="background-image: url('https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=1200&q=80'); filter: brightness(0.65);">
                    </div>
                    {{-- Overlay Teks --}}
                    <div class="absolute bottom-0 left-0 right-0 p-8">
                        <p class="text-white/70 text-sm font-semibold uppercase tracking-wider mb-1">Dinas Kependudukan dan Catatan Sipil</p>
                        <h2 class="text-white text-4xl font-extrabold leading-tight">Layanan Publik<br>Terbaik</h2>
                    </div>
                </div>

                {{-- Card Info (Bawah) --}}
                <div class="bg-blue-600 rounded-3xl p-6 text-white mt-6 shrink-0">
                    <div class="grid grid-cols-3 divide-x divide-white/20 text-center">
                        {{-- Jam Operasional --}}
                        <div class="px-4">
                            <p class="text-white/60 text-xs font-semibold uppercase tracking-wider mb-1">Jam Operasional</p>
                            <p class="text-2xl font-extrabold">08:00 - 16:00</p>
                        </div>
                        {{-- Loket Aktif --}}
                        <div class="px-4">
                            <p class="text-white/60 text-xs font-semibold uppercase tracking-wider mb-1">Loket Aktif</p>
                            <p class="text-2xl font-extrabold">2 / 3</p>
                        </div>
                        {{-- Status --}}
                        <div class="px-4">
                            <p class="text-white/60 text-xs font-semibold uppercase tracking-wider mb-1">Status</p>
                            <div class="flex items-center justify-center gap-2 mt-1">
                                <span class="w-3 h-3 bg-emerald-400 rounded-full shadow-sm shadow-emerald-400/50"></span>
                                <span class="text-2xl font-extrabold">Operasional</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ============================================================
            |  KOLOM KANAN: Status Antrean (Span 5)
            ============================================================ --}}
            <div class="col-span-5 flex flex-col gap-6">

                {{-- Card 1: Panggilan Saat Ini --}}
                <div class="bg-white shadow-lg rounded-3xl overflow-hidden border border-gray-100 flex flex-col">
                    {{-- Header --}}
                    <div class="bg-blue-600 px-6 py-4">
                        <h3 class="text-white text-sm font-bold uppercase tracking-widest text-center">Panggilan Saat Ini</h3>
                    </div>
                    {{-- Body --}}
                    <div class="flex-1 flex flex-col items-center justify-center p-10 text-center">
                        <p class="text-blue-600 text-8xl font-black tracking-tight leading-none mb-3">A002</p>
                        <p class="text-gray-900 text-xl font-bold">Silakan ke Loket 1</p>
                    </div>
                </div>

                {{-- Card 2: Status Loket --}}
                <div class="bg-white shadow-lg rounded-3xl overflow-hidden border border-gray-100 flex-1 flex flex-col">
                    {{-- Header --}}
                    <div class="bg-blue-600 px-6 py-4">
                        <h3 class="text-white text-sm font-bold uppercase tracking-widest text-center">Status Loket</h3>
                    </div>
                    {{-- Body: Daftar Loket --}}
                    <div class="flex-1 flex flex-col divide-y divide-gray-100">

                        {{-- Row 1: Loket 1 — Memanggil --}}
                        <div class="flex items-center justify-between px-6 py-5 bg-blue-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                                    <span class="text-white text-sm font-black">1</span>
                                </div>
                                <span class="text-base font-bold text-gray-900">Loket 1</span>
                            </div>
                            <span class="text-xl font-black text-gray-900">A-024</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                Memanggil
                            </span>
                        </div>

                        {{-- Row 2: Loket 2 — Melayani --}}
                        <div class="flex items-center justify-between px-6 py-5 bg-emerald-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center">
                                    <span class="text-white text-sm font-black">2</span>
                                </div>
                                <span class="text-base font-bold text-gray-900">Loket 2</span>
                            </div>
                            <span class="text-xl font-black text-gray-900">A-025</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                Melayani
                            </span>
                        </div>

                        {{-- Row 3: Loket 3 — Standby --}}
                        <div class="flex items-center justify-between px-6 py-5 bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-300 rounded-xl flex items-center justify-center">
                                    <span class="text-white text-sm font-black">3</span>
                                </div>
                                <span class="text-base font-bold text-gray-900">Loket 3</span>
                            </div>
                            <span class="text-xl font-black text-gray-300">-</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 border border-gray-300 text-gray-400 text-xs font-bold rounded-full">
                                <span class="w-2 h-2 border-2 border-gray-300 rounded-full"></span>
                                Standby
                            </span>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    // Konfigurasi Laravel Echo untuk Reverb
    // Inisialisasi Laravel Echo
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: '{{ config('broadcasting.connections.reverb.key') }}',
        wsHost: '{{ config('broadcasting.connections.reverb.options.host') }}',
        wsPort: {{ config('broadcasting.connections.reverb.options.port') }},
        wssPort: {{ config('broadcasting.connections.reverb.options.port') }},
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
    });

    // Listen untuk Event Check-in
    window.Echo.channel('queues')
        .listen('QueueCheckedIn', (e) => {
            console.log('Check-in event received:', e.queue);
            
            // Tampilkan Notifikasi Kedatangan
            if (typeof showArrivalNotification === 'function') {
                showArrivalNotification(e.queue);
            }
            
            // Mainkan Suara Notifikasi (Pengaman Autoplay)
            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3');
            audio.play().catch(e => console.log('Audio autoplay blocked by browser.'));
        });
    
    // Fungsi untuk membuka blokir audio browser
    function unlockAudio() {
        // Hilangkan overlay
        const overlay = document.getElementById('audio-unlock-overlay');
        overlay.style.opacity = '0';
        setTimeout(() => overlay.remove(), 500);

        // Pancing browser agar memberikan izin audio dengan memutar suara kosong/pendek
        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3');
        audio.volume = 0; // Tanpa suara, hanya pancingan
        audio.play().catch(e => console.log('Izin suara diberikan.'));
    }

    function showArrivalNotification(queue) {
        // Buat elemen notifikasi sederhana yang muncul di pojok
        const toast = document.createElement('div');
        toast.className = "fixed bottom-10 right-10 bg-white border-l-8 border-emerald-500 shadow-2xl rounded-2xl p-6 transform translate-y-20 transition-all duration-500 z-50 flex items-center gap-5";
        toast.innerHTML = `
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center shrink-0">
                <span class="text-emerald-600 text-2xl font-black">${queue.queue_number}</span>
            </div>
            <div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">BARU DATANG</p>
                <p class="text-gray-900 text-lg font-extrabold">${queue.service.service_name}</p>
            </div>
        `;
        document.body.appendChild(toast);

        // Animasi muncul
        setTimeout(() => toast.style.transform = "translateY(0)", 100);
        
        // Hapus otomatis setelah 8 detik
        setTimeout(() => {
            toast.style.opacity = "0";
            setTimeout(() => toast.remove(), 500);
        }, 8000);
    }

    function monitorClock() {
        const hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        return {
            jam: '',
            tanggal: '',
            startClock() {
                const update = () => {
                    const now = new Date();
                    const h = String(now.getHours()).padStart(2,'0');
                    const m = String(now.getMinutes()).padStart(2,'0');
                    this.jam = h + '.' + m;
                    this.tanggal = hari[now.getDay()] + ', ' + now.getDate() + ' ' + bulan[now.getMonth()] + ' ' + now.getFullYear();
                };
                update();
                setInterval(update, 1000);
            }
        }
    }
</script>
</body>
</html>

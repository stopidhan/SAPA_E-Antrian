{{--
|--------------------------------------------------------------------------
| File: operator/index.blade.php (File Utama / Layout Wrapper)
| SAPA E-Antrian — Dashboard Operator Loket (Desktop)
| 3 Fase: Standby → Dipanggil → Melayani
| Menggunakan @include partials agar kode bersih & mudah di-maintain.
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Operator — SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:'Figtree',sans-serif}[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased">

{{-- ====== FULL-SCREEN WRAPPER + ALPINE STATE ====== --}}
<div x-data="operatorDashboard()" class="min-h-screen bg-slate-50 flex flex-col">

    {{-- Navbar --}}
    @include('operator.partials.navbar')

    {{-- ====== TOAST NOTIFICATIONS ====== --}}
    <div class="fixed top-4 right-6 z-50 space-y-2" style="min-width: 340px;">
        <template x-for="notif in notifications" :key="notif.id">
            <div x-show="notif.visible"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-4"
                 class="flex items-center gap-3 px-4 py-3 bg-white rounded-xl border shadow-md"
                 :class="{
                     'border-blue-200': notif.type === 'call',
                     'border-amber-200': notif.type === 'skip',
                 }">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                     :class="{
                         'bg-blue-100': notif.type === 'call',
                         'bg-amber-100': notif.type === 'skip',
                     }">
                    <span x-show="notif.type === 'call'" class="text-base">📞</span>
                    <span x-show="notif.type === 'skip'" class="text-base">⏭️</span>
                </div>
                <p class="text-sm font-semibold text-gray-700" x-text="notif.message"></p>
            </div>
        </template>
    </div>

    {{-- ====== LAYOUT UTAMA (2 KOLOM) ====== --}}
    <div class="grid grid-cols-12 gap-6 p-6 max-w-7xl mx-auto w-full flex-1">

        {{-- Kolom Kiri: Panel Antrean (col-span-8) --}}
        <div class="col-span-12 lg:col-span-8">
            @include('operator.partials.standby')
            @include('operator.partials.calling')
            @include('operator.partials.serving')
        </div>

        {{-- Kolom Kanan: Sidebar (col-span-4) --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">
            @include('operator.partials.sidebar')
        </div>

    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function operatorDashboard() {
        return {
            state: 'standby',
            loket: 'Loket 1',
            timerDisplay: '00:00',
            timerSeconds: 0,
            timerInterval: null,
            currentQueue: null,
            notifications: [],
            notifCounter: 0,

            queue: [
                { id: 1, nomor: 'A-024', layanan: 'Pelayanan KTP', tipe: 'onsite' },
                { id: 2, nomor: 'B-015', layanan: 'Pelayanan KK', tipe: 'online' },
                { id: 3, nomor: 'C-008', layanan: 'Pelayanan Akta', tipe: 'onsite' },
                { id: 4, nomor: 'A-025', layanan: 'Pelayanan KTP', tipe: 'online' },
            ],

            showNotif(message, type = 'call') {
                const id = ++this.notifCounter;
                const notif = { id, message, type, visible: true };
                this.notifications.push(notif);
                setTimeout(() => {
                    notif.visible = false;
                    setTimeout(() => {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                    }, 200);
                }, 3000);
            },

            panggilBerikutnya() {
                if (this.queue.length === 0) return;
                this.currentQueue = this.queue.shift();
                this.state = 'calling';
                this.showNotif('Memanggil nomor ' + this.currentQueue.nomor + ' ke ' + this.loket, 'call');
            },

            panggilUlang() {
                if (!this.currentQueue) return;
                this.showNotif('Memanggil ulang nomor ' + this.currentQueue.nomor + '...', 'call');
            },

            lewatiAntrian() {
                if (!this.currentQueue) return;
                const skipped = this.currentQueue;
                this.showNotif('Nomor ' + skipped.nomor + ' dilewati', 'skip');
                this.queue.push(skipped);

                if (this.queue.length > 1) {
                    this.currentQueue = this.queue.shift();
                    setTimeout(() => {
                        this.showNotif('Memanggil nomor ' + this.currentQueue.nomor + ' ke ' + this.loket, 'call');
                    }, 500);
                } else {
                    this.currentQueue = null;
                    this.state = 'standby';
                }
            },

            startServing() {
                this.timerSeconds = 0;
                this.timerDisplay = '00:00';
                this.timerInterval = setInterval(() => {
                    this.timerSeconds++;
                    const m = String(Math.floor(this.timerSeconds / 60)).padStart(2, '0');
                    const s = String(this.timerSeconds % 60).padStart(2, '0');
                    this.timerDisplay = m + ':' + s;
                }, 1000);
            },

            stopServing() {
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                }
                this.timerSeconds = 0;
                this.timerDisplay = '00:00';
                this.currentQueue = null;
            }
        }
    }
</script>
</body>
</html>

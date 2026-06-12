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
    <style>
        body {
            font-family: 'Figtree', sans-serif
        }

        [x-cloak] {
            display: none !important
        }
    </style>
</head>

<body class="antialiased">

    {{-- ====== FULL-SCREEN WRAPPER + ALPINE STATE ====== --}}
    <div x-data="operatorDashboard()" @confirm-close-session.window="closeSession()" class="min-h-screen bg-slate-50 flex flex-col">

        {{-- Navbar --}}
        @include('Pages.StaffOperatorLoket.partials.Navbar')

        {{-- ====== TOAST NOTIFICATIONS ====== --}}
        <div class="fixed top-4 right-6 z-50 space-y-2" style="min-width: 340px;">
            <template x-for="notif in notifications" :key="notif.id">
                <div x-show="notif.visible" x-transition:enter="transition ease-out duration-200"
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
                @include('Pages.StaffOperatorLoket.partials.Standby')
                @include('Pages.StaffOperatorLoket.partials.Calling')
                @include('Pages.StaffOperatorLoket.partials.Serving')
            </div>

            {{-- Kolom Kanan: Sidebar (col-span-4) --}}
            <div class="col-span-12 lg:col-span-4 space-y-6">
                @include('Pages.StaffOperatorLoket.partials.Sidebar')
            </div>

        </div>

        {{-- ====== MODAL PILIH LOKET ====== --}}
        <div x-show="!counterId" x-cloak class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-xl">
                <h2 class="text-2xl font-bold text-slate-800 mb-6 text-center">Pilih Loket / Buka Sesi</h2>
                <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    @foreach($availableCounters as $c)
                    <button @click="openSession({{ $c->id }})" class="w-full text-left px-5 py-4 rounded-xl border-2 border-slate-100 hover:border-blue-500 hover:bg-blue-50 transition-all flex justify-between items-center group">
                        <div>
                            <span class="block font-bold text-slate-800">{{ $c->counter_number }}</span>
                            <span class="block text-sm text-slate-500">{{ $c->service->service_name }}</span>
                        </div>
                        <span class="text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity">➜</span>
                    </button>
                    @endforeach
                    @if($availableCounters->isEmpty())
                        <div class="text-center text-slate-500">Tidak ada loket aktif yang tersedia.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ====== MODAL CONFIRMATION ====== --}}
        <x-modals.modal-confirmation name="confirm-logout" variant="logout" />
        <x-modals.modal-confirmation name="confirm-close-session" variant="close-session" />
    </div>

    <script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function operatorDashboard() {
            return {
                loket: '{{ $namaLoket }}',
                counterId: '{{ $idLoket }}',
                timerDisplay: '00:00',
                timerSeconds: {{ $timerSeconds ?? 0 }},
                timerInterval: null,
                currentQueue: @json($activeQueue ?? null),
                state: '{{ $activeQueue ? ($activeQueue['status'] === 'serving' ? 'serving' : 'calling') : 'standby' }}',
                notifications: [],
                notifCounter: 0,
                serviceCategory: '',
                serviceDescription: '',

                // Camera states
                isCameraOpen: false,
                videoStream: null,
                photoBase64: null,

                queue: @json($queuesData),
                history: @json($historyData),
                instanceSlug: '{{ auth()->user()->instance->instance_slug ?? '' }}',

                init() {
                    // Coba auto-select counter dari localStorage jika ada tapi belum buka sesi
                    if (!this.counterId) {
                        const lastCounterId = localStorage.getItem('last_counter_id');
                        if (lastCounterId) {
                            // Validasi apakah counterId masih ada di availableCounters
                            const availableIds = @json($availableCounters->pluck('id'));
                            if (availableIds.includes(parseInt(lastCounterId))) {
                                this.openSession(lastCounterId);
                                return;
                            }
                        }
                    }

                    // Jika halaman direfresh dan sedang melayani, lanjutkan timer
                    if (this.state === 'serving') {
                        this.startTimer();
                    } else if (this.timerSeconds > 0) {
                        // Jika masih ada sisa timer tapi tidak serving, reset
                        this.timerSeconds = 0;
                    }

                    // Ambil data pertama kali saat halaman dimuat
                    if (this.counterId) {
                        this.fetchQueues();
                    }

                    // INISIALISASI WEBSOCKET (LARAVEL ECHO + REVERB)
                    // Menggantikan sistem polling AJAX setInterval
                    window.Echo = new Echo({
                        broadcaster: 'reverb',
                        key: '{{ config('broadcasting.connections.reverb.key') }}',
                        wsHost: window.location.hostname,
                        wsPort: {{ config('broadcasting.connections.reverb.options.port') }},
                        wssPort: {{ config('broadcasting.connections.reverb.options.port') }},
                        forceTLS: false,
                        enabledTransports: ['ws', 'wss'],
                    });

                    // Listen event dari channel instansi ini via WebSocket
                    window.Echo.channel('queues.{{ auth()->user()->instance_id }}')
                        .listen('QueueUpdated', (e) => {
                            console.log('[WebSocket] Antrean terupdate:', e);
                            if(this.counterId) this.fetchQueues(); // Perbarui daftar antrean seketika tanpa reload
                        })
                        .listen('QueueCheckedIn', (e) => {
                            console.log('[WebSocket] Ada antrean baru datang:', e);
                            if(this.counterId) this.fetchQueues(); // Perbarui daftar antrean seketika tanpa reload
                        });
                },

                openSession(counterId) {
                    fetch(`/${this.instanceSlug}/staff/operator/open-session`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ counter_id: counterId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.setItem('last_counter_id', counterId);
                            window.location.reload();
                        } else {
                            alert(data.message || 'Gagal membuka sesi');
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        console.error('Error opening session:', err);
                        alert('Gagal membuka sesi. Silakan coba lagi.');
                    });
                },

                closeSession() {
                    fetch(`/${this.instanceSlug}/staff/operator/close-session`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.removeItem('last_counter_id');
                            window.location.reload();
                        }
                    });
                },

                fetchQueues() {
                    fetch(`/${this.instanceSlug}/staff/operator/api/queues`)
                        .then(res => {
                            // [BUG FIX] Cek apakah response adalah JSON yang valid
                            // Jika sesi berakhir, server mungkin mengembalikan redirect (HTML),
                            // bukan JSON. Tangani ini dengan elegan tanpa melempar error.
                            const contentType = res.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                console.warn('[SAPA] Sesi habis atau tidak valid. Melewati update antrean.');
                                return null;
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (!data) return; // Skip jika response tidak valid
                            this.queue = data.waiting;
                            this.history = data.history;
                        })
                        .catch(err => console.error("Gagal refresh antrean", err));
                },
                showNotif(message, type = 'call') {
                    const id = ++this.notifCounter;
                    const notif = {
                        id,
                        message,
                        type,
                        visible: true
                    };
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

                    // Request ke server untuk mengubah status di database menjadi 'called'
                    fetch(`/${this.instanceSlug}/staff/operator/panggil/${this.currentQueue.id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                counter_id: this.counterId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                // Jika bentrok (race condition) dengan operator lain
                                this.showNotif(data.message, 'skip');
                                this.currentQueue = null;
                                this.state = 'standby';
                                this.fetchQueues(); // Segarkan antrean seketika
                                return;
                            }
                            console.log(data.message);
                            this.state = 'calling';
                            this.showNotif('Memanggil nomor ' + this.currentQueue.nomor + ' ke ' + this.loket, 'call');
                        })
                        .catch(error => console.error('Error:', error));
                },

                panggilUlang() {
                    if (!this.currentQueue) return;

                    // Request kembali ke server untuk memperbarui waktu panggil (called_time)
                    fetch(`/${this.instanceSlug}/staff/operator/panggil/${this.currentQueue.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            counter_id: this.counterId
                        })
                    });

                    this.showNotif('Memanggil ulang nomor ' + this.currentQueue.nomor + '...', 'call');
                },

                lewatiAntrian() {
                    if (!this.currentQueue) return;
                    const skipped = this.currentQueue;

                    // POST API ke db (dilewati -> skipped)
                    fetch(`/${this.instanceSlug}/staff/operator/lewati/${skipped.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({})
                    });

                    this.showNotif('Nomor ' + skipped.nomor + ' dilewati', 'skip');

                    if (this.queue.length > 0) {
                        this.currentQueue = this.queue.shift();

                        // Otomatis panggil antrean berikutnya
                        fetch(`/${this.instanceSlug}/staff/operator/panggil/${this.currentQueue.id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    counter_id: this.counterId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    this.showNotif(data.message, 'skip');
                                    this.currentQueue = null;
                                    this.state = 'standby';
                                    this.fetchQueues();
                                    return;
                                }
                                this.state = 'calling';
                                this.showNotif('Memanggil nomor ' + this.currentQueue.nomor + ' ke ' + this.loket,
                                    'call');
                            });

                    } else {
                        this.currentQueue = null;
                        this.state = 'standby';
                    }
                },

                /* 
                // Fitur batalkan antrean (Disembunyikan sementara)
                batalkanAntrean() {
                    if (!this.currentQueue) return;
                    const cancelled = this.currentQueue;

                    fetch(`/${this.instanceSlug}/staff/operator/batal/${cancelled.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            counter_id: this.counterId
                        })
                    });

                    this.showNotif('Nomor ' + cancelled.nomor + ' dibatalkan', 'skip');

                    if (this.timerInterval) {
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                    }
                    this.timerSeconds = 0;
                    this.timerDisplay = '00:00';
                    this.currentQueue = null;
                    this.state = 'standby';
                    this.fetchQueues();
                },
                */

                startTimer() {
                    if (this.timerInterval) clearInterval(this.timerInterval);

                    // Pastikan timerSeconds adalah integer bulat
                    this.timerSeconds = Math.floor(this.timerSeconds);

                    // Update display immediately before interval ticks
                    const m = String(Math.floor(this.timerSeconds / 60)).padStart(2, '0');
                    const s = String(this.timerSeconds % 60).padStart(2, '0');
                    this.timerDisplay = m + ':' + s;

                    this.timerInterval = setInterval(() => {
                        this.timerSeconds++;
                        const min = String(Math.floor(this.timerSeconds / 60)).padStart(2, '0');
                        const sec = String(this.timerSeconds % 60).padStart(2, '0');
                        this.timerDisplay = min + ':' + sec;
                    }, 1000);
                },

                startServing() {
                    if (!this.currentQueue) return;

                    fetch(`/${this.instanceSlug}/staff/operator/layani/${this.currentQueue.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            counter_id: this.counterId
                        })
                    });

                    this.state = 'serving';
                    this.timerSeconds = 0;
                    this.startTimer();
                },

                stopServing() {
                    if (!this.currentQueue) return;

                    if (!this.serviceCategory || this.serviceCategory.trim() === '') {
                        this.showNotif('Peringatan: Kategori Layanan wajib dipilih!', 'skip');
                        return;
                    }

                    if (!this.serviceDescription || this.serviceDescription.trim() === '') {
                        this.showNotif('Peringatan: Deskripsi / Catatan Layanan wajib diisi!', 'skip');
                        return;
                    }

                    fetch(`/${this.instanceSlug}/staff/operator/selesai/${this.currentQueue.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            category: this.serviceCategory,
                            description: this.serviceDescription,
                            photo: this.photoBase64
                        })
                    });

                    if (this.timerInterval) {
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                    }
                    this.timerSeconds = 0;
                    this.timerDisplay = '00:00';
                    this.currentQueue = null;
                    this.serviceCategory = '';
                    this.serviceDescription = '';

                    this.stopCameraStream();
                    this.isCameraOpen = false;
                    this.photoBase64 = null;

                    this.state = 'standby'; // Auto reset UI ke mode menunggu/Standby
                },

                startCamera() {
                    this.isCameraOpen = true;
                    this.photoBase64 = null;
                    navigator.mediaDevices.getUserMedia({
                            video: true
                        })
                        .then(stream => {
                            this.videoStream = stream;
                            // wait for alpine to show video element
                            setTimeout(() => {
                                if (this.$refs.videoElement) {
                                    this.$refs.videoElement.srcObject = stream;
                                    this.$refs.videoElement.play();
                                }
                            }, 100);
                        })
                        .catch(err => {
                            console.error("Camera access denied:", err);
                            this.showNotif('Gagal mengakses kamera.', 'skip');
                            this.isCameraOpen = false;
                        });
                },

                takePhoto() {
                    const canvas = this.$refs.canvasElement;
                    const video = this.$refs.videoElement;
                    if (!video) return;

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    this.photoBase64 = canvas.toDataURL('image/jpeg', 0.8);
                    this.stopCameraStream();
                },

                retakePhoto() {
                    this.startCamera();
                },

                stopCameraStream() {
                    if (this.videoStream) {
                        this.videoStream.getTracks().forEach(track => track.stop());
                        this.videoStream = null;
                    }
                }
            }
        }
    </script>
</body>

</html>

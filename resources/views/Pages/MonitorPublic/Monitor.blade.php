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
        [x-cloak] {
            display: none !important
        }

        body {
            font-family: 'Figtree', sans-serif
        }

        body {
            font-family: 'Figtree', sans-serif
        }
    </style>
</head>

<body class="bg-gray-200 antialiased overflow-hidden" x-data="monitorRealtime()" x-init="initMonitor()" @click.once="unlockAudio()">

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
                    <h1 class="text-white text-2xl font-bold leading-tight">{{ $instance->instance_name }}</h1>
                    <p class="text-white/60 text-sm font-medium">Sistem Antrean Publik</p>
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
                    <div class="flex-1 relative rounded-3xl overflow-hidden bg-black shadow-inner" x-data="mediaPlayer()" x-init="initPlayer()">
                        
                        {{-- Media Slider --}}
                        <template x-if="contents.length > 0">
                            <div class="absolute inset-0 w-full h-full">
                                <template x-for="(media, index) in contents" :key="index">
                                    <div x-show="currentIndex === index" 
                                         x-transition.opacity.duration.1000ms
                                         class="absolute inset-0 w-full h-full flex items-center justify-center bg-black">
                                         
                                         <template x-if="media.type === 'image'">
                                             <img :src="media.url" class="w-full h-full object-cover" />
                                         </template>
                                         <template x-if="media.type === 'video'">
                                             <video :id="'media-video-' + index" :src="media.url" class="w-full h-full object-cover" muted playsinline></video>
                                         </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Fallback / Default Background --}}
                        <template x-if="contents.length === 0">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-blue-700 w-full h-full">
                                <div class="absolute inset-0 bg-cover bg-center"
                                    style="background-image: url('https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=1200&q=80'); filter: brightness(0.65);">
                                </div>
                                {{-- Overlay Teks --}}
                                <div class="absolute bottom-0 left-0 right-0 p-8">
                                    <p class="text-white/70 text-sm font-semibold uppercase tracking-wider mb-1">{{ $instance->instance_name }}</p>
                                    <h2 class="text-white text-4xl font-extrabold leading-tight">Layanan Publik<br>Terbaik</h2>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Card Info (Bawah) --}}
                    <div class="bg-blue-600 rounded-3xl p-6 text-white mt-6 shrink-0">
                        <div class="grid grid-cols-3 divide-x divide-white/20 text-center">
                            {{-- Jam Operasional --}}
                            <div class="px-4">
                                <p class="text-white/60 text-xs font-semibold uppercase tracking-wider mb-1">Jam
                                    Operasional</p>
                                <p class="text-2xl font-extrabold" x-text="operationalInfo.jam_operasional">-</p>
                            </div>
                            {{-- Loket Aktif --}}
                            <div class="px-4">
                                <p class="text-white/60 text-xs font-semibold uppercase tracking-wider mb-1">Loket Aktif
                                </p>
                                <p class="text-2xl font-extrabold" x-text="stats.active + ' / ' + stats.total">- / -</p>
                            </div>
                            {{-- Status --}}
                            <div class="px-4">
                                <p class="text-white/60 text-xs font-semibold uppercase tracking-wider mb-1">Status</p>
                                <div class="flex items-center justify-center gap-2 mt-1">
                                    <span
                                        class="w-3 h-3 rounded-full shadow-sm"
                                        :class="operationalInfo.is_open ? 'bg-emerald-400 shadow-emerald-400/50' : 'bg-red-400 shadow-red-400/50'"></span>
                                    <span class="text-2xl font-extrabold" x-text="operationalInfo.status">-</span>
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
                            <h3 class="text-white text-sm font-bold uppercase tracking-widest text-center">Panggilan
                                Saat Ini</h3>
                        </div>
                        {{-- Body --}}
                        <div class="flex-1 flex flex-col items-center justify-center p-10 text-center">
                            <template x-if="currentCall">
                                <div>
                                    <p class="text-blue-600 text-8xl font-black tracking-tight leading-none mb-3"
                                        x-text="currentCall.queue_number">A000</p>
                                    <p class="text-gray-900 text-xl font-bold">Silakan ke <span
                                             x-text="currentCall.counter_number.toLowerCase().includes('loket') ? currentCall.counter_number : 'Loket ' + currentCall.counter_number"></span></p>
                                </div>
                            </template>
                            <template x-if="!currentCall">
                                <div>
                                    <p class="text-gray-300 text-6xl font-black tracking-tight leading-none mb-3">-</p>
                                    <p class="text-gray-400 text-xl font-bold">Belum Ada Panggilan</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Card 2: Status Loket --}}
                    <div
                        class="bg-white shadow-lg rounded-3xl overflow-hidden border border-gray-100 flex-1 flex flex-col">
                        {{-- Header --}}
                        <div class="bg-blue-600 px-6 py-4">
                            <h3 class="text-white text-sm font-bold uppercase tracking-widest text-center">Status Loket
                            </h3>
                        </div>
                        {{-- Body: Daftar Loket --}}
                        <div class="flex-1 flex flex-col divide-y divide-gray-100">

                            <template x-for="counter in counters" :key="counter.id">
                                <div class="flex items-center justify-between px-6 py-5" :class="counter.status_bg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                            :class="counter.icon_bg">
                                            <span class="text-white text-sm font-black"
                                                x-text="counter.counter_number.replace(/loket/gi, '').trim()"></span>
                                        </div>
                                        <span class="text-base font-bold text-gray-900"
                                            x-text="counter.counter_number.toLowerCase().includes('loket') ? counter.counter_number : 'Loket ' + counter.counter_number"></span>
                                    </div>
                                    <span class="text-xl font-black"
                                        :class="counter.queue_number == '-' ? 'text-gray-300' : 'text-gray-900'"
                                        x-text="counter.queue_number"></span>
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full"
                                        :class="{
                                            'bg-blue-100 text-blue-700': counter.status === 'Memanggil',
                                            'bg-emerald-100 text-emerald-700': counter.status === 'Dilayani',
                                            'bg-red-100 text-red-700': counter.status === 'Tutup',
                                            'border border-gray-300 text-gray-400': counter.status === 'Menunggu'
                                        }">
                                        <span class="w-2 h-2 rounded-full"
                                            :class="{
                                                'bg-blue-500': counter.status === 'Memanggil',
                                                'bg-emerald-500': counter.status === 'Dilayani',
                                                'bg-red-500': counter.status === 'Tutup',
                                                'border-2 border-gray-300': counter.status === 'Menunggu'
                                            }"></span>
                                        <span x-text="counter.status"></span>
                                    </span>
                                </div>
                            </template>
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
                wsHost: window.location.hostname,
                wsPort: {{ config('broadcasting.connections.reverb.options.port') }},
                wssPort: {{ config('broadcasting.connections.reverb.options.port') }},
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
            });

            // Listen untuk Event Check-in
            window.Echo.channel('queues.{{ $instance->id }}')
                .listen('QueueCheckedIn', (e) => {
                    console.log('Check-in event received:', e.queue);

                    // Tampilkan Notifikasi Kedatangan
                    if (typeof showArrivalNotification === 'function') {
                        showArrivalNotification(e.queue);
                    }

                    // Mainkan Suara Notifikasi (Pengaman Autoplay)
                    const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3');
                    audio.play().catch(e => console.log('Audio autoplay blocked by browser.'));
                })
                .listen('QueueUpdated', (e) => {
                    console.log('QueueUpdated event received:', e.message, e.queue);

                    // Trigger fetch data to update the small counter cards
                    window.dispatchEvent(new CustomEvent('fetch-monitor-data'));

                    if (e.message === 'called' && e.queue) {
                        // If it is a new call, push it to our audio queue system
                        window.dispatchEvent(new CustomEvent('add-call-queue', {
                            detail: e.queue
                        }));
                    }
                })
                .listen('MediaUpdated', (e) => {
                    console.log('MediaUpdated event received');
                    window.dispatchEvent(new CustomEvent('fetch-monitor-data'));
                });

            // Fungsi untuk membuka blokir audio browser
            function unlockAudio() {
                // Pancing browser agar memberikan izin audio dengan memutar suara kosong/pendek
                const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3');
                audio.volume = 0; // Tanpa suara, hanya pancingan
                audio.play().catch(e => console.log('Izin suara diberikan.'));
            }

            function showArrivalNotification(queue) {
                // Buat elemen notifikasi sederhana yang muncul di pojok
                const toast = document.createElement('div');
                toast.className =
                    "fixed bottom-10 right-10 bg-white border-l-8 border-emerald-500 shadow-2xl rounded-2xl p-6 transform translate-y-20 transition-all duration-500 z-50 flex items-center gap-5";
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

            function mediaPlayer() {
                return {
                    contents: @json($mediaContents ?? []),
                    currentIndex: 0,
                    timer: null,

                    initPlayer() {
                        window.addEventListener('update-media-contents', (e) => {
                            // Cek jika konten benar-benar berubah untuk menghindari restart paksa saat queue update
                            if (JSON.stringify(this.contents) !== JSON.stringify(e.detail)) {
                                this.contents = e.detail;
                                this.currentIndex = 0;
                                this.playCurrentMedia();
                            }
                        });

                        if (this.contents.length === 0) return;
                        this.playCurrentMedia();
                    },

                    playCurrentMedia() {
                        if (this.timer) clearTimeout(this.timer);
                        
                        let media = this.contents[this.currentIndex];
                        
                        if (media.type === 'video') {
                            this.$nextTick(() => {
                                let videoEl = document.getElementById('media-video-' + this.currentIndex);
                                if (videoEl) {
                                    videoEl.currentTime = 0;
                                    videoEl.play().catch(e => console.log('Video play error:', e));
                                }
                                
                                this.timer = setTimeout(() => {
                                    this.nextMedia();
                                }, media.duration);
                            });
                        } else {
                            this.timer = setTimeout(() => {
                                this.nextMedia();
                            }, media.duration);
                        }
                    },

                    nextMedia() {
                        this.currentIndex = (this.currentIndex + 1) % this.contents.length;
                        this.playCurrentMedia();
                    }
                }
            }

            function monitorClock() {
                const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                    'Oktober', 'November', 'Desember'
                ];
                return {
                    jam: '',
                    tanggal: '',
                    startClock() {
                        const update = () => {
                            const now = new Date();
                            const h = String(now.getHours()).padStart(2, '0');
                            const m = String(now.getMinutes()).padStart(2, '0');
                            this.jam = h + '.' + m;
                            this.tanggal = hari[now.getDay()] + ', ' + now.getDate() + ' ' + bulan[now.getMonth()] + ' ' +
                                now.getFullYear();
                        };
                        update();
                        setInterval(update, 1000);
                    }
                };
            }

            function monitorRealtime() {
                    return {
                        currentCall: null,
                        counters: [],
                        stats: {
                            active: '-',
                            total: '-'
                        },
                        operationalInfo: {
                            jam_operasional: '-',
                            status: '-',
                            is_open: false
                        },

                        audioQueue: [],
                        isPlaying: false,
                        ttsEnabled: {{ $instance->tts_enabled ? 'true' : 'false' }},
                        ttsLanguage: '{{ $instance->tts_language ?? "id-ID" }}',

                        initMonitor() {
                            this.fetchData();

                            // Pre-load Web Speech API Voices
                            if ('speechSynthesis' in window) {
                                window.speechSynthesis.getVoices();
                                window.speechSynthesis.onvoiceschanged = () => {
                                    window.speechSynthesis.getVoices();
                                };
                            }

                            // [BUG FIX] Hapus listener lama sebelum mendaftar yang baru
                            // Mencegah bug "double call" akibat initMonitor() terpanggil lebih dari sekali
                            // oleh Alpine.js (misal saat hot-reload atau reactive update)
                            if (this._fetchHandler) window.removeEventListener('fetch-monitor-data', this._fetchHandler);
                            if (this._callHandler) window.removeEventListener('add-call-queue', this._callHandler);

                            // Simpan referensi handler agar bisa di-remove nanti
                            this._fetchHandler = () => {
                                this.fetchData();
                            };
                            this._callHandler = (e) => {
                                this.audioQueue.push(e.detail);
                                this.processAudioQueue();
                            };

                            window.addEventListener('fetch-monitor-data', this._fetchHandler);
                            window.addEventListener('add-call-queue', this._callHandler);
                        },

                        playTTS(queueNumber, counterNumber, callback) {
                            if (!this.ttsEnabled || !('speechSynthesis' in window)) {
                                callback();
                                return;
                            }

                            // Pecah nomor antrean agar dibaca per huruf/angka (Contoh: A 0 0 1)
                            let spelledNumber = queueNumber.split('').join(' ');

                            // Bersihkan kata "loket" dari counterNumber untuk menghindari suara ganda (e.g., "menuju loket Loket 1")
                            let cleanCounter = counterNumber.toLowerCase().includes('loket') 
                                ? counterNumber 
                                : `loket, ${counterNumber}`;

                            // Teks yang akan dibacakan
                            let textToSpeak = `Nomor antrean, ${spelledNumber}. silakan menuju ${cleanCounter}.`;
                            if (this.ttsLanguage.startsWith('en')) {
                                textToSpeak = `Queue number, ${spelledNumber}. please proceed to ${counterNumber.toLowerCase().includes('counter') ? counterNumber : 'counter ' + counterNumber}.`;
                            }

                            let utterance = new SpeechSynthesisUtterance(textToSpeak);
                            utterance.lang = this.ttsLanguage;
                            utterance.rate = 0.85; // Diperlambat sedikit agar lebih jelas

                            // Mencari suara Microsoft Edge TTS (Gadis / Ardi) atau yang sesuai dengan bahasa
                            let voices = window.speechSynthesis.getVoices();
                            let edgeVoice = voices.find(v => (v.name.includes('Gadis') || v.name.includes('Ardi') || v.name.includes('Microsoft')) && v.lang.includes(this.ttsLanguage.split('-')[0]));
                            
                            if (!edgeVoice) {
                                edgeVoice = voices.find(v => v.lang.includes(this.ttsLanguage.split('-')[0]));
                            }

                            if (edgeVoice) {
                                utterance.voice = edgeVoice;
                            }

                            utterance.onend = () => {
                                callback();
                            };
                            utterance.onerror = (e) => {
                                console.error('TTS Error:', e);
                                callback();
                            };

                            window.speechSynthesis.speak(utterance);
                        },

                        processAudioQueue() {
                            if (this.isPlaying || this.audioQueue.length === 0) return;

                            this.isPlaying = true;
                            const callInfo = this.audioQueue.shift();

                            // Update tampilan besar Panggilan Saat Ini ke nomor yang baru masuk
                            this.currentCall = callInfo;

                            // Mainkan bunyi bel (Chime) terlebih dahulu
                            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3');
                            audio.play().then(() => {
                                // Setelah bel bunyi (jeda 2 detik), baru bicara menggunakan Edge TTS
                                setTimeout(() => {
                                    this.playTTS(callInfo.queue_number, callInfo.counter_number, () => {
                                        // Setelah selesai bicara, tunggu 1 detik sebelum lanjut antrean lain
                                        setTimeout(() => {
                                            this.isPlaying = false;
                                            this.processAudioQueue();
                                        }, 1000);
                                    });
                                }, 2000);
                            }).catch(e => {
                                console.log('Audio error:', e);
                                // Kalau bel diblokir browser, paksa langsung baca TTS
                                this.playTTS(callInfo.queue_number, callInfo.counter_number, () => {
                                    this.isPlaying = false;
                                    this.processAudioQueue();
                                });
                            });
                        },

                        fetchData() {
                            const instanceSlug = '{{ $instance->instance_slug }}';
                            fetch(`/${instanceSlug}/monitor/api`)
                                .then(response => response.json())
                                .then(data => {
                                    // Jangan timpa currentCall jika sedang asyik memproses panggilan suara secara realtime
                                    if (!this.isPlaying && this.audioQueue.length === 0) {
                                        this.currentCall = data.current_call;
                                    }

                                    this.counters = data.counters;
                                    this.stats = data.counters_stats;
                                    this.operationalInfo = data.operational_info;
                                    window.dispatchEvent(new CustomEvent('update-media-contents', { detail: data.media_contents }));
                                })
                                .catch(error => console.error('Error fetching monitor data:', error));
                        }
                    }
                }
        </script>
</body>

</html>

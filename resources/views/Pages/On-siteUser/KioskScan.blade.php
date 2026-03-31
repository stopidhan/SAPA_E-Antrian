{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — kiosk-scan.blade.php
| Tahap Alternatif: Scan QR Code Online (Mesin Kiosk Layar Sentuh)
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scan QR — SAPA Kiosk</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Figtree', sans-serif }
        @keyframes scanLine {
            0%   { top: 10%; opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { top: 85%; opacity: 0; }
        }
        .scan-line {
            animation: scanLine 2.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="antialiased">

<div class="w-full min-h-screen bg-gradient-to-br from-blue-500 to-blue-700 flex flex-col items-center justify-center font-sans select-none relative pb-10">

    {{-- ====== DEKORASI LATAR ====== --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-white/5 rounded-full"></div>
    </div>

    {{-- ====== HEADER ====== --}}
    <div class="text-center mb-8 relative z-10">
        <div class="inline-flex items-center justify-center bg-white rounded-xl px-5 py-2 shadow-lg shadow-blue-900/20 mb-5">
            <span class="text-blue-600 text-lg font-black tracking-tight">SAPA</span>
        </div>
        <h1 class="text-white text-4xl font-extrabold tracking-tight mb-2">Validasi Tiket Online</h1>
        <p class="text-blue-100 text-lg">Scan QR Code dari aplikasi booking Anda</p>
    </div>

    {{-- ====== CARD UTAMA ====== --}}
    <div class="bg-white rounded-2xl shadow-2xl shadow-blue-900/30 w-full max-w-lg mx-6 overflow-hidden relative z-10">

        {{-- Judul --}}
        <div class="text-center pt-8 pb-4 px-8">
            <h2 class="text-xl font-bold text-gray-900">Scan QR Code Anda</h2>
        </div>

        {{-- Area Kamera (REAL) --}}
        <div class="px-8 pb-4">
            <div class="relative w-full aspect-square bg-gray-900 rounded-2xl overflow-hidden shadow-inner border-4 border-gray-100">
                
                {{-- Container untuk Library Scanner --}}
                <div id="reader" class="w-full h-full object-cover"></div>

                {{-- Overlay Scanning --}}
                <div id="scan-overlay" class="absolute inset-0 pointer-events-none z-10">
                    {{-- Corner Brackets --}}
                    <div class="absolute inset-0 p-6">
                        <div class="absolute top-6 left-6 w-12 h-12 border-t-4 border-l-4 border-emerald-400 rounded-tl-lg"></div>
                        <div class="absolute top-6 right-6 w-12 h-12 border-t-4 border-r-4 border-emerald-400 rounded-tr-lg"></div>
                        <div class="absolute bottom-6 left-6 w-12 h-12 border-b-4 border-l-4 border-emerald-400 rounded-bl-lg"></div>
                        <div class="absolute bottom-6 right-6 w-12 h-12 border-b-4 border-r-4 border-emerald-400 rounded-br-lg"></div>
                    </div>

                    {{-- Garis Scanning --}}
                    <div class="scan-line absolute left-6 right-6 h-0.5">
                        <div class="w-full h-full bg-emerald-400 rounded-full shadow-[0_0_15px_rgba(52,211,153,0.8)]"></div>
                    </div>
                </div>

                {{-- Status Overlay (Success/Error) --}}
                <div id="status-overlay" class="absolute inset-0 z-20 hidden flex items-center justify-center bg-white/90 backdrop-blur-sm transition-all duration-300">
                    <div class="text-center p-6">
                        <div id="status-icon" class="w-24 h-24 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <!-- Icon will be injected here -->
                        </div>
                        <h3 id="status-title" class="text-2xl font-black mb-2"></h3>
                        <p id="status-message" class="text-gray-500 font-medium"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instruksi --}}
        <div class="px-8 pb-6 text-center">
            <div id="instruction-box" class="bg-blue-50 border border-blue-100 rounded-xl px-5 py-4 flex items-start gap-3 transition-colors">
                <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                <p id="instruction-text" class="text-sm text-blue-700 leading-relaxed text-left">Arahkan <strong>QR Code</strong> di HP Anda ke arah kamera untuk Check-in otomatis.</p>
            </div>
        </div>

        {{-- Tombol Kembali --}}
        <div class="px-8 pb-8">
            <a href="{{ route('kiosk.home') }}"
               class="w-full flex items-center justify-center gap-2 py-4 border-2 border-gray-200 text-gray-600 text-base font-bold rounded-xl hover:bg-gray-50 active:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                Kembali ke Halaman Utama
            </a>
        </div>
    </div>

    {{-- ====== WATERMARK ====== --}}
    <div class="absolute bottom-4 text-center w-full">
        <p class="text-blue-200/50 text-xs">SAPA E-Antrian &middot; Kiosk Self-Service</p>
    </div>

</div>

{{-- LIBRARY SCANNER --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    let html5QrCode;
    let isProcessing = false;

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        
        isProcessing = true;
        showStatus('loading', 'Memverivikasi...', decodedText);

        // Kirim hasil scan ke Backend via AJAX (Jalur Relatif agar tidak 404)
        fetch("/on-site-user/verify-scan", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ qr_data: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showStatus('success', 'Berhasil!', `${data.queue_number} - ${data.service_name}`);
                playAudio('success');
            } else {
                showStatus('error', 'Gagal', data.message);
                playAudio('error');
            }
            
            // Tunggu 3 detik sebelum siap scan lagi
            setTimeout(() => {
                hideStatus();
                isProcessing = false;
            }, 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            showStatus('error', 'Sistem Error', 'Gagal terhubung ke server.');
            setTimeout(() => {
                hideStatus();
                isProcessing = false;
            }, 3000);
        });
    }

    function showStatus(type, title, message) {
        const overlay = document.getElementById('status-overlay');
        const iconContainer = document.getElementById('status-icon');
        const titleEl = document.getElementById('status-title');
        const msgEl = document.getElementById('status-message');
        
        overlay.classList.remove('hidden');
        titleEl.innerText = title;
        msgEl.innerText = message;

        if (type === 'loading') {
            iconContainer.innerHTML = '<div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>';
            titleEl.className = "text-2xl font-black mb-2 text-blue-600";
        } else if (type === 'success') {
            iconContainer.innerHTML = '<div class="bg-emerald-100 p-4 rounded-full"><svg class="w-16 h-16 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>';
            titleEl.className = "text-2xl font-black mb-2 text-emerald-600";
        } else {
            iconContainer.innerHTML = '<div class="bg-red-100 p-4 rounded-full"><svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg></div>';
            titleEl.className = "text-2xl font-black mb-2 text-red-600";
        }
    }

    function hideStatus() {
        document.getElementById('status-overlay').classList.add('hidden');
    }

    function playAudio(type) {
        const audio = new Audio(type === 'success' 
            ? 'https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3' 
            : 'https://assets.mixkit.co/active_storage/sfx/1437/1437-preview.mp3');
        audio.play().catch(e => console.log('Audio autoplay blocked by browser.'));
    }

    // Inisialisasi Scanner saat halaman siap
    window.addEventListener('DOMContentLoaded', () => {
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start({ facingMode: "user" }, config, onScanSuccess)
            .catch(err => {
                console.error("Gagal menyalakan kamera:", err);
                document.getElementById('instruction-text').innerHTML = "<span class='text-red-600 font-bold'>Gagal membuka kamera. Harap izinkan akses kamera di browser Anda.</span>";
                document.getElementById('instruction-box').classList.replace('bg-blue-50', 'bg-red-50');
            });
    });
</script>

</body>
</html>

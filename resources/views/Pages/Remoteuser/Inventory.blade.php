{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — inventory.blade.php
| Halaman Tiket Tersimpan (Inventory QR Code)
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tiket Tersimpan — SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="bg-gray-200 antialiased">

@php
    $savedTickets = collect($savedTickets ?? []);
@endphp

<div class="w-full max-w-screen-2xl mx-auto min-h-screen bg-gray-50 relative flex flex-col">

    {{-- ====== TOP BAR ====== --}}
    <nav class="bg-white sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-2.5 px-4 py-3">
            <a href="{{ route('booking.dashboard') }}"
               class="p-1.5 -ml-1.5 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <h1 class="text-sm font-bold text-gray-900">Tiket Tersimpan</h1>
        </div>
    </nav>

    {{-- ====== CONTENT ====== --}}
    <div class="flex-1 px-4 sm:px-5 py-4">

        {{-- Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
            <div class="flex-1 bg-white border border-gray-100 rounded-lg px-2.5 py-2.5 text-center shadow-sm">
                <p class="text-sm font-black text-emerald-600">{{ collect($savedTickets)->where('status', 'Menunggu')->count() }}</p>
                <p class="text-[9px] text-gray-400 font-medium">Menunggu</p>
            </div>
            <div class="flex-1 bg-white border border-gray-100 rounded-lg px-2.5 py-2.5 text-center shadow-sm">
                <p class="text-sm font-black text-blue-600">{{ count($savedTickets) }}</p>
                <p class="text-[9px] text-gray-400 font-medium">Total Tiket</p>
            </div>
        </div>

        {{-- Info --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl px-3 py-2.5 flex items-start gap-2 mb-4">
            <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            <p class="text-[11px] text-blue-700 leading-relaxed">Buka QR Code dari tiket di bawah, lalu scan di mesin <strong>Kiosk</strong> saat tiba di instansi.</p>
        </div>

        {{-- Ticket List --}}
        @if(count($savedTickets) > 0)
        <div class="space-y-4">
            @foreach($savedTickets as $ticket)
            <div id="ticket-card-{{ $ticket->kode }}" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Status Banner --}}
                <div class="flex items-center justify-between px-3.5 py-2 {{ $ticket->warnaLight }} border-b {{ $ticket->warnaBorder }}">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 {{ $ticket->warnaText }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[10px] font-semibold {{ $ticket->warnaText }}">Tersimpan</span>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/80 text-[9px] font-semibold {{ $ticket->isExpired ? 'text-red-600 border-red-200' : 'text-amber-600 border-amber-200' }} rounded-full border">
                        <span class="w-1.5 h-1.5 {{ $ticket->isExpired ? 'bg-red-400' : 'bg-amber-400 animate-pulse' }} rounded-full"></span> {{ $ticket->status }}
                    </span>
                </div>

                {{-- Countdown Timer --}}
                <div class="px-3.5 pt-2.5 pb-0" x-data="countdown('{{ $ticket->batasWaktu }}')" x-init="startTimer()">
                    <div class="flex items-center justify-between bg-amber-50 border border-amber-100 rounded-lg px-2.5 py-1.5">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-[10px] font-semibold text-amber-700" x-show="!expired">Sisa waktu datang</span>
                            <span class="text-[10px] font-bold text-red-600" x-show="expired" x-cloak>Tiket hangus!</span>
                        <div class="w-1.5 h-1.5 rounded-full {{ $ticket->isExpired ? 'bg-red-500' : 'bg-emerald-500 animate-pulse' }}"></div>
                        <p class="text-[10px] font-bold {{ $ticket->warnaTeks }}">{{ $ticket->statusLabel }}</p>
                    </div>
                    <p class="text-[10px] font-medium text-gray-400 capitalize">{{ $ticket->type }}</p>
                </div>

                {{-- Capture Area Starts Here --}}
                <div class="bg-white">
                    {{-- Countdown Timer --}}
                    <div class="px-3.5 pt-3.5 pb-0" x-data="countdown('{{ $ticket->batasWaktu }}')" x-init="startTimer()">
                        <div class="bg-amber-50 border border-amber-100 rounded-lg px-3 py-1.5 flex items-center justify-between">
                            <p class="text-[9px] font-bold text-amber-800">Batas Waktu</p>
                            <div class="flex gap-1">
                                <span class="text-[11px] font-black text-amber-700" x-text="`${hours}:${minutes}:${seconds}`">00:00:00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Ticket Content --}}
                    <div id="ticket-clean-capture-{{ $ticket->kode }}" class="p-3.5 bg-white">
                        <div class="flex items-center gap-3">
                            {{-- Mini QR --}}
                            <div class="shrink-0">
                                <div class="w-20 h-20 bg-white rounded-xl border-2 border-gray-100 flex items-center justify-center overflow-hidden p-1.5 shadow-sm">
                                    {!! QrCode::size(72)->generate($ticket->kode) !!}
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <div class="w-5 h-5 {{ $ticket->warnaBg }} rounded flex items-center justify-center">
                                        <span class="text-white text-[9px] font-black">{{ $ticket->kodeHuruf }}</span>
                                    </div>
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $ticket->layanan }}</p>
                                </div>
                                <p class="text-[10px] text-gray-400 font-bold tracking-wider">{{ $ticket->nomor }} &middot; {{ $ticket->kode }}</p>
                                <p class="text-[10px] text-gray-400">{{ $ticket->tanggal }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons (EXCLUDED) --}}
                <div class="px-3.5 pb-3.5 pt-0 no-capture">
                    <div class="flex flex-col sm:flex-row items-center gap-2 mt-3">
                        <a href="{{ route('booking.tiket', ['queue_id' => $ticket->queueId]) }}"
                           class="flex-1 flex items-center justify-center gap-1.5 py-2 text-white text-[11px] font-bold rounded-lg transition {{ $ticket->isExpired ? 'bg-gray-300 pointer-events-none cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700' }}">
                            {{ $ticket->isExpired ? 'Tiket Hangus' : 'Buka QR' }}
                        </a>
                        <button onclick="downloadTicketCard('{{ $ticket->kode }}')"
                                class="flex-1 flex items-center justify-center gap-1.5 py-2 border-2 border-gray-200 text-gray-600 text-[11px] font-bold rounded-lg transition {{ $ticket->isExpired ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50' }}" {{ $ticket->isExpired ? 'disabled' : '' }}>
                            Unduh QR
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/></svg>
            </div>
            <p class="text-sm font-bold text-gray-900 mb-1">Belum Ada Tiket</p>
            <p class="text-[11px] text-gray-400 leading-relaxed">Tiket yang sudah dikonfirmasi akan muncul di sini.</p>
            <a href="{{ route('booking.dashboard') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition">
                Pilih Layanan
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
        </div>
        @endif

        {{-- Cara Check-in --}}
        <div class="mt-4 bg-blue-50 border border-blue-100 rounded-xl p-3.5">
            <div class="flex items-center gap-2 mb-2.5">
                <div class="w-6 h-6 bg-blue-600 rounded-md flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/></svg>
                </div>
                <p class="text-xs font-bold text-blue-900">Cara Check-in di Kiosk</p>
            </div>
            <div class="space-y-2">
                <div class="flex items-start gap-2">
                    <span class="w-4.5 h-4.5 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5 w-5 h-5">1</span>
                    <p class="text-[11px] text-blue-800 leading-relaxed">Datang ke instansi pada hari yang ditentukan</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5">2</span>
                    <p class="text-[11px] text-blue-800 leading-relaxed">Buka QR dari halaman ini atau unduh gambarnya</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5">3</span>
                    <p class="text-[11px] text-blue-800 leading-relaxed">Arahkan QR ke mesin <strong>Kiosk Scanner</strong> untuk check-in</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-4">
            <div class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2.5 text-center">
                <p class="text-[10px] text-gray-400">Menampilkan {{ count($savedTickets) }} tiket tersimpan</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function downloadTicketCard(kode) {
        const area = document.getElementById(`ticket-clean-capture-${kode}`);
        
        html2canvas(area, {
            scale: 2,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = `Tiket-SAPA-${kode}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        }).catch(err => {
            console.error('Download failed:', err);
            alert('Gagal mengunduh gambar. Silakan coba lagi.');
        });
    }

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

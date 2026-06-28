{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — Dashboard.blade.php
| Revisi M-Paspor: Pilih Layanan → Modal Tanggal & Slot Waktu → Konfirmasi
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Layanan — SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important }
        body { font-family: 'Figtree', sans-serif }
    </style>
</head>

<body class="bg-gray-200 antialiased">

    <div class="w-full max-w-screen-2xl mx-auto min-h-screen bg-gray-50 relative flex flex-col"
         x-data="slotPicker(
             '{{ route('booking.api.slots') }}',
             '{{ route('booking.konfirmasi') }}'
         )">

        {{-- ====== TOP BAR ====== --}}
        <nav class="bg-white sticky top-0 z-30 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-5 py-3.5">
                <div class="flex items-center gap-2.5 min-w-0 w-full sm:w-auto">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                        <span class="text-white text-[10px] font-black">SAPA</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400 leading-none">Selamat datang</p>
                        <p class="text-sm font-bold text-gray-900 truncate">{{ $namaUser }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0 w-full sm:w-auto justify-end flex-wrap sm:flex-nowrap">
                    <a href="{{ route('booking.inventory') }}"
                        class="flex items-center gap-1 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1.5 rounded-lg hover:bg-emerald-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                        </svg>
                        Tiket
                    </a>
                    <a href="{{ route('booking.riwayat') }}"
                        class="flex items-center gap-1 text-[11px] font-semibold text-blue-600 bg-blue-50 px-2.5 py-1.5 rounded-lg hover:bg-blue-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Riwayat
                    </a>
                    <form method="POST" action="{{ route('booking.logout') }}" class="inline-flex">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-1 text-[11px] font-semibold text-red-600 bg-red-50 px-2.5 py-1.5 rounded-lg hover:bg-red-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        {{-- ====== ERROR MESSAGES ====== --}}
        @if ($errors->any())
            <div class="mx-4 sm:mx-5 mt-4">
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-2xl px-4 py-3.5">
                    <span class="text-base shrink-0">⚠️</span>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="text-[13px] text-red-800 leading-snug">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="flex-1 px-4 sm:px-5 pt-5 pb-8">

            {{-- ====== STATE 1: ADA ANTREAN AKTIF ====== --}}
            @if ($hasActiveQueue)
                <div class="mb-5 space-y-3">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 shadow-lg shadow-blue-200/50">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-blue-200 text-[10px] font-semibold uppercase tracking-widest">Antrean Aktif</p>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 text-white text-[10px] font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 bg-yellow-300 rounded-full animate-pulse"></span> Menunggu
                            </span>
                        </div>
                        <p class="text-white text-3xl font-black tracking-tight mb-1">{{ $nomorAntrean }}</p>
                        @if ($activeQueue->scheduled_slot && $activeQueue->scheduled_date)
                            <p class="text-blue-200 text-xs mb-3">
                                {{ $activeQueue->service->service_name ?? 'Layanan' }} &middot;
                                Slot: {{ \Carbon\Carbon::createFromFormat('H:i', $activeQueue->scheduled_slot)->format('H:i') }} —
                                Tiba sebelum batas waktu!
                            </p>
                        @else
                            <p class="text-blue-200 text-xs mb-3">{{ $activeQueue->service->service_name ?? 'Layanan' }} &middot; Segera datang ke lokasi</p>
                        @endif

                        {{-- Countdown Timer --}}
                        @php
                            $expiry = $activeQueue->scheduled_date && $activeQueue->scheduled_slot
                                ? \Carbon\Carbon::parse($activeQueue->scheduled_date->toDateString() . ' ' . $activeQueue->scheduled_slot)->addMinutes(30)->toIso8601String()
                                : now()->addMinutes(30)->toIso8601String();
                        @endphp
                        <div class="bg-white/15 rounded-xl px-3 py-2.5 mb-3" x-data="countdown('{{ $expiry }}')" x-init="startTimer()">
                            <p class="text-[10px] text-blue-200 font-semibold text-center mb-1.5" x-show="!expired">Segera menuju instansi dalam</p>
                            <p class="text-[10px] text-red-300 font-bold text-center mb-1.5" x-show="expired" x-cloak>Waktu kedatangan habis!</p>
                            <div class="flex items-center justify-center gap-1.5" x-show="!expired">
                                <div class="bg-white/20 rounded-lg px-2 py-1 min-w-[34px] text-center">
                                    <p class="text-sm font-black text-white" x-text="hours">00</p>
                                    <p class="text-[7px] text-blue-200 font-semibold -mt-0.5">Jam</p>
                                </div>
                                <span class="text-white/50 font-black text-sm">:</span>
                                <div class="bg-white/20 rounded-lg px-2 py-1 min-w-[34px] text-center">
                                    <p class="text-sm font-black text-white" x-text="minutes">00</p>
                                    <p class="text-[7px] text-blue-200 font-semibold -mt-0.5">Mnt</p>
                                </div>
                                <span class="text-white/50 font-black text-sm">:</span>
                                <div class="bg-white/20 rounded-lg px-2 py-1 min-w-[34px] text-center">
                                    <p class="text-sm font-black" :class="seconds <= 10 ? 'text-red-300' : 'text-white'" x-text="seconds">00</p>
                                    <p class="text-[7px] text-blue-200 font-semibold -mt-0.5">Dtk</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('booking.tiket') }}"
                            class="flex items-center justify-center gap-2 w-full py-2.5 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition">
                            <span>👁️</span> Lihat Tiket & QR Code
                        </a>
                    </div>

                    {{-- Mini QR Preview --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border-b border-emerald-100">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-[11px] font-semibold text-emerald-700">Tiket Tersimpan — Siap Scan di Kiosk</p>
                        </div>
                        <div class="p-4 flex items-center gap-4">
                            <a href="{{ route('booking.tiket') }}" class="shrink-0">
                                <div class="w-28 h-28 bg-white rounded-2xl border-2 border-gray-100 flex items-center justify-center hover:border-blue-300 transition overflow-hidden p-2 shadow-sm">
                                    {!! QrCode::size(100)->generate($kodeBooking) !!}
                                </div>
                            </a>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-900 mb-0.5">{{ $nomorAntrean }}</p>
                                <p class="text-[11px] text-gray-400 mb-2">BKG: {{ $kodeBooking }}</p>
                                <a href="{{ route('booking.tiket') }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold rounded-lg transition">
                                    Buka QR untuk Scan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                        <span class="shrink-0 text-sm">⚠️</span>
                        <p class="text-[11px] text-red-700 leading-relaxed">Anda memiliki antrean aktif yang sedang berjalan. <strong>Selesaikan pelayanan Anda di loket terlebih dahulu</strong> sebelum mengambil antrean baru.</p>
                    </div>
                </div>
            @endif

            {{-- Info Limit Harian Habis --}}
            @if ($bookingTodayCount >= 2)
                <div class="mb-5 flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 shadow-sm">
                    <span class="shrink-0 text-sm">ℹ️</span>
                    <p class="text-[11px] text-amber-800 leading-relaxed">
                        Limit antrean harian Anda hari ini telah habis (**Maksimal 2 antrean per hari**). Silakan kembali esok hari.
                    </p>
                </div>
            @endif

            {{-- ====== HEADER PILIH LAYANAN ====== --}}
            <div class="mb-4">
                <h2 class="text-base font-bold text-gray-900">Pilih Layanan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pilih layanan, lalu tentukan jadwal dan slot waktu kedatangan Anda.</p>
            </div>

            {{-- ====== DAFTAR LAYANAN (ACCORDION) ====== --}}
            @php
                $isDisabled = $hasActiveQueue || ($bookingTodayCount >= 2);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($layanans as $svc)
                    @php
                        $prefix = strtoupper($svc->queue_prefix);
                        $c      = $colorMap[$prefix] ?? $defaultColor;
                        $slug   = Str::slug($svc->service_name);
                    @endphp

                    <div x-data="{ expanded: false }"
                        class="bg-white rounded-2xl border {{ $c['border'] }} shadow-sm {{ $c['shadow'] }} overflow-hidden transition-all duration-300 {{ $isDisabled ? 'opacity-60 grayscale-[30%] pointer-events-none' : '' }}">

                        {{-- Header Card --}}
                        <div class="flex items-center gap-3.5 p-4 pb-3">
                            <div class="w-11 h-11 {{ $c['bg'] }} rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                                <span class="text-white text-lg font-black">{{ $svc->queue_prefix }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-gray-900">{{ $svc->service_name }}</h3>
                                <p class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Slot {{ $svc->_slot_duration }} menit · Maks. {{ $svc->_slot_capacity }} orang/slot
                                </p>
                            </div>
                        </div>

                        {{-- Toggle Accordion --}}
                        <button @click="expanded = !expanded"
                            class="flex items-center justify-between w-full px-4 py-2.5 border-t border-gray-100 hover:bg-gray-50 transition group">
                            <span class="flex items-center gap-1.5 text-xs {{ $c['text'] }} font-medium">
                                <span>📄</span> Lihat Detail Layanan
                            </span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-300 group-hover:text-gray-600"
                                :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        {{-- Isi Accordion --}}
                        <div x-show="expanded" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="px-4 pb-4 space-y-4 border-t border-gray-100">

                            <div class="pt-3">
                                <p class="text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">Deskripsi</p>
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    {{ $svc->description ?: 'Layanan pendaftaran antrean untuk ' . $svc->service_name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-2">Persyaratan Umum</p>
                                <ul class="space-y-1.5">
                                    <li class="flex items-start gap-2">
                                        <svg class="w-3.5 h-3.5 {{ $c['text'] }} shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        <span class="text-xs text-gray-600">Warga Negara Indonesia (WNI)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-3.5 h-3.5 {{ $c['text'] }} shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        <span class="text-xs text-gray-600">Terdaftar di wilayah instansi terkait</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Tombol Pilih Layanan → Buka Modal Slot --}}
                        <div class="px-4 pb-4">
                            <button
                                @click="openModal('{{ $slug }}', '{{ $svc->service_name }}')"
                                class="flex items-center justify-center gap-1.5 w-full py-3 {{ $c['btnBg'] }} text-white text-xs font-bold rounded-xl shadow-sm transition">
                                Pilih Jadwal & Slot Waktu
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Info Footer --}}
            <div class="mt-5">
                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex items-start gap-2">
                    <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <p class="text-[11px] text-blue-700 leading-relaxed">
                        Pilih layanan, tentukan <strong>tanggal</strong> dan <strong>slot waktu</strong> kedatangan, lalu datangi instansi sesuai jadwal.
                        Tiket akan hangus jika tidak check-in dalam 30 menit setelah waktu slot dimulai.
                    </p>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- MODAL: PILIH TANGGAL & SLOT WAKTU (Alpine.js)                   --}}
        {{-- ================================================================ --}}
        <div x-show="modalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40" @click="closeModal()"></div>

            {{-- Modal Panel --}}
            <div class="relative bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-y-full sm:translate-y-4 sm:opacity-0"
                x-transition:enter-end="translate-y-0 sm:opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-y-0 sm:opacity-100"
                x-transition:leave-end="translate-y-full sm:translate-y-4 sm:opacity-0">

                {{-- Handle (mobile) --}}
                <div class="flex justify-center pt-3 pb-1 sm:hidden">
                    <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
                </div>

                {{-- Header Modal --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <p class="text-xs text-gray-400">Booking Antrean</p>
                        <h3 class="text-sm font-bold text-gray-900" x-text="selectedServiceName">Pilih Jadwal</h3>
                    </div>
                    <button @click="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5">

                    {{-- Step 1: Pilih Tanggal --}}
                    <div>
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white text-[10px] font-black rounded-full mr-1.5">1</span>
                            Pilih Tanggal
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($availableDates as $dateOpt)
                                <button
                                    @click="selectDate('{{ $dateOpt['value'] }}')"
                                    :class="selectedDate === '{{ $dateOpt['value'] }}'
                                        ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-100'
                                        : 'bg-white text-gray-700 border-gray-200 hover:border-blue-300 hover:bg-blue-50'"
                                    class="flex flex-col items-center justify-center py-3 px-4 rounded-xl border-2 transition font-semibold text-xs">
                                    <span class="text-sm font-black">{{ $dateOpt['label'] }}</span>
                                    <span class="text-[10px] opacity-70">{{ $dateOpt['sublabel'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Step 2: Pilih Slot Waktu --}}
                    <div x-show="selectedDate" x-cloak>
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white text-[10px] font-black rounded-full mr-1.5">2</span>
                            Pilih Slot Waktu
                        </p>

                        {{-- Loading --}}
                        <div x-show="loadingSlots" class="flex items-center justify-center py-8 gap-2">
                            <svg class="animate-spin w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <p class="text-xs text-gray-400">Memuat slot tersedia...</p>
                        </div>

                        {{-- Slot Grid --}}
                        <div x-show="!loadingSlots && slots.length > 0" class="grid grid-cols-2 gap-2">
                            <template x-for="slot in slots" :key="slot.slot">
                                <button
                                    @click="!slot.full && selectSlot(slot.slot)"
                                    :disabled="slot.full"
                                    :class="{
                                        'bg-blue-600 text-white border-blue-600 shadow-md': selectedSlot === slot.slot,
                                        'bg-white text-gray-700 border-gray-200 hover:border-blue-300 hover:bg-blue-50': selectedSlot !== slot.slot && !slot.full,
                                        'bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed': slot.full
                                    }"
                                    class="flex flex-col items-center py-3 px-2 rounded-xl border-2 transition text-xs font-semibold">
                                    <span class="font-black text-sm" x-text="slot.display || slot.slot"></span>
                                    <span class="text-[10px] mt-0.5 opacity-70"
                                        :class="slot.full ? 'text-red-400 opacity-100' : ''"
                                        x-text="slot.full ? 'Penuh' : 'Sisa ' + slot.sisa + ' tempat'"></span>
                                </button>
                            </template>
                        </div>

                        {{-- No Slots Available --}}
                        <div x-show="!loadingSlots && slots.length === 0" class="text-center py-6">
                            <p class="text-sm text-gray-400">Tidak ada slot tersedia untuk tanggal ini.</p>
                            <p class="text-xs text-gray-300 mt-1">Coba pilih tanggal lain.</p>
                        </div>
                    </div>
                </div>

                {{-- Footer Modal —  Tombol Lanjut --}}
                <div class="px-5 py-4 border-t border-gray-100 bg-white">
                    <button
                        @click="proceedToKonfirmasi()"
                        :disabled="!selectedDate || !selectedSlot"
                        :class="(!selectedDate || !selectedSlot) ? 'opacity-40 cursor-not-allowed bg-blue-400' : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 shadow-md shadow-blue-100'"
                        class="w-full py-3 text-white text-sm font-bold rounded-xl transition flex items-center justify-center gap-2">
                        Lanjut ke Konfirmasi
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // =====================================================================
        // Alpine.js: Slot Picker (Modal Tanggal & Slot Waktu)
        // =====================================================================
        function slotPicker(apiUrl, konfirmasiUrl) {
            return {
                modalOpen: false,
                selectedServiceSlug: '',
                selectedServiceName: '',
                selectedDate: '',
                selectedSlot: '',
                slots: [],
                loadingSlots: false,

                openModal(slug, name) {
                    this.selectedServiceSlug = slug;
                    this.selectedServiceName = name;
                    this.selectedDate = '';
                    this.selectedSlot = '';
                    this.slots = [];
                    this.modalOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.modalOpen = false;
                    document.body.style.overflow = '';
                },

                async selectDate(date) {
                    this.selectedDate = date;
                    this.selectedSlot = '';
                    this.slots = [];
                    await this.fetchSlots();
                },

                selectSlot(slot) {
                    this.selectedSlot = slot;
                },

                async fetchSlots() {
                    if (!this.selectedDate || !this.selectedServiceSlug) return;
                    this.loadingSlots = true;
                    try {
                        const params = new URLSearchParams({
                            service_slug: this.selectedServiceSlug,
                            date: this.selectedDate,
                        });
                        const res = await fetch(`${apiUrl}?${params}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        });
                        const data = await res.json();
                        this.slots = data.success ? data.slots : [];
                    } catch (e) {
                        console.error('Gagal memuat slot:', e);
                        this.slots = [];
                    } finally {
                        this.loadingSlots = false;
                    }
                },

                proceedToKonfirmasi() {
                    if (!this.selectedDate || !this.selectedSlot) return;
                    const url = new URL(konfirmasiUrl);
                    url.searchParams.set('layanan', this.selectedServiceSlug);
                    url.searchParams.set('tanggal', this.selectedDate);
                    url.searchParams.set('slot', this.selectedSlot);
                    window.location.href = url.toString();
                },
            };
        }

        // =====================================================================
        // Countdown Timer
        // =====================================================================
        function countdown(deadline) {
            return {
                hours: '00', minutes: '00', seconds: '00',
                expired: false, interval: null,
                startTimer() {
                    const end = new Date(deadline).getTime();
                    this.update(end);
                    this.interval = setInterval(() => this.update(end), 1000);
                },
                update(end) {
                    const diff = end - Date.now();
                    if (diff <= 0) {
                        this.hours = '00'; this.minutes = '00'; this.seconds = '00';
                        this.expired = true;
                        clearInterval(this.interval);
                        return;
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

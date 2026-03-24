{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — dashboard.blade.php
| Tahap 2: Pilih Layanan & Accordion
|--------------------------------------------------------------------------
| State 1: Ada antrean aktif → tiket mini + layanan disabled
| State 2: Tidak ada antrean → layanan bisa diklik
| Accordion: Alpine.js x-data="{ expanded: false }"
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
    <style>[x-cloak]{display:none!important}body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="bg-gray-200 antialiased">

@php
    // Simulasi state — ubah ke true untuk melihat state "terkunci"
    $hasActiveQueue = false;
    $namaUser = session('nama', 'Khairuddin Al Fadhilah');
    $nomorAntrean = 'B-007';

    $services = [
        (object)[
            'kode'       => 'A',
            'nama'       => 'Pelayanan KTP',
            'estimasi'   => '± 15 menit',
            'kuota'      => 50,
            'terisi'     => 34,
            'warna'      => 'blue',
            'bg'         => 'bg-blue-600',
            'bgLight'    => 'bg-blue-50',
            'border'     => 'border-blue-200',
            'text'       => 'text-blue-600',
            'ring'       => 'ring-blue-100',
            'btnBg'      => 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800',
            'shadow'     => 'shadow-blue-100',
            'deskripsi'  => 'Pelayanan pembuatan dan perpanjangan Kartu Tanda Penduduk (KTP) elektronik untuk WNI yang sudah memenuhi syarat.',
            'syarat'     => ['WNI berusia minimal 17 tahun atau sudah menikah', 'Terdaftar di Dinas Kependudukan setempat', 'Belum pernah memiliki KTP atau KTP sudah habis masa berlaku'],
            'dokumen'    => ['Kartu Keluarga (KK) asli', 'Akta Kelahiran asli', 'Surat pengantar dari RT/RW'],
            'alur'       => ['Ambil nomor antrean', 'Verifikasi dokumen di loket', 'Foto & rekam sidik jari', 'Tunggu proses cetak'],
        ],
        (object)[
            'kode'       => 'B',
            'nama'       => 'Pelayanan KK',
            'estimasi'   => '± 20 menit',
            'kuota'      => 40,
            'terisi'     => 28,
            'warna'      => 'emerald',
            'bg'         => 'bg-emerald-600',
            'bgLight'    => 'bg-emerald-50',
            'border'     => 'border-emerald-200',
            'text'       => 'text-emerald-600',
            'ring'       => 'ring-emerald-100',
            'btnBg'      => 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800',
            'shadow'     => 'shadow-emerald-100',
            'deskripsi'  => 'Pelayanan pembuatan, perubahan, dan pembaharuan Kartu Keluarga (KK) untuk menyesuaikan data kependudukan.',
            'syarat'     => ['Merupakan kepala keluarga atau anggota keluarga', 'Memiliki dokumen pendukung yang sah', 'Mengisi formulir permohonan KK'],
            'dokumen'    => ['KTP elektronik asli', 'Akta Nikah / Akta Cerai (jika ada perubahan)', 'Surat keterangan pindah (jika pindah domisili)'],
            'alur'       => ['Ambil nomor antrean', 'Serahkan dokumen di loket', 'Verifikasi data oleh petugas', 'KK dicetak & diserahkan'],
        ],
        (object)[
            'kode'       => 'C',
            'nama'       => 'Pelayanan Akta',
            'estimasi'   => '± 30 menit',
            'kuota'      => 30,
            'terisi'     => 18,
            'warna'      => 'amber',
            'bg'         => 'bg-amber-500',
            'bgLight'    => 'bg-amber-50',
            'border'     => 'border-amber-200',
            'text'       => 'text-amber-600',
            'ring'       => 'ring-amber-100',
            'btnBg'      => 'bg-amber-500 hover:bg-amber-600 active:bg-amber-700',
            'shadow'     => 'shadow-amber-100',
            'deskripsi'  => 'Pelayanan penerbitan Akta Kelahiran, Akta Kematian, dan Akta Perkawinan sesuai regulasi Dinas Kependudukan.',
            'syarat'     => ['Pelaporan maksimal 60 hari setelah peristiwa', 'Memiliki saksi minimal 2 orang', 'Data pelapor sudah tercatat di Disdukcapil'],
            'dokumen'    => ['KTP & KK asli pelapor', 'Surat keterangan lahir/meninggal dari RS atau Desa', 'Fotokopi KTP 2 orang saksi'],
            'alur'       => ['Ambil nomor antrean', 'Pengisian formulir di loket', 'Verifikasi & tanda tangan', 'Akta dicetak & dilegalisir'],
        ],
    ];
@endphp

<div class="w-full max-w-screen-2xl mx-auto min-h-screen bg-gray-50 relative flex flex-col">

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
                <a href="{{ route('booking.inventory') }}" class="flex items-center gap-1 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1.5 rounded-lg hover:bg-emerald-100 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/></svg>
                    Tiket
                </a>
                <a href="{{ route('booking.riwayat') }}" class="flex items-center gap-1 text-[11px] font-semibold text-blue-600 bg-blue-50 px-2.5 py-1.5 rounded-lg hover:bg-blue-100 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat
                </a>
            </div>
        </div>
    </nav>

    {{-- ====== ALERT SUKSES ====== --}}
    @if(!$hasActiveQueue)
    <div class="mx-5 mt-4">
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-3.5">
            <svg class="w-6 h-6 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-[13px] text-emerald-800 leading-snug"><strong>Selamat Datang, {{ $namaUser }}!</strong> Akun Anda berhasil terdaftar.</p>
        </div>
    </div>
    @endif

    <div class="flex-1 px-4 sm:px-5 pt-5 pb-8">

        {{-- ====== STATE 1: ANTREAN AKTIF (TERKUNCI) ====== --}}
        @if($hasActiveQueue)
        <div class="mb-5 space-y-3">

            {{-- Card Antrean Aktif --}}
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 shadow-lg shadow-blue-200/50">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-blue-200 text-[10px] font-semibold uppercase tracking-widest">Antrean Aktif</p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 text-white text-[10px] font-semibold rounded-full">
                        <span class="w-1.5 h-1.5 bg-yellow-300 rounded-full animate-pulse"></span> Menunggu
                    </span>
                </div>
                <p class="text-white text-3xl font-black tracking-tight mb-1">{{ $nomorAntrean }}</p>
                <p class="text-blue-200 text-xs mb-3">Pelayanan KK &middot; est. ± 20 menit</p>

                {{-- Countdown Timer --}}
                <div class="bg-white/15 rounded-xl px-3 py-2.5 mb-3" x-data="countdown('{{ now()->addMinutes(22)->toIso8601String() }}')" x-init="startTimer()">
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

                <a href="{{ route('booking.tiket') }}" class="flex items-center justify-center gap-2 w-full py-2.5 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition">
                    <span>👁️</span> Lihat Tiket & QR Code
                </a>
            </div>

            {{-- Card Tiket Tersimpan (Inventory) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border-b border-emerald-100">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-[11px] font-semibold text-emerald-700">Tiket Tersimpan — Siap Scan di Kiosk</p>
                </div>
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        {{-- Mini QR Preview --}}
                        <a href="{{ route('booking.tiket') }}" class="shrink-0">
                            <div class="w-20 h-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center hover:border-blue-300 transition">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="0.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>
                                </svg>
                            </div>
                        </a>
                        {{-- Detail --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 mb-0.5">{{ $nomorAntrean }}</p>
                            <p class="text-[11px] text-gray-400 mb-2">Kode: BKG-32624504</p>
                            <a href="{{ route('booking.tiket') }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/></svg>
                                Buka QR untuk Scan
                            </a>
                        </div>
                    </div>

                    {{-- Instruksi Check-in --}}
                    <div class="mt-3 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5">
                        <p class="text-[10px] font-semibold text-blue-700 uppercase tracking-wider mb-1.5">Cara Check-in di Kiosk:</p>
                        <ol class="space-y-1">
                            <li class="flex items-center gap-2 text-[11px] text-blue-600">
                                <span class="w-4 h-4 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center shrink-0">1</span>
                                Datang ke instansi tujuan
                            </li>
                            <li class="flex items-center gap-2 text-[11px] text-blue-600">
                                <span class="w-4 h-4 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center shrink-0">2</span>
                                Buka QR Code dari tiket tersimpan
                            </li>
                            <li class="flex items-center gap-2 text-[11px] text-blue-600">
                                <span class="w-4 h-4 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center shrink-0">3</span>
                                Scan QR di mesin Kiosk untuk check-in
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Warning --}}
            <div class="flex items-start gap-2 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                <span class="shrink-0 text-sm">⚠️</span>
                <p class="text-[11px] text-red-700 leading-relaxed">Anda memiliki antrean aktif. <strong>Selesaikan dulu</strong> sebelum mengambil nomor baru.</p>
            </div>
        </div>
        @endif

        {{-- ====== HEADER SECTION ====== --}}
        <div class="mb-4">
            <h2 class="text-base font-bold text-gray-900">Pilih Layanan</h2>
            <p class="text-xs text-gray-400 mt-0.5">Pilih layanan yang Anda butuhkan dan lihat detailnya</p>
        </div>

        {{-- ====== KUOTA HARI INI (GENERAL) ====== --}}
        @if(!$hasActiveQueue)
        @php
            $totalKuota = collect($services)->sum('kuota');
            $totalTerisi = collect($services)->sum('terisi');
            $sisaKuota = $totalKuota - $totalTerisi;
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-900">Kuota Antrean Hari Ini</p>
                </div>
                <span class="text-[10px] text-gray-400">{{ now()->format('d M Y') }}</span>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-2 mb-2">
                <div class="flex-1 bg-indigo-50 rounded-xl px-3 py-2.5 text-center">
                    <p class="text-lg font-black text-indigo-700">{{ $totalKuota }}</p>
                    <p class="text-[9px] text-gray-400 font-medium">Total Kuota</p>
                </div>
                <div class="flex-1 bg-emerald-50 rounded-xl px-3 py-2.5 text-center">
                    <p class="text-lg font-black text-emerald-600">{{ $sisaKuota }}</p>
                    <p class="text-[9px] text-gray-400 font-medium">Sisa Tersedia</p>
                </div>
                <div class="flex-1 bg-gray-50 rounded-xl px-3 py-2.5 text-center">
                    <p class="text-lg font-black text-gray-700">{{ $totalTerisi }}</p>
                    <p class="text-[9px] text-gray-400 font-medium">Terisi</p>
                </div>
            </div>
            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-indigo-600 rounded-full transition-all" style="width: {{ round(($totalTerisi / $totalKuota) * 100) }}%"></div>
            </div>
            <p class="text-[9px] text-gray-400 mt-1.5 text-right">{{ round(($totalTerisi / $totalKuota) * 100) }}% kuota terisi</p>
        </div>
        @endif

        {{-- ====== STATE 2: DAFTAR LAYANAN (ACCORDION) ====== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($services as $svc)
            <div x-data="{ expanded: false }"
                 class="bg-white rounded-2xl border {{ $svc->border }} shadow-sm {{ $svc->shadow }} overflow-hidden transition-all duration-300 {{ $hasActiveQueue ? 'opacity-50 pointer-events-none' : '' }}">

                {{-- Header Card --}}
                <div class="flex items-center gap-3.5 p-4 pb-3">
                    <div class="w-11 h-11 {{ $svc->bg }} rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                        <span class="text-white text-lg font-black">{{ $svc->kode }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900">{{ $svc->nama }}</h3>
                        <p class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $svc->estimasi }}
                        </p>
                    </div>
                </div>

                {{-- Toggle Accordion --}}
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full px-4 py-2.5 border-t border-gray-100 hover:bg-gray-50 transition group">
                    <span class="flex items-center gap-1.5 text-xs {{ $svc->text }} font-medium">
                        <span>📄</span> Lihat Detail Layanan
                    </span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-300 group-hover:text-gray-600"
                         :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </button>

                {{-- Isi Accordion --}}
                <div x-show="expanded" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="px-4 pb-4 space-y-4 border-t border-gray-100">

                    {{-- Deskripsi --}}
                    <div class="pt-3">
                        <p class="text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">Deskripsi</p>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $svc->deskripsi }}</p>
                    </div>

                    {{-- Persyaratan --}}
                    <div>
                        <p class="text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-2">Persyaratan</p>
                        <ul class="space-y-1.5">
                            @foreach($svc->syarat as $s)
                            <li class="flex items-start gap-2">
                                <svg class="w-3.5 h-3.5 {{ $svc->text }} shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span class="text-xs text-gray-600 leading-relaxed">{{ $s }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Dokumen --}}
                    <div>
                        <p class="text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-2">Dokumen yang Dibawa</p>
                        <ul class="space-y-1.5">
                            @foreach($svc->dokumen as $d)
                            <li class="flex items-start gap-2">
                                <span class="text-xs shrink-0">📋</span>
                                <span class="text-xs text-gray-600 leading-relaxed">{{ $d }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Alur Proses --}}
                    <div>
                        <p class="text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-2">Alur Proses</p>
                        <ol class="space-y-2">
                            @foreach($svc->alur as $i => $a)
                            <li class="flex items-center gap-2.5">
                                <span class="w-6 h-6 {{ $svc->bg }} text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                                <span class="text-xs text-gray-600">{{ $a }}</span>
                            </li>
                            @endforeach
                        </ol>
                    </div>
                </div>

                {{-- Tombol Pilih --}}
                <div class="px-4 pb-4">
                    <a href="{{ route('booking.konfirmasi', ['layanan' => strtolower(str_replace(' ', '-', $svc->nama))]) }}"
                       class="flex items-center justify-center gap-1.5 w-full py-3 {{ $svc->btnBg }} text-white text-xs font-bold rounded-xl shadow-sm transition {{ $hasActiveQueue ? 'pointer-events-none' : '' }}">
                        Pilih Layanan Ini
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Info --}}
        <div class="mt-5">
            <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex items-start gap-2">
                <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                <p class="text-[11px] text-blue-700 leading-relaxed">Kuota pendaftaran online di-reset setiap pukul <strong>00:00 WIB</strong>.</p>
            </div>
        </div>


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

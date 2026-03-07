{{--
|--------------------------------------------------------------------------
| SAPA E-Antrian — riwayat.blade.php
| Halaman Ekstra: History Kunjungan
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat — SAPA E-Antrian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="bg-gray-200 antialiased">

@php
    $riwayat = [
        (object)['tanggal' => '12 Feb 2026', 'layanan' => 'Pelayanan KTP',  'nomor' => 'A-011', 'kode' => 'A', 'status' => 'Selesai',    'warna' => 'blue',    'kodeBooking' => 'BKG-19283746', 'waktuDatang' => '08:24 WIB', 'waktuSelesai' => '08:42 WIB', 'loket' => 'Loket 2', 'petugas' => 'Siti Nurhaliza', 'alasanBatal' => null],
        (object)['tanggal' => '10 Feb 2026', 'layanan' => 'Konsultasi',     'nomor' => 'C-005', 'kode' => 'C', 'status' => 'Dibatalkan', 'warna' => 'amber',   'kodeBooking' => 'BKG-28374651', 'waktuDatang' => null,         'waktuSelesai' => null,          'loket' => null,     'petugas' => null,             'alasanBatal' => 'Tidak hadir (waktu habis)'],
        (object)['tanggal' => '03 Feb 2026', 'layanan' => 'Pelayanan KK',   'nomor' => 'B-019', 'kode' => 'B', 'status' => 'Selesai',    'warna' => 'emerald', 'kodeBooking' => 'BKG-37465182', 'waktuDatang' => '09:10 WIB', 'waktuSelesai' => '09:35 WIB', 'loket' => 'Loket 1', 'petugas' => 'Ahmad Fauzi',    'alasanBatal' => null],
        (object)['tanggal' => '28 Jan 2026', 'layanan' => 'Pelayanan Akta', 'nomor' => 'C-032', 'kode' => 'C', 'status' => 'Selesai',    'warna' => 'amber',   'kodeBooking' => 'BKG-46518293', 'waktuDatang' => '10:05 WIB', 'waktuSelesai' => '10:28 WIB', 'loket' => 'Loket 3', 'petugas' => 'Dewi Kartika',   'alasanBatal' => null],
        (object)['tanggal' => '15 Jan 2026', 'layanan' => 'Pelayanan KTP',  'nomor' => 'A-045', 'kode' => 'A', 'status' => 'Dibatalkan', 'warna' => 'blue',    'kodeBooking' => 'BKG-51829374', 'waktuDatang' => null,         'waktuSelesai' => null,          'loket' => null,     'petugas' => null,             'alasanBatal' => 'Dibatalkan oleh pengguna'],
    ];
@endphp

<div class="max-w-md mx-auto min-h-screen bg-gray-50 relative flex flex-col">

    {{-- ====== TOP BAR ====== --}}
    <nav class="bg-white sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-2.5 px-4 py-3">
            <a href="{{ route('booking.dashboard') }}"
               class="p-1.5 -ml-1.5 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <h1 class="text-sm font-bold text-gray-900">Riwayat Antrean Saya</h1>
        </div>
    </nav>

    {{-- ====== DAFTAR RIWAYAT ====== --}}
    <div class="flex-1 px-4 py-4">

        {{-- Summary --}}
        <div class="flex items-center gap-2 mb-3">
            <div class="flex-1 bg-white border border-gray-100 rounded-lg px-2.5 py-2 text-center shadow-sm">
                <p class="text-sm font-black text-gray-900">{{ collect($riwayat)->where('status', 'Selesai')->count() }}</p>
                <p class="text-[9px] text-gray-400 font-medium">Selesai</p>
            </div>
            <div class="flex-1 bg-white border border-gray-100 rounded-lg px-2.5 py-2 text-center shadow-sm">
                <p class="text-sm font-black text-gray-900">{{ collect($riwayat)->where('status', 'Dibatalkan')->count() }}</p>
                <p class="text-[9px] text-gray-400 font-medium">Dibatalkan</p>
            </div>
            <div class="flex-1 bg-white border border-gray-100 rounded-lg px-2.5 py-2 text-center shadow-sm">
                <p class="text-sm font-black text-gray-900">{{ count($riwayat) }}</p>
                <p class="text-[9px] text-gray-400 font-medium">Total</p>
            </div>
        </div>

        {{-- List --}}
        <div class="space-y-2">
            @foreach ($riwayat as $idx => $item)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                 x-data="{ open: false }">
                {{-- Clickable Header --}}
                <button @click="open = !open" class="w-full text-left px-3 py-2.5 focus:outline-none">
                    {{-- Header: Tanggal + Badge --}}
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] text-gray-400 font-medium flex items-center gap-1">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            {{ $item->tanggal }}
                        </span>

                        @if ($item->status === 'Selesai')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[9px] font-semibold rounded-full border border-emerald-100">
                                <span class="w-1 h-1 bg-emerald-500 rounded-full"></span>
                                Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 text-red-600 text-[9px] font-semibold rounded-full border border-red-200">
                                <span class="w-1 h-1 bg-red-500 rounded-full"></span>
                                Dibatalkan
                            </span>
                        @endif
                    </div>

                    {{-- Content: Kode + Detail --}}
                    <div class="flex items-center gap-2.5">
                        {{-- Kode Badge --}}
                        @php
                            $kodeBg = match($item->warna) {
                                'blue'    => 'bg-blue-600',
                                'emerald' => 'bg-emerald-600',
                                'amber'   => 'bg-amber-500',
                                default   => 'bg-gray-500',
                            };
                        @endphp
                        <div class="w-9 h-9 {{ $kodeBg }} rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                            <span class="text-white text-xs font-black">{{ $item->kode }}</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="text-xs font-bold text-gray-900 truncate">{{ $item->layanan }}</h3>
                            <p class="text-[10px] text-gray-400 mt-0.5">Nomor: <span class="font-semibold text-gray-500">{{ $item->nomor }}</span></p>
                        </div>

                        <svg class="w-3.5 h-3.5 text-gray-300 shrink-0 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </button>

                {{-- Expandable Detail --}}
                <div x-show="open" x-collapse x-cloak>
                    <div class="border-t border-gray-100 px-3 pb-3 pt-2.5">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Detail Riwayat</p>
                        <div class="space-y-2">
                            {{-- Kode Booking --}}
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                                    Kode Booking
                                </span>
                                <span class="text-[11px] font-bold text-gray-900">{{ $item->kodeBooking }}</span>
                            </div>

                            @if($item->status === 'Selesai')
                            {{-- Waktu Datang --}}
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Waktu Datang
                                </span>
                                <span class="text-[11px] font-bold text-gray-900">{{ $item->waktuDatang }}</span>
                            </div>
                            {{-- Waktu Selesai --}}
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Waktu Selesai
                                </span>
                                <span class="text-[11px] font-bold text-gray-900">{{ $item->waktuSelesai }}</span>
                            </div>
                            {{-- Loket --}}
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                    Loket
                                </span>
                                <span class="text-[11px] font-bold text-gray-900">{{ $item->loket }}</span>
                            </div>
                            {{-- Petugas --}}
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    Petugas
                                </span>
                                <span class="text-[11px] font-bold text-gray-900">{{ $item->petugas }}</span>
                            </div>
                            @else
                            {{-- Alasan Batal --}}
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                    Alasan
                                </span>
                                <span class="text-[11px] font-bold text-red-600">{{ $item->alasanBatal }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Status Summary --}}
                        @if($item->status === 'Selesai')
                        <div class="mt-2.5 bg-emerald-50 border border-emerald-100 rounded-lg px-2.5 py-2 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-[10px] text-emerald-700 leading-relaxed">Pelayanan selesai dan berhasil diproses oleh <strong>{{ $item->petugas }}</strong> di <strong>{{ $item->loket }}</strong>.</p>
                        </div>
                        @else
                        <div class="mt-2.5 bg-red-50 border border-red-100 rounded-lg px-2.5 py-2 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                            <p class="text-[10px] text-red-600 leading-relaxed">Antrean tidak diproses. Alasan: <strong>{{ $item->alasanBatal }}</strong></p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="mt-4">
            <div class="bg-gray-100 border border-gray-200 rounded-lg px-3 py-2.5 text-center">
                <p class="text-[10px] text-gray-400">Menampilkan {{ count($riwayat) }} riwayat terakhir</p>
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>

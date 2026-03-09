@extends('layouts.testes')

@section('title', 'Activity Log - SAPA')

@php
    $withSidebar = true;

    // ── Static dummy data (no backend) ──────────────────────────────────────
    $totalLogs = 48;
    $successCount = 35;
    $warningCount = 8;
    $errorCount = 5;
    $successRate = 73;

    $logs = collect([
        (object) [
            'id' => 1,
            'action' => 'User Login',
            'category' => 'auth',
            'status' => 'success',
            'description' => 'Pengguna admin berhasil login ke sistem.',
            'ip_address' => '192.168.1.10',
            'logged_at' => '2026-03-09 08:15:00',
            'properties' => (object) ['browser' => 'Chrome', 'os' => 'Windows'],
            'user' => (object) ['name' => 'Admin Utama', 'username' => 'admin', 'role' => 'admin'],
        ],
        (object) [
            'id' => 2,
            'action' => 'Tambah Antrian',
            'category' => 'queue',
            'status' => 'success',
            'description' => 'Antrian baru A-001 berhasil ditambahkan untuk layanan KTP.',
            'ip_address' => '192.168.1.11',
            'logged_at' => '2026-03-09 09:30:00',
            'properties' => (object) ['queue_number' => 'A-001', 'service' => 'KTP'],
            'user' => (object) ['name' => 'Operator 1', 'username' => 'operator1', 'role' => 'operator'],
        ],
        (object) [
            'id' => 3,
            'action' => 'Ubah Konfigurasi',
            'category' => 'config',
            'status' => 'warning',
            'description' => 'Pengaturan jam operasional diubah tanpa konfirmasi supervisor.',
            'ip_address' => '192.168.1.12',
            'logged_at' => '2026-03-09 10:00:00',
            'properties' => (object) ['field' => 'operating_hours', 'old' => '08:00-16:00', 'new' => '07:00-17:00'],
            'user' => (object) ['name' => 'Admin Cabang', 'username' => 'admincabang', 'role' => 'admin'],
        ],
        (object) [
            'id' => 4,
            'action' => 'Hapus Konten Media',
            'category' => 'content',
            'status' => 'success',
            'description' => 'Banner promosi bulan Februari berhasil dihapus dari sistem.',
            'ip_address' => '192.168.1.10',
            'logged_at' => '2026-03-09 11:20:00',
            'properties' => (object) ['file' => 'banner_feb_2026.jpg'],
            'user' => (object) ['name' => 'Admin Utama', 'username' => 'admin', 'role' => 'admin'],
        ],
        (object) [
            'id' => 5,
            'action' => 'Gagal Login',
            'category' => 'auth',
            'status' => 'error',
            'description' => 'Percobaan login gagal — kata sandi salah sebanyak 3 kali.',
            'ip_address' => '10.0.0.99',
            'logged_at' => '2026-03-09 12:45:00',
            'properties' => (object) ['attempts' => 3],
            'user' => null,
        ],
        (object) [
            'id' => 6,
            'action' => 'Tambah Layanan',
            'category' => 'service',
            'status' => 'success',
            'description' => 'Layanan baru "Legalisir Dokumen" berhasil ditambahkan.',
            'ip_address' => '192.168.1.10',
            'logged_at' => '2026-03-08 14:00:00',
            'properties' => (object) ['service_name' => 'Legalisir Dokumen'],
            'user' => (object) ['name' => 'Admin Utama', 'username' => 'admin', 'role' => 'admin'],
        ],
        (object) [
            'id' => 7,
            'action' => 'Update Profil Pengguna',
            'category' => 'user',
            'status' => 'success',
            'description' => 'Data profil operator berhasil diperbarui.',
            'ip_address' => '192.168.1.15',
            'logged_at' => '2026-03-08 15:30:00',
            'properties' => (object) ['field' => 'email'],
            'user' => (object) ['name' => 'Operator 2', 'username' => 'operator2', 'role' => 'operator'],
        ],
        (object) [
            'id' => 8,
            'action' => 'Reset Antrian',
            'category' => 'queue',
            'status' => 'warning',
            'description' => 'Antrian harian direset sebelum jam operasional berakhir.',
            'ip_address' => '192.168.1.13',
            'logged_at' => '2026-03-08 16:55:00',
            'properties' => (object) ['reset_by' => 'manual'],
            'user' => (object) ['name' => 'Supervisor', 'username' => 'supervisor', 'role' => 'admin'],
        ],
    ]);
    // ────────────────────────────────────────────────────────────────────────
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50" x-data="activityLogPage()">
        <div class="container mx-auto px-4 py-6 space-y-6">

            {{-- Statistic Card --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $statCards = [
                        [
                            'label' => 'Total Aktivitas',
                            'value' => $totalLogs,
                            'sub' => 'Hari ini',
                            'color' => 'text-gray-800',
                            'bg' => 'bg-gray-100',
                            'icon' => 'activity',
                        ],
                        [
                            'label' => 'Success',
                            'value' => $successCount,
                            'sub' => $successRate . '% dari total',
                            'color' => 'text-green-700',
                            'bg' => 'bg-green-100',
                            'icon' => 'check',
                        ],
                        [
                            'label' => 'Warning',
                            'value' => $warningCount,
                            'sub' => 'Perlu perhatian',
                            'color' => 'text-amber-700',
                            'bg' => 'bg-amber-100',
                            'icon' => 'warning',
                        ],
                        [
                            'label' => 'Error',
                            'value' => $errorCount,
                            'sub' => 'Memerlukan tindakan',
                            'color' => 'text-red-700',
                            'bg' => 'bg-red-100',
                            'icon' => 'error',
                        ],
                    ];
                @endphp
                @foreach ($statCards as $s)
                    <div class="bg-white rounded-2xl border shadow-sm p-5">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm text-gray-500">{{ $s['label'] }}</p>
                            <div class="w-9 h-9 {{ $s['bg'] }} rounded-xl flex items-center justify-center">
                                @if ($s['icon'] === 'activity')
                                    <svg class="w-5 h-5 {{ $s['color'] }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                @elseif($s['icon'] === 'check')
                                    <svg class="w-5 h-5 {{ $s['color'] }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif($s['icon'] === 'warning')
                                    <svg class="w-5 h-5 {{ $s['color'] }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 {{ $s['color'] }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <p class="text-3xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $s['sub'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Filter Section --}}
            <div class="bg-white rounded-2xl border shadow-sm">
                <div class="p-5 border-b flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h2 class="font-bold">Filter Activity Log</h2>
                </div>
                <form method="GET" action="#" class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

                        {{-- Pencarian --}}
                        <div class="space-y-1.5">
                            <label
                                class="block text-xs font-semibold text-gray-600 uppercase tracking-wide">Pencarian</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" name="search" value="" placeholder="Cari user, aksi..."
                                    class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- Tanggal Mulai --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide">Tanggal
                                Mulai</label>
                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Tanggal Akhir --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide">Tanggal
                                Akhir</label>
                            <input type="date" name="end_date" value="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Kategori --}}
                        <div class="space-y-1.5">
                            <label
                                class="block text-xs font-semibold text-gray-600 uppercase tracking-wide">Kategori</label>
                            <select name="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                <option value="all">Semua Kategori</option>
                                <option value="auth">🔐 Authentication</option>
                                <option value="queue">📋 Queue Management</option>
                                <option value="config">⚙ Configuration</option>
                                <option value="user">👤 User Management</option>
                                <option value="content">🖼 Content</option>
                                <option value="service">🏢 Service</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</label>
                            <select name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                <option value="all">Semua Status</option>
                                <option value="success">✅ Success</option>
                                <option value="warning">⚠️ Warning</option>
                                <option value="error">❌ Error</option>
                                <option value="info">ℹ️ Info</option>
                            </select>
                        </div>


                    </div>

                    <div class="flex items-center gap-3 mt-4">
                        <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors">
                            Terapkan Filter
                        </button>
                        <a href="#"
                            class="px-5 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-medium transition-colors">
                            Reset Filter
                        </a>
                        <span class="text-sm text-gray-500 ml-auto">
                            Menampilkan {{ $logs->count() }} dari {{ $totalLogs }} aktivitas
                        </span>
                    </div>
                </form>
            </div>

            {{-- Main Section --}}
            <div class="gap-6">
                <div class="bg-white rounded-2xl border shadow-sm">
                    <div class="p-5 border-b flex items-center justify-between">
                        <h2 class="font-bold">Riwayat Aktivitas</h2>
                        <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">
                            {{ $logs->count() }} total
                        </span>
                    </div>

                    <div class="p-5 overflow-y-auto" style="max-height: 680px">
                        @if ($logs->isEmpty())
                            <div class="text-center py-16 text-gray-400">
                                <svg class="w-14 h-14 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <p class="font-medium">Tidak ada aktivitas yang sesuai dengan filter</p>
                            </div>
                        @else
                            {{-- Group by date --}}
                            @php $currentDate = null; @endphp
                            @foreach ($logs as $index => $log)
                                {{-- Date separator --}}
                                @php $logDate = \Carbon\Carbon::parse($log->logged_at)->toDateString(); @endphp
                                @if ($logDate !== $currentDate)
                                    @php $currentDate = $logDate; @endphp
                                    <div class="flex items-center gap-3 my-4 first:mt-0">
                                        <div class="flex-1 h-px bg-gray-100"></div>
                                        <span class="text-xs font-semibold text-gray-400 px-2 bg-white whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($logDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                        </span>
                                        <div class="flex-1 h-px bg-gray-100"></div>
                                    </div>
                                @endif

                                {{-- Log Entry --}}
                                <div class="relative flex gap-4 pb-5 cursor-pointer group"
                                    @click="selectLog({{ json_encode($log) }})">

                                    {{-- Timeline line --}}
                                    @if (!$loop->last)
                                        <div class="absolute left-[21px] top-[44px] w-0.5 h-[calc(100%-20px)] bg-gray-100">
                                        </div>
                                    @endif

                                    {{-- Status Icon --}}
                                    @php
                                        $statusCfg = match ($log->status) {
                                            'success' => [
                                                'bg' => 'bg-green-100',
                                                'icon_color' => 'text-green-600',
                                                'border' => 'border-green-200',
                                            ],
                                            'warning' => [
                                                'bg' => 'bg-amber-100',
                                                'icon_color' => 'text-amber-600',
                                                'border' => 'border-amber-200',
                                            ],
                                            'error' => [
                                                'bg' => 'bg-red-100',
                                                'icon_color' => 'text-red-600',
                                                'border' => 'border-red-200',
                                            ],
                                            default => [
                                                'bg' => 'bg-blue-100',
                                                'icon_color' => 'text-blue-600',
                                                'border' => 'border-blue-200',
                                            ],
                                        };
                                        $categoryCfg = match ($log->category) {
                                            'auth' => ['label' => 'Auth', 'bg' => 'bg-purple-100 text-purple-700'],
                                            'queue' => ['label' => 'Queue', 'bg' => 'bg-blue-100   text-blue-700'],
                                            'config' => [
                                                'label' => 'Config',
                                                'bg' => 'bg-gray-100   text-gray-700',
                                            ],
                                            'user' => ['label' => 'User', 'bg' => 'bg-green-100  text-green-700'],
                                            'content' => [
                                                'label' => 'Konten',
                                                'bg' => 'bg-orange-100 text-orange-700',
                                            ],
                                            'service' => [
                                                'label' => 'Layanan',
                                                'bg' => 'bg-indigo-100 text-indigo-700',
                                            ],
                                            default => [
                                                'label' => $log->category,
                                                'bg' => 'bg-gray-100 text-gray-600',
                                            ],
                                        };
                                    @endphp

                                    <div
                                        class="w-11 h-11 rounded-full {{ $statusCfg['bg'] }} border-2 {{ $statusCfg['border'] }} flex items-center justify-center flex-shrink-0 z-10 transition-transform group-hover:scale-110">
                                        @if ($log->status === 'success')
                                            <svg class="w-5 h-5 {{ $statusCfg['icon_color'] }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        @elseif($log->status === 'warning')
                                            <svg class="w-5 h-5 {{ $statusCfg['icon_color'] }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        @elseif($log->status === 'error')
                                            <svg class="w-5 h-5 {{ $statusCfg['icon_color'] }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 {{ $statusCfg['icon_color'] }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>

                                    {{-- Content card --}}
                                    <div class="flex-1 bg-white border border-gray-100 rounded-xl p-4 shadow-sm
                                        group-hover:border-blue-200 group-hover:shadow-md transition-all"
                                        :class="selectedLogId === {{ $log->id }} ?
                                            'border-blue-400 ring-2 ring-blue-100' : ''">

                                        <div class="flex items-start justify-between gap-2 mb-2 flex-wrap">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="font-semibold text-sm text-gray-800">{{ $log->action }}</span>
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-xs font-medium {{ $categoryCfg['bg'] }}">
                                                    {{ $categoryCfg['label'] }}
                                                </span>
                                            </div>
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $statusCfg['bg'] }} {{ $statusCfg['icon_color'] }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-gray-600 mb-3 leading-relaxed">{{ $log->description }}
                                        </p>

                                        <div class="flex items-center gap-4 text-xs text-gray-400 flex-wrap">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <strong
                                                    class="text-gray-600">{{ $log->user?->name ?? ($log->user?->username ?? 'System') }}</strong>
                                                <span
                                                    class="text-gray-400">({{ ucfirst($log->user?->role ?? 'system') }})</span>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($log->logged_at)->format('H:i:s') }}
                                            </span>
                                            @if ($log->ip_address)
                                                <span class="flex items-center gap-1 font-mono">
                                                    IP: {{ $log->ip_address }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function activityLogPage() {
            return {
                selectedLog: null,
                selectedLogId: null,

                selectLog(log) {
                    this.selectedLog = log;
                    this.selectedLogId = log.id;
                },
            };
        }
    </script>
@endpush

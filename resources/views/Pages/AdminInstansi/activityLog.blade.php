@extends('layouts.testes')

@section('title', 'Activity Log - SAPA')

@php
    $withSidebar = true;

    $statCards = [
        [
            'label' => 'Total Aktivitas',
            'value' => $totalLogs,
            'sub' => 'Sesuai filter',
            'color' => 'text-gray-800',
            'icon' =>
                '<svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>',
        ],
        [
            'label' => 'Success',
            'value' => $successCount,
            'sub' => $successRate . '% dari total',
            'color' => 'text-green-700',
            'icon' =>
                '<svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Warning',
            'value' => $warningCount,
            'sub' => 'Perlu perhatian',
            'color' => 'text-amber-700',
            'icon' =>
                '<svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
        ],
        [
            'label' => 'Error',
            'value' => $errorCount,
            'sub' => 'Memerlukan tindakan',
            'color' => 'text-red-700',
            'icon' =>
                '<svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Info',
            'value' => $infoCount,
            'sub' => 'Informasi sistem',
            'color' => 'text-blue-700',
            'icon' =>
                '<svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
    ];
@endphp

@section('content')
    <div class="bg-gray-50" x-data="activityLogPage()">
        <div class="container mx-auto p-6 space-y-6">

            {{-- Statistic Card --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <x-card :cards="$statCards" />
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
                <form method="GET"
                    action="{{ route('activity.log', ['instance_slug' => request()->route('instance_slug')]) }}"
                    class="p-5">

                    {{-- Grid untuk filter lainnya --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Pencarian --}}
                        <div class="lg:col-span-4">
                            <x-search-bar name="search" placeholder="Cari user, aksi..." value="{{ request('search') }}" />
                        </div>

                        {{-- Tanggal Mulai --}}
                        <x-input-date name="start_date" label="Tanggal Mulai" value="{{ request('start_date', date('Y-m-d')) }}" />

                        {{-- Tanggal Akhir --}}
                        <x-input-date name="end_date" label="Tanggal Akhir" value="{{ request('end_date', date('Y-m-d')) }}" />

                        {{-- Kategori --}}
                        <x-input-dropdown name="category" label="Kategori" :options="[
                            ['value' => 'all', 'label' => 'Semua Kategori'],
                            ['value' => 'auth', 'label' => 'Authentication'],
                            ['value' => 'queue', 'label' => 'Queue Management'],
                            ['value' => 'config', 'label' => 'Configuration'],
                            ['value' => 'user', 'label' => 'User Management'],
                            ['value' => 'content', 'label' => 'Content'],
                            ['value' => 'service', 'label' => 'Service'],
                        ]"
                            value="{{ request('category', 'all') }}" />

                        {{-- Status --}}
                        <x-input-dropdown name="status" label="Status" :options="[
                            ['value' => 'all', 'label' => 'Semua Status'],
                            ['value' => 'success', 'label' => 'Success'],
                            ['value' => 'warning', 'label' => 'Warning'],
                            ['value' => 'error', 'label' => 'Error'],
                            ['value' => 'info', 'label' => 'Info'],
                        ]"
                            value="{{ request('status', 'all') }}" />
                    </div>

                    <div class="flex justify-end gap-2 pt-8">
                        <a href="{{ route('activity.log', ['instance_slug' => request()->route('instance_slug')]) }}">
                            <x-button type="button" variant="white" class="border-gray-200">
                                Reset
                            </x-button>
                        </a>
                        <x-button type="submit" variant="primary"
                            icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>'>
                            Terapkan Filter
                        </x-button>
                    </div>
                </form>
            </div>

            {{-- Main Section --}}
            <div class="gap-6">
                <div class="bg-white rounded-2xl border shadow-sm">
                    <div class="p-5 border-b flex items-center justify-between">
                        <h2 class="font-bold">Riwayat Aktivitas</h2>
                        <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">
                            Menampilkan {{ $logs->count() }} dari {{ $totalLogs }} aktivitas
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
                                    <div x-data="{ expanded: false }"
                                        class="flex-1 bg-white border border-gray-100 rounded-xl p-4 shadow-sm
                                        hover:border-blue-200 hover:shadow-md transition-all">

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

                                            @if (isset($log->raw_properties['attributes']) && count($log->raw_properties['attributes']) > 0)
                                                <button @click="expanded = !expanded"
                                                    class="ml-auto text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 transition-colors">
                                                    <span x-text="expanded ? 'Sembunyikan Detail' : 'Lihat Detail'">Lihat
                                                        Detail</span>
                                                    <svg class="w-4 h-4 transform transition-transform"
                                                        :class="expanded ? 'rotate-180' : ''" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Detail Accordion --}}
                                        @if (isset($log->raw_properties['attributes']) && count($log->raw_properties['attributes']) > 0)
                                            <div x-show="expanded" x-collapse x-cloak
                                                class="mt-4 pt-4 border-t border-gray-100">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    @if (isset($log->raw_properties['old']) && count($log->raw_properties['old']) > 0)
                                                        <div class="bg-red-50/50 rounded-lg p-3 border border-red-100">
                                                            <h4
                                                                class="text-xs font-semibold text-red-800 mb-2 uppercase tracking-wider">
                                                                Nilai Lama</h4>
                                                            <div class="space-y-1.5">
                                                                @foreach ($log->raw_properties['old'] as $key => $value)
                                                                    @if (in_array($key, ['created_at', 'updated_at']))
                                                                        @continue
                                                                    @endif
                                                                    <div class="flex flex-col text-sm">
                                                                        <span
                                                                            class="text-gray-500 text-xs font-mono">{{ $key }}</span>
                                                                        <span
                                                                            class="text-gray-800 break-all">{{ is_array($value) ? json_encode($value) : (string) $value }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div
                                                        class="bg-green-50/50 rounded-lg p-3 border border-green-100 {{ !isset($log->raw_properties['old']) ? 'md:col-span-2' : '' }}">
                                                        <h4
                                                            class="text-xs font-semibold text-green-800 mb-2 uppercase tracking-wider">
                                                            Nilai Baru</h4>
                                                        <div class="space-y-1.5">
                                                            @foreach ($log->raw_properties['attributes'] as $key => $value)
                                                                @if (in_array($key, ['created_at', 'updated_at']))
                                                                    @continue
                                                                @endif
                                                                <div class="flex flex-col text-sm">
                                                                    <span
                                                                        class="text-gray-500 text-xs font-mono">{{ $key }}</span>
                                                                    <span
                                                                        class="text-gray-800 break-all">{{ is_array($value) ? json_encode($value) : (string) $value }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @endforeach

                        @endif
                    </div>
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

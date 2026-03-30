@extends('layouts.testes')

@section('title', 'Laporan & Statistik - SAPA')

@php

    $withSidebar = true;

    $totalQueue = 128;
    $completedQueue = 105;
    $completionRate = 82;
    $avgServiceTime = 7.4;
    $waitingQueue = 15;
    $servingQueue = 8;

    $statCards = [
        [
            'label' => 'Total Antrean',
            'value' => $totalQueue,
            'color' => 'text-gray-800',
            'sub' => '+12% dari kemarin',
            'icon' =>
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
        ],
        [
            'label' => 'Selesai Dilayani',
            'value' => $completedQueue,
            'color' => 'text-green-600',
            'sub' => $completionRate . '% dari total',
            'icon' =>
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Rata-rata Waktu',
            'value' => number_format($avgServiceTime, 1) . ' mnt',
            'color' => 'text-blue-600',
            'sub' => 'Per pelayanan',
            'icon' =>
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Sedang Menunggu',
            'value' => $waitingQueue,
            'color' => 'text-orange-600',
            'sub' => $servingQueue . ' sedang dilayani',
            'icon' =>
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>',
        ],
    ];

    $today = date('Y-m-d');
@endphp

@section('content')
    <div class="bg-gray-50" x-data="reportsPage()">

        <div class="container mx-auto p-6 space-y-6">
            {{-- ===== FILTER SECTION ===== --}}
            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h2 class="text-lg font-bold">Filter Laporan</h2>
                </div>

                <form method="GET" action="#" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <form method="GET" action="#" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <x-input-date name="start_date" label="Tanggal Mulai" value="{{ $today }}" />
                        <x-input-date name="end_date" label="Tanggal Akhir" value="{{ $today }}" />
                        <x-input-dropdown name="service_id" label="Layanan" :options="$serviceOptions" value="all" />
                        <x-input-dropdown name="operator" label="Operator" :options="$operatorOptions" value="all" />
                    </form>
                </form>
            </div>

            {{-- ===== STATISTICS CARDS ===== --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-card :cards="$statCards" />
            </div>

            {{-- ===== CHARTS TABS ===== --}}
            <x-tab :tabs="[
                ['id' => 'service', 'label' => 'Per Layanan'],
                ['id' => 'hourly', 'label' => 'Per Jam'],
                ['id' => 'type', 'label' => 'Tipe Registrasi'],
            ]">
                @slot('header')
                    <x-button variant="white"
                        icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>'>
                        Export PDF
                    </x-button>
                    <x-button variant="success"
                        icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>'>
                        Export Excel
                    </x-button>
                @endslot

                {{-- Per Layanan --}}
                <div x-show="activeTab === 'service'" x-cloak class="bg-white rounded-2xl border shadow-sm p-6">
                    <h3 class="font-bold mb-1">Antrean Per Layanan</h3>
                    <p class="text-sm text-gray-500 mb-4">Jumlah antrean untuk setiap jenis layanan</p>
                    {{-- <canvas id="chart-per-service" style="height:380px"></canvas> --}}
                </div>

                {{-- Per Jam --}}
                <div x-show="activeTab === 'hourly'" x-cloak class="bg-white rounded-2xl border shadow-sm p-6">
                    <h3 class="font-bold mb-1">Antrean Per Jam</h3>
                    <p class="text-sm text-gray-500 mb-4">Distribusi antrean berdasarkan waktu</p>
                    {{-- <canvas id="chart-per-hour" style="height:380px"></canvas> --}}
                </div>

                {{-- Tipe Registrasi --}}
                <div x-show="activeTab === 'type'" x-cloak class="bg-white rounded-2xl border shadow-sm p-6">
                    <h3 class="font-bold mb-1">Tipe Registrasi</h3>
                    <p class="text-sm text-gray-500 mb-4">Perbandingan registrasi online vs onsite</p>
                    {{-- <canvas id="chart-reg-type" style="height:380px"></canvas> --}}
                </div>
            </x-tab>

            {{-- ===== DATA TABLE ===== --}}
            <div class="bg-white rounded-2xl border shadow-sm">
                <div class="p-5 border-b">
                    <h3 class="font-bold">Detail Data Antrean</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Rincian lengkap setiap antrean</p>
                </div>

                <x-table :columns="[
                    'No. Antrean',
                    'Layanan',
                    'Tipe',
                    'Waktu Daftar',
                    'Waktu Selesai',
                    'Durasi',
                    'Operator',
                    'Status',
                    'Aksi',
                ]" :rows="$queueData" emptyMessage="Tidak ada data antrean">
                    @foreach ($queueData as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold">{{ $item->queue_number }}</td>
                            <td class="px-4 py-3">{{ $item->service_name }}</td>
                            <td class="px-4 py-3">
                                <x-label-status type="registration_type" :value="$item->registration_type" />
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $item->start_at ? \Carbon\Carbon::parse($item->start_at)->format('H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->format('H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $item->service_time ? $item->service_time . ' mnt' : '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->operator_name }}</td>
                            <td class="px-4 py-3">
                                <x-label-status type="status" :value="$item->status" />
                            </td>
                            <td class="px-4 py-3">
                                <x-action-buttons :view="true" viewAction="openDetailModal" :edit="false"
                                    :delete="false" />
                            </td>
                        </tr>
                    @endforeach
                </x-table>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function reportsPage() {
            return {};
        }
    </script>
@endpush

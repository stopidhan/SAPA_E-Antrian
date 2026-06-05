@extends('layouts.testes')

@section('title', 'Laporan & Statistik - SAPA')

@php
    $withSidebar = true;
    $today = date('Y-m-d');

    $statCards = [
        [
            'label' => 'Total Antrean',
            'value' => $totalQueue,
            'color' => 'text-gray-800',
            'sub' => ($growth >= 0 ? '+' : '') . $growth . '% dari periode sebelumnya',
            'icon' =>
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
        ],
        [
            'label' => 'Selesai Dilayani',
            'value' => $completedQueue,
            'color' => 'text-green-600',
            'sub' => $cancelledQueue . ' antrean batal/dilewati',
            'icon' =>
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Rata-rata Waktu Tunggu',
            'value' => number_format($avgWaitTime, 1) . ' mnt',
            'color' => 'text-purple-600',
            'sub' => 'Waktu sebelum dilayani',
            'icon' =>
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Rata-rata Waktu Layanan',
            'value' => number_format($avgServiceTime, 1) . ' mnt',
            'color' => 'text-blue-600',
            'sub' => 'Durasi penanganan operator',
            'icon' =>
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>',
        ],
    ];
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

                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4"
                    id="filterForm" x-data="{ startDate: '{{ request('start_date', date('Y-m-d')) }}', endDate: '{{ request('end_date', date('Y-m-d')) }}' }">
                    <x-input-date name="start_date" label="Tanggal Mulai" value="{{ request('start_date', date('Y-m-d')) }}"
                        x-model="startDate" x-bind:max="endDate" />
                    <x-input-date name="end_date" label="Tanggal Akhir" value="{{ request('end_date', date('Y-m-d')) }}"
                        x-model="endDate" x-bind:min="startDate" />
                    <x-input-dropdown name="service_id" label="Layanan" :options="$serviceOptions"
                        value="{{ request('service_id', 'all') }}" />
                    <x-input-dropdown name="operator" label="Operator" :options="$operatorOptions"
                        value="{{ request('operator', 'all') }}" />
                    <div class="md:col-span-4 flex justify-end">
                        <x-button type="submit" variant="primary"
                            icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>'>
                            Terapkan Filter
                        </x-button>
                    </div>
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
                    <a href="{{ route('reports.export.pdf', request()->all()) }}" target="_blank">
                        <x-button variant="white" type="button"
                            icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>'>
                            Export PDF
                        </x-button>
                    </a>
                    <a href="{{ route('reports.export.excel', request()->all()) }}">
                        <x-button variant="success" type="button"
                            icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>'>
                            Export Excel
                        </x-button>
                    </a>
                @endslot

                {{-- Per Layanan --}}
                <div x-show="activeTab === 'service'" x-cloak class="bg-white rounded-2xl border shadow-sm p-6">
                    <h3 class="font-bold mb-1">Antrean Per Layanan</h3>
                    <p class="text-sm text-gray-500 mb-4">Jumlah antrean untuk setiap jenis layanan</p>
                    <div style="height:380px" class="relative">
                        <canvas id="chart-per-service"></canvas>
                    </div>
                </div>

                {{-- Per Jam --}}
                <div x-show="activeTab === 'hourly'" x-cloak class="bg-white rounded-2xl border shadow-sm p-6">
                    <h3 class="font-bold mb-1">Antrean Per Jam</h3>
                    <p class="text-sm text-gray-500 mb-4">Distribusi antrean berdasarkan waktu</p>
                    <div style="height:380px" class="relative">
                        <canvas id="chart-per-hour"></canvas>
                    </div>
                </div>

                {{-- Tipe Registrasi --}}
                <div x-show="activeTab === 'type'" x-cloak class="bg-white rounded-2xl border shadow-sm p-6">
                    <h3 class="font-bold mb-1">Tipe Registrasi</h3>
                    <p class="text-sm text-gray-500 mb-4">Perbandingan registrasi online vs onsite</p>
                    <div style="height:380px" class="relative flex justify-center">
                        <canvas id="chart-reg-type"></canvas>
                    </div>
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
                    'Waktu Mulai',
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
                                <x-action-buttons :view="true" :viewAction="'openDetailModal(' . json_encode($item) . ')'" :edit="false"
                                    :delete="false" />
                            </td>
                        </tr>
                    @endforeach
                </x-table>
            </div>

        </div>
        {{-- Detail Modal --}}
        @include('components.Modals.modal_queue-detail')

    </div>
@endsection

@push('scripts')
    <script>
        function reportsPage() {
            return {
                selectedQueue: null,
                chartData: @json($chartData),
                init() {
                    this.renderCharts();
                },
                renderCharts() {
                    // Chart Per Layanan
                    const ctxService = document.getElementById('chart-per-service');
                    if (ctxService && this.chartData.service.labels.length > 0) {
                        new Chart(ctxService, {
                            type: 'bar',
                            data: {
                                labels: this.chartData.service.labels,
                                datasets: [{
                                    label: 'Total Antrean',
                                    data: this.chartData.service.data,
                                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                    borderRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Chart Per Jam
                    const ctxHourly = document.getElementById('chart-per-hour');
                    if (ctxHourly && this.chartData.hourly.labels.length > 0) {
                        new Chart(ctxHourly, {
                            type: 'line',
                            data: {
                                labels: this.chartData.hourly.labels,
                                datasets: [{
                                    label: 'Total Antrean',
                                    data: this.chartData.hourly.data,
                                    borderColor: 'rgba(245, 158, 11, 1)',
                                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Chart Tipe Registrasi
                    const ctxRegType = document.getElementById('chart-reg-type');
                    if (ctxRegType && this.chartData.regType.labels.length > 0) {
                        new Chart(ctxRegType, {
                            type: 'doughnut',
                            data: {
                                labels: this.chartData.regType.labels,
                                datasets: [{
                                    data: this.chartData.regType.data,
                                    backgroundColor: [
                                        'rgba(16, 185, 129, 0.8)', // Green for Online/Onsite
                                        'rgba(99, 102, 241, 0.8)' // Indigo for the other
                                    ],
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });
                    }
                },
                openDetailModal(item) {
                    this.selectedQueue = item;
                    this.$dispatch('open-modal', 'service-detail');
                }
            };
        }
    </script>
@endpush

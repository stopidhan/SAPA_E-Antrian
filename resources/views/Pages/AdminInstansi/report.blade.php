@extends('layouts.testes')

@section('title', 'Laporan & Statistik - SAPA')

@php
    $withSidebar = true;
    $today = date('Y-m-d');
    $instanceSlug = auth()->user()->instance->instance_slug ?? null;

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

                <form method="GET" action="{{ route('reports.index', ['instance_slug' => $instanceSlug]) }}"
                    class="space-y-4">

                    {{-- Row 1: Search & Dates --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <x-input-text name="search" label="Cari Antrean"
                                placeholder="No. Antrean atau Nama Pelanggan..." value="{{ request('search') }}" />
                        </div>
                        <x-input-date name="start_date" label="Tanggal Mulai"
                            value="{{ request('start_date', date('Y-m-d')) }}" />
                        <x-input-date name="end_date" label="Tanggal Akhir"
                            value="{{ request('end_date', date('Y-m-d')) }}" />
                    </div>

                    {{-- Row 2: Dropdowns --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <x-input-dropdown name="service_id" label="Layanan" :options="$serviceOptions"
                            value="{{ request('service_id', 'all') }}" />
                        <x-input-dropdown name="operator" label="Operator" :options="$operatorOptions"
                            value="{{ request('operator', 'all') }}" />
                        <x-input-dropdown name="counter_id" label="Loket" :options="$counterOptions"
                            value="{{ request('counter_id', 'all') }}" />
                        <x-input-dropdown name="source" label="Sumber" :options="$sourceOptions"
                            value="{{ request('source', 'all') }}" />
                    </div>

                    {{-- Action Row --}}
                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('reports.index', ['instance_slug' => $instanceSlug]) }}">
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
                    <a href="{{ route('reports.export.pdf', array_merge(['instance_slug' => $instanceSlug], request()->except('instance_slug'))) }}"
                        target="_blank">
                        <x-button variant="white" type="button"
                            icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>'>
                            Export PDF
                        </x-button>
                    </a>
                    <a
                        href="{{ route('reports.export.excel', array_merge(['instance_slug' => $instanceSlug], request()->except('instance_slug'))) }}">
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
                    @if (count($chartData['service']['labels']) > 0)
                        <div style="height:380px" class="relative">
                            <canvas id="chart-per-service"></canvas>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-400 py-12" style="height:380px">
                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <p class="text-lg font-medium text-gray-500">Belum ada data grafik</p>
                            <p class="text-sm">Tidak ada data untuk rentang waktu dan filter yang dipilih.</p>
                        </div>
                    @endif
                </div>

                {{-- Per Jam --}}
                <div x-show="activeTab === 'hourly'" x-cloak class="bg-white rounded-2xl border shadow-sm p-6">
                    <h3 class="font-bold mb-1">Antrean Per Jam</h3>
                    <p class="text-sm text-gray-500 mb-4">Distribusi antrean berdasarkan waktu</p>
                    @if (count($chartData['hourly']['labels']) > 0)
                        <div style="height:380px" class="relative">
                            <canvas id="chart-per-hour"></canvas>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-400 py-12" style="height:380px">
                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-500">Belum ada data grafik</p>
                            <p class="text-sm">Tidak ada data untuk rentang waktu dan filter yang dipilih.</p>
                        </div>
                    @endif
                </div>

                {{-- Tipe Registrasi --}}
                <div x-show="activeTab === 'type'" x-cloak class="bg-white rounded-2xl border shadow-sm p-6">
                    <h3 class="font-bold mb-1">Tipe Registrasi</h3>
                    <p class="text-sm text-gray-500 mb-4">Perbandingan registrasi online vs onsite</p>
                    @if (count($chartData['regType']['labels']) > 0)
                        <div style="height:380px" class="relative flex justify-center">
                            <canvas id="chart-reg-type"></canvas>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-400 py-12" style="height:380px">
                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-500">Belum ada data grafik</p>
                            <p class="text-sm">Tidak ada data untuk rentang waktu dan filter yang dipilih.</p>
                        </div>
                    @endif
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
                    'Pelanggan',
                    'Tanggal',
                    'Durasi',
                    'Operator',
                    'Status',
                    'Foto',
                    'Aksi',
                ]" :rows="$queueData" emptyMessage="Tidak ada data antrean">
                    @foreach ($queueData as $queue)
                        @php
                            $duration =
                                $queue->started_at && $queue->completed_at
                                    ? round(
                                        \Carbon\Carbon::parse($queue->started_at)->diffInMinutes($queue->completed_at),
                                    )
                                    : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold font-mono text-blue-700">{{ $queue->queue_number }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $queue->service_name }}</div>
                                <div class="text-xs text-gray-400 truncate max-w-[150px]">{{ $queue->service_category }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $queue->customer_name ?? '-' }}</div>
                                <div class="text-xs text-gray-400 capitalize">{{ $queue->queue_source }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $queue->taken_at ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $duration }} mnt
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <div class="font-medium">{{ $queue->operator_name }}</div>
                                <div class="text-xs text-gray-400">{{ $queue->counter_name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <x-label-status type="status" :value="$queue->status" />
                            </td>
                            <td class="px-4 py-3">
                                @if ($queue->photo_path)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 font-bold" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 font-bold" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M20 12H4"></path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end">
                                    <x-action-buttons :view="true" viewAction="viewDetail({{ $queue->id }})"
                                        :edit="false" :delete="false" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-table>
            </div>

        </div>
        {{-- Detail Modal --}}
        <x-modals.modal_queue-detail />

    </div>
@endsection

@push('scripts')
    <script>
        function reportsPage() {
            return {
                detailData: null,
                detailLoading: false,
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
                async viewDetail(queueId) {
                    this.detailLoading = true;
                    this.detailData = null;
                    this.$dispatch('open-modal', 'queue-detail-modal');

                    try {
                        const url = `{{ route('reports.api.queue-detail', ['instance_slug' => $instanceSlug, 'queue' => '__ID__']) }}`.replace('__ID__', queueId);
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to fetch detail');
                        }

                        this.detailData = await response.json();
                    } catch (err) {
                        console.error('Detail fetch error:', err);
                        showToast('Gagal memuat detail antrean', 'error');
                        this.$dispatch('close-modal', 'queue-detail-modal');
                    } finally {
                        this.detailLoading = false;
                    }
                }
            };
        }
    </script>
@endpush

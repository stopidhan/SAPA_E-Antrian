@extends('layouts.testes')

@section('title', 'Laporan & Statistik - SAPA')

@php
    $withSidebar = true;

    // {{-- Dummy data pengganti variabel dari controller --}}
    $totalQueue = 128;
    $completedQueue = 105;
    $completionRate = 82;
    $avgServiceTime = 7.4;
    $waitingQueue = 15;
    $servingQueue = 8;

    $services = [
        (object) ['id' => 1, 'name' => 'Pelayanan Umum'],
        (object) ['id' => 2, 'name' => 'Administrasi KTP'],
        (object) ['id' => 3, 'name' => 'Akta Kelahiran'],
    ];

    $operators = [
        (object) ['name' => 'Budi Santoso'],
        (object) ['name' => 'Siti Rahayu'],
        (object) ['name' => 'Ahmad Fauzi'],
    ];

    $queueData = collect([
        (object) [
            'queue_number' => 'A-001',
            'service_name' => 'Pelayanan Umum',
            'registration_type' => 'online',
            'registered_at' => '2026-03-09 08:05:00',
            'completed_at' => '2026-03-09 08:12:00',
            'service_time' => 7,
            'operator_name' => 'Budi Santoso',
            'status' => 'completed',
        ],
        (object) [
            'queue_number' => 'A-002',
            'service_name' => 'Administrasi KTP',
            'registration_type' => 'onsite',
            'registered_at' => '2026-03-09 08:10:00',
            'completed_at' => '2026-03-09 08:20:00',
            'service_time' => 10,
            'operator_name' => 'Siti Rahayu',
            'status' => 'completed',
        ],
        (object) [
            'queue_number' => 'A-003',
            'service_name' => 'Akta Kelahiran',
            'registration_type' => 'online',
            'registered_at' => '2026-03-09 08:15:00',
            'completed_at' => null,
            'service_time' => null,
            'operator_name' => 'Ahmad Fauzi',
            'status' => 'serving',
        ],
        (object) [
            'queue_number' => 'A-004',
            'service_name' => 'Pelayanan Umum',
            'registration_type' => 'onsite',
            'registered_at' => '2026-03-09 08:20:00',
            'completed_at' => null,
            'service_time' => null,
            'operator_name' => '-',
            'status' => 'waiting',
        ],
    ]);

    $serviceChartData = [
        ['name' => 'Pelayanan Umum', 'total' => 52, 'completed' => 45],
        ['name' => 'Administrasi KTP', 'total' => 38, 'completed' => 31],
        ['name' => 'Akta Kelahiran', 'total' => 24, 'completed' => 18],
        ['name' => 'Surat Domisili', 'total' => 14, 'completed' => 11],
    ];

    $hourlyChartData = [
        ['hour' => '08:00', 'count' => 12],
        ['hour' => '09:00', 'count' => 24],
        ['hour' => '10:00', 'count' => 30],
        ['hour' => '11:00', 'count' => 18],
        ['hour' => '12:00', 'count' => 8],
        ['hour' => '13:00', 'count' => 15],
        ['hour' => '14:00', 'count' => 21],
    ];

    $registrationTypeData = [['name' => 'Online', 'value' => 74], ['name' => 'Onsite', 'value' => 54]];

    $today = date('Y-m-d');
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50" x-data="reportsPage()">

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
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $today }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ $today }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Layanan</label>
                        <select name="service_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="all">Semua Layanan</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Operator</label>
                        <select name="operator"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="all">Semua Operator</option>
                            @foreach ($operators as $op)
                                <option value="{{ $op->name }}">{{ $op->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- ===== STATISTICS CARDS ===== --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $statCards = [
                        ['Total Antrean', $totalQueue, 'text-gray-800', '+12% dari kemarin', 'users'],
                        [
                            'Selesai Dilayani',
                            $completedQueue,
                            'text-green-600',
                            $completionRate . '% dari total',
                            'check',
                        ],
                        [
                            'Rata-rata Waktu',
                            number_format($avgServiceTime, 1) . ' mnt',
                            'text-blue-600',
                            'Per pelayanan',
                            'clock',
                        ],
                        [
                            'Sedang Menunggu',
                            $waitingQueue,
                            'text-orange-600',
                            $servingQueue . ' sedang dilayani',
                            'trending',
                        ],
                    ];
                @endphp
                @foreach ($statCards as [$label, $value, $color, $sub, $icon])
                    <div class="bg-white rounded-2xl border shadow-sm p-5">
                        <div class="flex items-start justify-between mb-2">
                            <p class="text-sm font-medium text-gray-600">{{ $label }}</p>
                            <div class="text-gray-300">
                                @if ($icon === 'users')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                @elseif ($icon === 'check')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif ($icon === 'clock')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <p class="text-2xl font-bold {{ $color }}">{{ $value }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $sub }}</p>
                    </div>
                @endforeach
            </div>

            {{-- ===== CHARTS TABS ===== --}}
            <div x-data="{ chartTab: 'service' }">
                <div class="flex items-center justify-between mb-4">
                    {{-- Tab switcher --}}
                    <div class="bg-gray-100 p-1 rounded-xl inline-flex gap-1">
                        @foreach ([['service', 'Per Layanan'], ['hourly', 'Per Jam'], ['type', 'Tipe Registrasi']] as [$t, $l])
                            <button @click="chartTab = '{{ $t }}'"
                                :class="chartTab === '{{ $t }}' ? 'bg-white shadow text-blue-700 font-semibold' :
                                    'text-gray-500 hover:text-gray-700'"
                                class="px-4 py-2 rounded-lg text-sm transition-all">
                                {{ $l }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Export buttons --}}
                    <div class="flex gap-2">
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 border bg-white border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export PDF
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>

                {{-- Per Layanan --}}
                <div x-show="chartTab === 'service'">
                    <div class="bg-white rounded-2xl border shadow-sm p-6">
                        <h3 class="font-bold mb-1">Antrean Per Layanan</h3>
                        <p class="text-sm text-gray-500 mb-4">Jumlah antrean untuk setiap jenis layanan</p>
                        {{-- <canvas id="chart-per-service" style="height:380px"></canvas> --}}
                    </div>
                </div>

                {{-- Per Jam --}}
                <div x-show="chartTab === 'hourly'">
                    <div class="bg-white rounded-2xl border shadow-sm p-6">
                        <h3 class="font-bold mb-1">Antrean Per Jam</h3>
                        <p class="text-sm text-gray-500 mb-4">Distribusi antrean berdasarkan waktu</p>
                        {{-- <canvas id="chart-per-hour" style="height:380px"></canvas> --}}
                    </div>
                </div>

                {{-- Tipe Registrasi --}}
                <div x-show="chartTab === 'type'">
                    <div class="bg-white rounded-2xl border shadow-sm p-6">
                        <h3 class="font-bold mb-1">Tipe Registrasi</h3>
                        <p class="text-sm text-gray-500 mb-4">Perbandingan registrasi online vs onsite</p>
                        {{-- <canvas id="chart-reg-type" style="height:380px"></canvas> --}}
                    </div>
                </div>
            </div>

            {{-- ===== DATA TABLE ===== --}}
            <div class="bg-white rounded-2xl border shadow-sm">
                <div class="p-5 border-b">
                    <h3 class="font-bold">Detail Data Antrean</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Rincian lengkap setiap antrean</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                @foreach (['No. Antrean', 'Layanan', 'Tipe', 'Waktu Daftar', 'Waktu Selesai', 'Durasi', 'Operator', 'Status'] as $th)
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                        {{ $th }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($queueData as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono font-bold">{{ $item->queue_number }}</td>
                                    <td class="px-4 py-3">{{ $item->service_name }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $item->registration_type === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($item->registration_type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $item->registered_at ? \Carbon\Carbon::parse($item->registered_at)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $item->service_time ? $item->service_time . ' mnt' : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item->operator_name }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $item->status === 'completed'
                                                ? 'bg-green-100 text-green-700'
                                                : ($item->status === 'serving'
                                                    ? 'bg-blue-100 text-blue-700'
                                                    : 'bg-gray-100 text-gray-600') }}">
                                            {{ $item->status === 'completed' ? 'Selesai' : ($item->status === 'serving' ? 'Dilayani' : 'Menunggu') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-gray-400">Tidak ada data antrean</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function reportsPage() {
            return {};
        }

        document.addEventListener('DOMContentLoaded', () => {
            const serviceData = @json($serviceChartData);
            const hourlyData = @json($hourlyChartData);
            const regData = @json($registrationTypeData);

            // Bar chart – per service
            const barCtx = document.getElementById('chart-per-service');
            if (barCtx) {
                new Chart(barCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: serviceData.map(d => d.name),
                        datasets: [{
                                label: 'Total',
                                data: serviceData.map(d => d.total),
                                backgroundColor: '#3B82F6',
                                borderRadius: 4,
                            },
                            {
                                label: 'Selesai',
                                data: serviceData.map(d => d.completed),
                                backgroundColor: '#10B981',
                                borderRadius: 4,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true
                            },
                        },
                    },
                });
            }

            // Line chart – per hour
            const lineCtx = document.getElementById('chart-per-hour');
            if (lineCtx) {
                new Chart(lineCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: hourlyData.map(d => d.hour),
                        datasets: [{
                            label: 'Jumlah Antrean',
                            data: hourlyData.map(d => d.count),
                            borderColor: '#8B5CF6',
                            backgroundColor: 'rgba(139,92,246,0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                    },
                });
            }

            // Pie chart – reg type
            const pieCtx = document.getElementById('chart-reg-type');
            if (pieCtx) {
                new Chart(pieCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: regData.map(d => d.name),
                        datasets: [{
                            data: regData.map(d => d.value),
                            backgroundColor: ['#0088FE', '#00C49F', '#FFBB28'],
                            borderWidth: 2,
                            borderColor: '#fff',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` ${ctx.label}: ${ctx.parsed}`,
                                },
                            },
                        },
                    },
                });
            }
        });
    </script>
@endpush

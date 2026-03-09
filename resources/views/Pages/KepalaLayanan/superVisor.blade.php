@extends('layouts.testes')

@section('title', 'Dashboard Kepala Layanan - SAPA')

@section('content')
    <div class="min-h-screen bg-gray-50" x-data="supervisorDashboard()">

        <main class="container mx-auto max-w-7xl px-4 py-6">

            {{-- ===== TOP STATS (5 cards) ===== --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">

                @php
                    $stats = [
                        [
                            'label' => 'Total Antrean',
                            'value' => 150,
                            'color' => 'text-gray-800',
                            'icon' => 'users',
                            'bg' => 'text-blue-400',
                        ],
                        [
                            'label' => 'Selesai',
                            'value' => 120,
                            'color' => 'text-green-600',
                            'icon' => 'check',
                            'bg' => 'text-green-400',
                        ],
                        [
                            'label' => 'Sedang Dilayani',
                            'value' => 10,
                            'color' => 'text-blue-600',
                            'icon' => 'activity',
                            'bg' => 'text-blue-400',
                        ],
                        [
                            'label' => 'Menunggu',
                            'value' => 20,
                            'color' => 'text-orange-600',
                            'icon' => 'clock',
                            'bg' => 'text-orange-400',
                        ],
                        [
                            'label' => 'Avg. Waktu',
                            'value' => '5m',
                            'color' => 'text-purple-600',
                            'icon' => 'trending',
                            'bg' => 'text-purple-400',
                        ],
                    ];
                @endphp

                @foreach ($stats as $stat)
                    <div class="bg-white rounded-2xl border shadow-sm p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                                <p class="text-3xl font-bold {{ $stat['color'] }} mt-1">{{ $stat['value'] }}</p>
                            </div>
                            <div class="opacity-40 {{ $stat['bg'] }}">
                                @if ($stat['icon'] === 'users')
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                @elseif($stat['icon'] === 'check')
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif($stat['icon'] === 'activity')
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                @elseif($stat['icon'] === 'clock')
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ===== TABS ===== --}}
            <div x-data="{ activeTab: 'live' }">
                <div class="flex items-center justify-between mb-4 gap-4 flex-wrap">

                    {{-- Tab Buttons --}}
                    <div class="bg-gray-100 p-1 rounded-xl flex gap-1">
                        @foreach ([['live', 'Live Tracking'], ['analytics', 'Analitik'], ['history', 'Riwayat Layanan']] as [$tab, $label])
                            <button @click="activeTab = '{{ $tab }}'"
                                :class="activeTab === '{{ $tab }}' ? 'bg-white shadow text-blue-700 font-semibold' :
                                    'text-gray-500 hover:text-gray-700'"
                                class="px-4 py-2 rounded-lg text-sm transition-all">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Period Selector + Export --}}
                    <div class="flex items-center gap-3 flex-wrap">
                        <select x-model="period"
                            class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                        </select>
                        <a href="#"
                            class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export PDF
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>

                {{-- ===== TAB: LIVE TRACKING ===== --}}
                @include('Pages.KepalaLayanan.liveTracking')

                {{-- ===== TAB: ANALYTICS ===== --}}
                @include('Pages.KepalaLayanan.analytics')

                {{-- ===== TAB: HISTORY ===== --}}
                @include('Pages.KepalaLayanan.history')

            </div>{{-- end tabs --}}
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        function supervisorDashboard() {
            return {
                period: 'today'
            };
        }
    </script>
@endpush

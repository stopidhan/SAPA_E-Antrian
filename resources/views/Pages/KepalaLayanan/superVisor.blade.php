@extends('layouts.testes')

@section('title', 'Dashboard Kepala Layanan - SAPA')

@php
    $statCards = [
        [
            'label' => 'Total Antrean',
            'value' => 150,
            'color' => 'text-gray-800',
            'icon' =>
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM15 20H9m6 0h6M9 20H3" /></svg>',
        ],
        [
            'label' => 'Selesai',
            'value' => 120,
            'color' => 'text-green-600',
            'icon' =>
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Sedang Dilayani',
            'value' => 10,
            'color' => 'text-blue-600',
            'icon' =>
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>',
        ],
        [
            'label' => 'Menunggu',
            'value' => 20,
            'color' => 'text-orange-600',
            'icon' =>
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Avg. Waktu',
            'value' => '5m',
            'color' => 'text-purple-600',
            'icon' =>
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3l-4 4m0 4l4 4v-7m8-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
    ];
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50" x-data="supervisorDashboard()">

        <main class="container mx-auto max-w-7xl px-4 py-6">

            {{-- ===== TOP STATS (5 cards) ===== --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                <x-card :cards="$statCards" />
            </div>

            {{-- ===== CHARTS TABS ===== --}}
            <x-tab :tabs="[
                ['id' => 'live', 'label' => 'Live Tracking'],
                ['id' => 'analytics', 'label' => 'Analitik'],
                ['id' => 'history', 'label' => 'Riwayat Layanan'],
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

                {{-- ===== TAB: LIVE TRACKING ===== --}}
                @include('Pages.KepalaLayanan.liveTracking')

                {{-- ===== TAB: ANALYTICS ===== --}}
                @include('Pages.KepalaLayanan.analytics')

                {{-- ===== TAB: HISTORY ===== --}}
                @include('Pages.KepalaLayanan.history')
            </x-tab>

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

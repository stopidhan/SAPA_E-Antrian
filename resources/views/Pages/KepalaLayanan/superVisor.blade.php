@extends('layouts.testes')

@section('title', 'Dashboard Kepala Layanan - SAPA')

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
                    {{-- Export buttons — visibility controlled per tab via Alpine --}}
                    <template x-if="activeTab === 'live'">
                        <div class="flex gap-2">
                            <a href="{{ route('supervisor.export.live.pdf') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Export PDF
                            </a>
                            <a href="{{ route('supervisor.export.live.excel') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Excel
                            </a>
                        </div>
                    </template>

                    {{-- No export for analytics tab --}}

                    <template x-if="activeTab === 'history'">
                        <div class="flex gap-2">
                            <a href="{{ route('supervisor.export.history.pdf') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Export PDF
                            </a>
                            <a href="{{ route('supervisor.export.history.excel') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Excel
                            </a>
                        </div>
                    </template>
                @endslot

                {{-- ===== TAB: LIVE TRACKING ===== --}}
                @include('Pages.KepalaLayanan.liveTracking')

                {{-- ===== TAB: ANALYTICS ===== --}}
                @include('Pages.KepalaLayanan.analytics')

                {{-- ===== TAB: HISTORY ===== --}}
                @include('Pages.KepalaLayanan.history')
            </x-tab>

        </main>

        {{-- ===== DETAIL MODAL ===== --}}
        <x-modals.modal_queue-detail />
    </div>
@endsection

@push('scripts')
    <script>
        function supervisorDashboard() {
            return {
                period: 'today',
                detailData: null,
                detailLoading: false,
                pollingInterval: null,
                liveRegChart: null,

                init() {
                    // Start polling when on live tab
                    this.startPolling();
                    this.renderLiveRegChart();
                },

                renderLiveRegChart() {
                    const ctxRegType = document.getElementById('chart-live-reg-type');
                    if (ctxRegType) {
                        this.liveRegChart = new Chart(ctxRegType, {
                            type: 'doughnut',
                            data: {
                                labels: ['Online', 'Onsite'],
                                datasets: [{
                                    data: [{{ $registrationTypes['online'] ?? 0 }}, {{ $registrationTypes['onsite'] ?? 0 }}],
                                    backgroundColor: [
                                        'rgba(59, 130, 246, 0.8)', // Blue for Online
                                        'rgba(16, 185, 129, 0.8)' // Green for Onsite
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

                startPolling() {
                    // Poll every 10 seconds
                    this.pollingInterval = setInterval(() => {
                        this.fetchLiveData();
                    }, 10000);
                },

                async fetchLiveData() {
                    try {
                        const response = await fetch('{{ route('supervisor.api.live') }}', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });

                        if (!response.ok) return;

                        const data = await response.json();

                        // Update stat cards
                        if (data.statCards) {
                            const cardEls = document.querySelectorAll(
                                '.grid.grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-5 .bg-white');
                            data.statCards.forEach((card, i) => {
                                if (cardEls[i]) {
                                    const valueEl = cardEls[i].querySelector('.text-3xl');
                                    if (valueEl) valueEl.textContent = card.value;
                                }
                            });
                        }

                        // Update Registration Types Chart
                        if (data.registrationTypes && this.liveRegChart) {
                            this.liveRegChart.data.datasets[0].data = [
                                data.registrationTypes.online,
                                data.registrationTypes.onsite
                            ];
                            this.liveRegChart.update();
                        }
                    } catch (err) {
                        console.warn('Live polling error:', err);
                    }
                },

                async viewDetail(queueId) {
                    this.detailLoading = true;
                    this.detailData = null;
                    this.$dispatch('open-modal', 'queue-detail-modal');

                    try {
                        const response = await fetch(`/supervisor/api/queue/${queueId}`, {
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
                },

                destroy() {
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                    }
                }
            };
        }
    </script>
@endpush

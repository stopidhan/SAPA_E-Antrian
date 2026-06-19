@extends('layouts.testes')

@section('title', 'Dashboard Kepala Layanan - SAPA')

@section('content')
    <div class="min-h-screen bg-gray-50" x-data="supervisorDashboard()">

        {{-- Real-time Connection Status Indicator --}}
        <div class="container mx-auto max-w-7xl px-4 pt-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <template x-if="wsConnected">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Live — Real-time
                        </span>
                    </template>
                    <template x-if="!wsConnected">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            Offline — Reconnecting...
                        </span>
                    </template>
                </div>
                <template x-if="lastUpdate">
                    <span class="text-xs text-gray-400" x-text="'Update terakhir: ' + lastUpdate"></span>
                </template>
            </div>
        </div>

        <main class="container mx-auto max-w-7xl px-4 py-6">

            {{-- ===== TOP STATS (4 cards) ===== --}}
            <div id="top-stats-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <x-card :cards="array_slice($statCards, 0, 4)" />
            </div>

            {{-- ===== CHARTS TABS ===== --}}
            <x-tab :tabs="[
                ['id' => 'live', 'label' => 'Live Tracking'],
                ['id' => 'analytics', 'label' => 'Analitik'],
                ['id' => 'history', 'label' => 'Riwayat Layanan'],
            ]" :activeTab="request('tab', 'live')">
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
                liveRegChart: null,
                wsConnected: false,
                lastUpdate: null,
                fetchInProgress: false,

                init() {
                    this.renderLiveRegChart();
                    this.initWebSocket();
                },

                /**
                 * Initialize Laravel Echo + Reverb WebSocket connection.
                 * Replaces the old setInterval polling with instant event-driven updates.
                 * Uses the Echo instance already initialized by Vite in bootstrap.js/echo.js
                 */
                initWebSocket(retryCount = 0) {
                    // Guard: wait for Echo instance from Vite to be ready
                    if (typeof window.Echo === 'undefined' || typeof window.Echo.channel !== 'function') {
                        if (retryCount < 20) {
                            setTimeout(() => this.initWebSocket(retryCount + 1), 250);
                            return;
                        }
                        console.error('[Supervisor WebSocket] Echo instance not loaded by Vite after retries');
                        this.startFallbackPolling();
                        return;
                    }

                    try {
                        const channel = window.Echo.channel('queues.{{ auth()->user()->instance_id }}');

                        // Listen for queue status changes (called, serving, completed, skipped, cancelled)
                        channel.listen('QueueUpdated', (e) => {
                            console.log('[Supervisor WebSocket] QueueUpdated:', e.message, e.queue);
                            this.onRealtimeEvent('QueueUpdated', e);
                        });

                        // Listen for new queue check-ins (online/kiosk)
                        channel.listen('QueueCheckedIn', (e) => {
                            console.log('[Supervisor WebSocket] QueueCheckedIn:', e.queue);
                            this.onRealtimeEvent('QueueCheckedIn', e);
                        });

                        // Track connection state via Pusher connector
                        const pusher = window.Echo.connector.pusher;
                        pusher.connection.bind('connected', () => {
                            this.wsConnected = true;
                            console.log('[Supervisor WebSocket] Connected ✓');
                        });
                        pusher.connection.bind('disconnected', () => {
                            this.wsConnected = false;
                            console.log('[Supervisor WebSocket] Disconnected ✕');
                        });
                        pusher.connection.bind('error', (err) => {
                            this.wsConnected = false;
                            console.warn('[Supervisor WebSocket] Connection error:', err);
                        });

                    } catch (err) {
                        console.error('[Supervisor WebSocket] Init failed:', err);
                        // Graceful fallback: poll every 15s if WebSocket fails
                        this.startFallbackPolling();
                    }
                },

                /**
                 * Handle any real-time event by debouncing and fetching fresh data.
                 */
                onRealtimeEvent(eventName, payload) {
                    this.lastUpdate = new Date().toLocaleTimeString('id-ID');

                    // Debounce: if multiple events arrive within 500ms, only fetch once
                    if (this._debounceTimer) clearTimeout(this._debounceTimer);
                    this._debounceTimer = setTimeout(() => {
                        this.fetchLiveData();
                        this.fetchLivePartial();
                    }, 300);
                },

                /**
                 * Fallback polling in case WebSocket connection fails entirely.
                 */
                startFallbackPolling() {
                    console.warn('[Supervisor] Falling back to polling (15s interval)');
                    this._fallbackInterval = setInterval(() => {
                        this.fetchLiveData();
                        this.fetchLivePartial();
                    }, 15000);
                },

                renderLiveRegChart() {
                    const ctxRegType = document.getElementById('chart-live-reg-type');
                    if (ctxRegType) {
                        this.liveRegChart = new Chart(ctxRegType, {
                            type: 'doughnut',
                            data: {
                                labels: ['Online', 'Onsite'],
                                datasets: [{
                                    data: [{{ $registrationTypes['online'] ?? 0 }},
                                        {{ $registrationTypes['onsite'] ?? 0 }}
                                    ],
                                    backgroundColor: [
                                        'rgba(59, 130, 246, 0.8)',
                                        'rgba(16, 185, 129, 0.8)'
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

                /**
                 * Fetch JSON stats and update stat cards + chart.
                 */
                async fetchLiveData() {
                    if (this.fetchInProgress) return;
                    this.fetchInProgress = true;

                    try {
                        const url = '{{ route('supervisor.api.live') }}?_t=' + new Date().getTime();
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Cache-Control': 'no-cache',
                                'Pragma': 'no-cache'
                            }
                        });

                        if (!response.ok) return;

                        const data = await response.json();

                        // Update stat cards
                        if (data.statCards) {
                            const topCardEls = document.querySelectorAll('#top-stats-container .bg-white');
                            for (let i = 0; i < 4; i++) {
                                if (topCardEls[i] && data.statCards[i]) {
                                    const valueEl = topCardEls[i].querySelector('.text-3xl');
                                    if (valueEl) valueEl.textContent = data.statCards[i].value;
                                }
                            }

                            const perfCardEls = document.querySelectorAll('#performance-stats-container .bg-white');
                            for (let i = 0; i < 2; i++) {
                                if (perfCardEls[i] && data.statCards[i + 4]) {
                                    const valueEl = perfCardEls[i].querySelector('.text-3xl');
                                    if (valueEl) valueEl.textContent = data.statCards[i + 4].value;
                                }
                            }
                        }

                        // Update Registration Types Doughnut Chart
                        if (data.registrationTypes && this.liveRegChart) {
                            this.liveRegChart.data.datasets[0].data = [
                                data.registrationTypes.online,
                                data.registrationTypes.onsite
                            ];
                            // Only update chart if canvas is visible (not hidden by another tab)
                            // to prevent Chart.js fullSize layout error
                            if (this.liveRegChart.canvas && this.liveRegChart.canvas.offsetParent !== null) {
                                this.liveRegChart.update();
                            }
                        }
                    } catch (err) {
                        console.warn('[Supervisor] Live data fetch error:', err);
                    } finally {
                        this.fetchInProgress = false;
                    }
                },

                /**
                 * Fetch the rendered HTML partial for the live tracking tab sections
                 * (Operator Performance + Counter Status) and swap the DOM content.
                 * This ensures server-rendered Blade logic (colors, badges, bars) stays accurate.
                 */
                async fetchLivePartial() {
                    try {
                        const url = '{{ route('supervisor.api.live.partial') }}?_t=' + new Date().getTime();
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                                'Cache-Control': 'no-cache',
                                'Pragma': 'no-cache'
                            }
                        });

                        if (!response.ok) return;

                        const html = await response.text();

                        // Replace the operator performance section
                        const operatorContainer = document.getElementById('live-operator-performance');
                        const counterContainer = document.getElementById('live-counter-status');

                        if (operatorContainer || counterContainer) {
                            // Parse the returned HTML into a temporary document
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            const newOperator = doc.getElementById('live-operator-performance');
                            const newCounter = doc.getElementById('live-counter-status');

                            console.log('[Supervisor] Live Partial Parsed. Found Operator:', !!newOperator,
                                'Found Counter:', !!newCounter);

                            if (operatorContainer && newOperator) {
                                operatorContainer.innerHTML = newOperator.innerHTML;
                            }
                            if (counterContainer && newCounter) {
                                counterContainer.innerHTML = newCounter.innerHTML;
                                console.log('[Supervisor] Counter Status DOM swapped successfully');
                            }
                        }
                    } catch (err) {
                        console.warn('[Supervisor] Live partial fetch error:', err);
                    }
                },

                async viewDetail(queueId) {
                    this.detailLoading = true;
                    this.detailData = null;
                    this.$dispatch('open-modal', 'queue-detail-modal');

                    try {
                        const response = await fetch(`{{ route('supervisor.api.queue-detail', ':id') }}`.replace(':id',
                            queueId), {
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

                cleanFilters(e) {
                    const form = e.target;
                    const inputs = form.querySelectorAll('input, select');
                    const defaultDate = '{{ date('Y-m-d') }}';
                    
                    inputs.forEach(input => {
                        // Skip 'tab' hidden input to ensure tab stays active
                        if (input.name === 'tab') return;
                        
                        if (!input.value || input.value === 'all') {
                            input.disabled = true;
                        } else if ((input.name === 'start_date' || input.name === 'end_date') && input.value === defaultDate) {
                            input.disabled = true;
                        }
                    });

                    // Re-enable inputs after submission so the form remains usable if the user navigates back
                    setTimeout(() => {
                        inputs.forEach(input => input.disabled = false);
                    }, 500);
                },

                destroy() {
                    // Clean up WebSocket channel
                    if (window.Echo) {
                        window.Echo.leave('queues.{{ auth()->user()->instance_id }}');
                    }
                    if (this._fallbackInterval) {
                        clearInterval(this._fallbackInterval);
                    }
                }
            };
        }
    </script>
@endpush

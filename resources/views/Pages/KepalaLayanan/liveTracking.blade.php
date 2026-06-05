{{-- ===== TAB: LIVE TRACKING ===== --}}
<div x-show="activeTab === 'live'" class="space-y-6">

    {{-- Operator Performance --}}
    <div class="bg-white rounded-2xl border shadow-sm">
        <div class="p-6 border-b">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold">Kinerja Operator </h2>
            </div>
            <p class="text-sm text-gray-500 mt-1">Rata-rata waktu layanan per operator dengan indikator performa</p>
        </div>

        <div class="p-6 space-y-4">
            @forelse ($operatorPerformance as $perf)
                @php
                    $totalSvc = $perf->fast_services + $perf->medium_services + $perf->slow_services;
                    $fastPct = $totalSvc > 0 ? ($perf->fast_services / $totalSvc) * 100 : 0;
                    $medPct = $totalSvc > 0 ? ($perf->medium_services / $totalSvc) * 100 : 0;
                    $slowPct = $totalSvc > 0 ? ($perf->slow_services / $totalSvc) * 100 : 0;
                    $efficiency = $totalSvc > 0 ? round(($perf->fast_services / $totalSvc) * 100) : 0;

                    if ($perf->avg_service_time <= 2) {
                        $bgColor = 'bg-green-50';
                        $textColor = 'text-green-700';
                        $badgeBg = 'bg-green-600';
                        $badgeLabel = 'Sangat Cepat';
                    } elseif ($perf->avg_service_time <= 5) {
                        $bgColor = 'bg-yellow-50';
                        $textColor = 'text-yellow-700';
                        $badgeBg = 'bg-yellow-600';
                        $badgeLabel = 'Normal';
                    } else {
                        $bgColor = 'bg-red-50';
                        $textColor = 'text-red-700';
                        $badgeBg = 'bg-red-600';
                        $badgeLabel = 'Perlu Ditingkatkan';
                    }
                @endphp

                <div class="border rounded-xl p-5 {{ $bgColor }} border-gray-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                {{ str_replace('Loket ', '', $perf->counter_name) }}
                            </div>
                            <div>
                                <div class="font-bold text-lg text-gray-800">{{ $perf->operator_name }}</div>
                                <div class="text-sm text-gray-500">{{ $perf->counter_name }}</div>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-white text-xs font-semibold {{ $badgeBg }}">
                            {{ $badgeLabel }}
                        </span>
                    </div>

                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <div class="text-center p-3 bg-white rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Rata-rata Waktu</div>
                            <div class="text-2xl font-bold {{ $textColor }}">
                                {{ number_format($perf->avg_service_time, 1) }}
                                <span class="text-sm ml-0.5">menit</span>
                            </div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Total Dilayani</div>
                            <div class="text-2xl font-bold text-gray-800">{{ $perf->total_served }}</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Hari Ini</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $perf->today_served }}</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Efisiensi</div>
                            <div class="text-2xl font-bold text-green-600">{{ $efficiency }}%</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">Distribusi Waktu Layanan</span>
                            <span class="text-xs text-gray-500">Total: {{ $totalSvc }} layanan</span>
                        </div>
                        <div class="w-full h-8 bg-gray-100 rounded-lg overflow-hidden flex">
                            @if ($perf->fast_services > 0)
                                <div class="bg-green-500 flex items-center justify-center text-white text-xs font-semibold"
                                    style="width: {{ $fastPct }}%">
                                    @if ($fastPct > 10)
                                        {{ $perf->fast_services }}
                                    @endif
                                </div>
                            @endif
                            @if ($perf->medium_services > 0)
                                <div class="bg-yellow-500 flex items-center justify-center text-white text-xs font-semibold"
                                    style="width: {{ $medPct }}%">
                                    @if ($medPct > 10)
                                        {{ $perf->medium_services }}
                                    @endif
                                </div>
                            @endif
                            @if ($perf->slow_services > 0)
                                <div class="bg-red-500 flex items-center justify-center text-white text-xs font-semibold"
                                    style="width: {{ $slowPct }}%">
                                    @if ($slowPct > 10)
                                        {{ $perf->slow_services }}
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-3 mt-3">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-green-500 rounded flex-shrink-0"></div>
                                <div class="text-xs">
                                    <div class="font-semibold text-gray-700">1-2 menit</div>
                                    <div class="text-gray-500">{{ $perf->fast_services }} layanan</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-yellow-500 rounded flex-shrink-0"></div>
                                <div class="text-xs">
                                    <div class="font-semibold text-gray-700">3-5 menit</div>
                                    <div class="text-gray-500">{{ $perf->medium_services }} layanan</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-red-500 rounded flex-shrink-0"></div>
                                <div class="text-xs">
                                    <div class="font-semibold text-gray-700">6-10+ menit</div>
                                    <div class="text-gray-500">{{ $perf->slow_services }} layanan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p>Belum ada operator aktif</p>
                </div>
            @endforelse

            {{-- Performance Standard Legend --}}
            @if (count($operatorPerformance) > 0)
                <div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Standar Kinerja Waktu Layanan
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                            <span class="text-xs text-gray-600"><strong>Sangat Cepat:</strong> 1-2 menit</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-xs text-gray-600"><strong>Normal:</strong> 3-5 menit</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-600 rounded-full"></div>
                            <span class="text-xs text-gray-600"><strong>Perlu Ditingkatkan:</strong> 6-10+ menit</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Counter Status + Registration Type --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Counter Status --}}
        <div class="bg-white rounded-2xl border shadow-sm">
            <div class="p-5 border-b">
                <h3 class="font-bold">Status Loket Real-time</h3>
                <p class="text-sm text-gray-500 mt-0.5">Monitoring kinerja per loket</p>
            </div>
            <div class="p-5 space-y-4">
                @forelse ($counters as $counter)
                    <div class="border rounded-xl p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                                    {{ str_replace('Loket ', '', $counter->name) }}
                                </div>
                                <div>
                                    <div class="font-semibold">{{ $counter->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $counter->operatorName ?? 'Tidak ada operator' }}</div>
                                </div>
                            </div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $counter->status === 'serving' ? 'bg-green-100 text-green-700' : ($counter->status === 'calling' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500') }}">
                                {{ $counter->status === 'serving' ? 'Melayani' : ($counter->status === 'calling' ? 'Memanggil' : 'Idle') }}
                            </span>
                        </div>
                        @if ($counter->current_queue)
                            <div class="bg-blue-50 rounded-lg p-3 text-center">
                                <div class="text-xs text-gray-500 mb-1">Antrean Saat Ini</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $counter->current_queue }}</div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <p>Belum ada loket aktif</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Registration Type --}}
        <div class="bg-white rounded-2xl border shadow-sm">
            <div class="p-5 border-b">
                <h3 class="font-bold">Tipe Pendaftaran</h3>
                <p class="text-sm text-gray-500 mt-0.5">Distribusi online vs onsite</p>
            </div>
            <div class="p-5">
                <div style="height:380px" class="relative flex justify-center">
                    <canvas id="chart-live-reg-type"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

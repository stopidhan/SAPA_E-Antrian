{{-- ===== TAB: HISTORY ===== --}}
<div x-show="activeTab === 'history'" x-cloak>
    <div class="space-y-6">
        {{-- ===== FILTER SECTION ===== --}}
        <div class="bg-white rounded-2xl border shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h2 class="text-lg font-bold">Filter Riwayat Layanan</h2>
            </div>

            <form method="GET" action="{{ route('supervisor.dashboard') }}" class="space-y-4" @submit="cleanFilters">
                {{-- Hidden input to keep active tab --}}
                <input type="hidden" name="tab" value="history">

                {{-- Row 1: Search & Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <x-input-text name="search" label="Cari Antrean"
                            placeholder="No. Antrean atau Nama Pelanggan..." value="{{ request('search') }}" />
                    </div>
                    <x-input-date name="start_date" label="Tanggal Mulai" value="{{ request('start_date', date('Y-m-d')) }}" />
                    <x-input-date name="end_date" label="Tanggal Akhir" value="{{ request('end_date', date('Y-m-d')) }}" />
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
                    <a href="{{ route('supervisor.dashboard', ['tab' => 'history']) }}">
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

        {{-- ===== TABLE SECTION ===== --}}
        <div class="bg-white rounded-2xl border shadow-sm">
            <div class="p-5 border-b flex justify-between items-center">
                <div>
                    <h3 class="font-bold">Daftar Riwayat</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Menampilkan antrean yang telah selesai</p>
                </div>
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
            ]" :rows="$completedQueues" emptyMessage="Belum ada riwayat layanan">
                @foreach ($completedQueues as $queue)
                    @php
                        $duration =
                            $queue->started_at && $queue->completed_at
                                ? round(\Carbon\Carbon::parse($queue->started_at)->diffInMinutes($queue->completed_at))
                                : 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold font-mono text-primary">{{ $queue->queue_number }}</td>
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
                            <span
                                class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-700">Selesai</span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($queue->photo_path)
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end">
                                <x-action-buttons :view="true" viewAction="viewDetail({{ $queue->id }})" :edit="false" :delete="false" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>
    </div>
</div>

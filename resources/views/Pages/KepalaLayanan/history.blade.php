{{-- ===== TAB: HISTORY ===== --}}
<div x-show="activeTab === 'history'">
    <div class="bg-white rounded-2xl border shadow-sm">
        <div class="p-5 border-b">
            <h3 class="font-bold">Riwayat Layanan (FR-29)</h3>
            <p class="text-sm text-gray-500 mt-0.5">Log pelayanan dengan foto dan deskripsi</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        @foreach (['No. Antrean', 'Layanan', 'Kategori', 'Waktu', 'Durasi', 'Operator', 'Status', 'Foto'] as $th)
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                {{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $completedQueues = [
                            (object) [
                                'queue_number' => 'A001',
                                'service_name' => 'Pembuatan KTP',
                                'service_category' => 'Administrasi',
                                'description' => 'Pembuatan KTP baru',
                                'completed_at' => '2026-03-08 10:30:00',
                                'started_at' => '2026-03-08 10:25:00',
                                'counter_id' => 'c1',
                                'photo_path' => 'path/to/photo.jpg',
                            ],
                            (object) [
                                'queue_number' => 'A002',
                                'service_name' => 'Pembayaran Pajak',
                                'service_category' => 'Keuangan',
                                'description' => 'Pembayaran PBB',
                                'completed_at' => '2026-03-08 11:00:00',
                                'started_at' => '2026-03-08 10:50:00',
                                'counter_id' => 'c2',
                                'photo_path' => null,
                            ],
                        ];
                    @endphp

                    @forelse($completedQueues as $queue)
                        @php
                            $duration =
                                $queue->started_at && $queue->completed_at
                                    ? round(
                                        \Carbon\Carbon::parse($queue->started_at)->diffInMinutes($queue->completed_at),
                                    )
                                    : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold font-mono">{{ $queue->queue_number }}</td>
                            <td class="px-4 py-3">{{ $queue->service_name }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $queue->service_category ?? '-' }}</div>
                                @if ($queue->description)
                                    <div class="text-xs text-gray-400 max-w-xs truncate">{{ $queue->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $queue->completed_at ? \Carbon\Carbon::parse($queue->completed_at)->format('H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">{{ $duration }} menit</td>
                            <td class="px-4 py-3 text-gray-600">Loket {{ str_replace('c', '', $queue->counter_id) }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Selesai</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($queue->photo_path)
                                    <span
                                        class="text-xs text-green-600 border border-green-300 rounded px-2 py-0.5">✓</span>
                                @else
                                    <span
                                        class="text-xs text-gray-400 border border-gray-200 rounded px-2 py-0.5">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-gray-400">Belum ada riwayat layanan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

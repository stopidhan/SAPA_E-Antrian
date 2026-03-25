@props([
    'title' => '',
    'subtitle' => '',
    'columns' => [],
    'rows' => collect(),
    'emptyMessage' => 'Tidak ada data',
    'perPage' => 10,
])

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                @foreach ($columns as $column)
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">
                        {{ $column }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @if ($rows->count())
                {{ $slot }}
            @else
                <tr>
                    <td colspan="{{ count($columns) }}" class="text-center py-10 text-gray-400">
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if (method_exists($rows, 'hasPages'))
    <div class="flex items-center justify-between px-4 py-4 border-t">
        <div class="text-sm text-gray-600">
            Menampilkan <span
                class="font-semibold">{{ $rows->count() > 0 ? ($rows->currentPage() - 1) * $rows->perPage() + 1 : 0 }}</span>-<span
                class="font-semibold">{{ ($rows->currentPage() - 1) * $rows->perPage() + $rows->count() }}</span> dari
            <span class="font-semibold">{{ $rows->total() }}</span>
        </div>

        <div class="flex gap-2">
            {{-- Previous Button --}}
            @if ($rows->onFirstPage())
                <button disabled
                    class="px-2 py-1 border border-gray-200 rounded text-gray-400 bg-gray-50 cursor-not-allowed text-sm">
                    &lt;
                </button>
            @else
                <a href="{{ $rows->previousPageUrl() }}"
                    class="px-2 py-1 border border-gray-200 rounded text-gray-600 hover:bg-blue-50 hover:border-blue-300 transition-colors text-sm">
                    &lt;
                </a>
            @endif

            {{-- Next Button --}}
            @if ($rows->hasMorePages())
                <a href="{{ $rows->nextPageUrl() }}"
                    class="px-2 py-1 border border-gray-200 rounded text-gray-600 hover:bg-blue-50 hover:border-blue-300 transition-colors text-sm">
                    &gt;
                </a>
            @else
                <button disabled
                    class="px-2 py-1 border border-gray-200 rounded text-gray-400 bg-gray-50 cursor-not-allowed text-sm">
                    &gt;
                </button>
            @endif
        </div>
    </div>
@endif

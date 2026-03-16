@props([
    'title' => '',
    'subtitle' => '',
    'columns' => [],
    'rows' => collect(),
    'emptyMessage' => 'Tidak ada data',
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

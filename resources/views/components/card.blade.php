@props([
    'cards' => [],
])


@foreach ($cards as $card)
    <div class="bg-white rounded-2xl border shadow-sm p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-medium text-gray-600">{{ $card['label'] }}</p>
            <div class="text-gray-300">
                {!! $card['icon'] !!}
            </div>
        </div>
        <p class="text-3xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
        @if (isset($card['sub']) && $card['sub'])
            <p class="text-xs text-gray-400 mt-1">{{ $card['sub'] }}</p>
        @endif
    </div>
@endforeach

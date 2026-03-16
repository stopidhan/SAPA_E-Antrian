@props([
    'tabs' => [],
    'activeTab' => null,
])

@php
    $activeTab = $activeTab ?? ($tabs[0]['id'] ?? null);
@endphp

<div x-data="{ activeTab: '{{ $activeTab }}' }">
    <div class="flex items-center justify-between mb-4">
        {{-- Tab switcher --}}
        <div class="bg-gray-100 p-1 rounded-xl inline-flex gap-1">
            @foreach ($tabs as $tab)
                <button @click="activeTab = '{{ $tab['id'] }}'"
                    :class="activeTab === '{{ $tab['id'] }}' ? 'bg-white shadow text-blue-700 font-semibold' :
                        'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 rounded-lg text-sm transition-all">
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Header content (export buttons, etc) --}}
        <div class="flex gap-2">
            {{ $header ?? '' }}
        </div>
    </div>

    {{-- Tab contents (render hanya sekali, bukan loop) --}}
    <div>
        {{ $slot }}
    </div>
</div>

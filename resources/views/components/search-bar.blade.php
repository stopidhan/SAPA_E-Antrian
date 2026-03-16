@props([
    'name' => 'search',
    'placeholder' => 'Cari...',
    'class' => '',
    'inputClass' =>
        'w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white',
])

<div class="flex-1 relative {{ $class }}">
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
    </svg>
    <input type="text" name="{{ $name }}" x-model="{{ $name }}" placeholder="{{ $placeholder }}"
        class="{{ $inputClass }}">
</div>

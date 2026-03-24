@props([
    'name' => '',
    'label' => '',
    'options' => [], // Array of ['value' => '', 'label' => '']
    'value' => '',
    'required' => false,
    'readonly' => false,
    'class' => '',
    'buttonClass' =>
        'px-5 py-2.5 rounded-lg border border-gray-300 cursor-pointer text-slate-900 text-sm font-medium outline-none bg-white hover:bg-gray-50 w-full text-left flex items-center justify-between',
    'menuClass' =>
        'absolute top-full left-0 mt-1 rounded-lg [box-shadow:0_8px_19px_-7px_rgba(215,215,215,1)] bg-white py-2 z-[1000] w-full divide-y divide-gray-200 max-h-96 overflow-auto',
    'optionClass' => 'px-5 py-2.5 hover:bg-gray-50 text-slate-600 text-sm font-medium cursor-pointer',
])

<div class="space-y-1 {{ $class }}" x-data="{ open: false, selected: '{{ $value }}', selectedLabel: '{{ $options[array_search($value, array_column($options, 'value'))]['label'] ?? 'Pilih...' }}' }">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <!-- Hidden input for form submission -->
    <input type="hidden" name="{{ $name }}" x-model="selected" @if ($required) required @endif>

    <!-- Dropdown trigger -->
    <div class="relative">
        <button type="button" @click="open = !open" :class="{ 'ring-2 ring-blue-500': open }"
            class="{{ $buttonClass }} @if ($readonly) cursor-not-allowed pointer-events-none @endif">
            <span x-text="selectedLabel"></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 fill-gray-500 inline" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M11.99997 18.1669a2.38 2.38 0 0 1-1.68266-.69733l-9.52-9.52a2.38 2.38 0 1 1 3.36532-3.36532l7.83734 7.83734 7.83734-7.83734a2.38 2.38 0 1 1 3.36532 3.36532l-9.52 9.52a2.38 2.38 0 0 1-1.68266.69734z"
                    clip-rule="evenodd" data-original="#000000" />
            </svg>
        </button>

        <!-- Dropdown menu -->
        <ul x-show="open" @click.away="open = false" class="{{ $menuClass }}">
            @foreach ($options as $option)
                <li @click="selected = '{{ $option['value'] }}'; selectedLabel = '{{ $option['label'] }}'; open = false"
                    class="{{ $optionClass }}">
                    {{ $option['label'] }}
                </li>
            @endforeach
        </ul>
    </div>
</div>

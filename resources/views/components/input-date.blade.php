@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'required' => false,
    'readonly' => false,
    'error' => null,
    'class' => '',
    'inputClass' =>
        'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
])

<div class="space-y-1 {{ $class }}">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <input type="date" name="{{ $name }}" value="{{ $value }}"
        {{ $attributes->except(['class', 'name', 'value', 'required', 'readonly']) }}
        class="{{ $inputClass }} @if ($error) border-red-500 focus:ring-red-500 focus:border-red-500 @endif @if ($readonly) cursor-not-allowed pointer-events-none @endif"
        @if ($required) required @endif @if ($readonly) readonly @endif>

    @if ($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif
</div>

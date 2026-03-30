@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'type' => 'text', // ADD THIS
    'required' => false,
    'readonly' => false,
    'class' => '',
    'inputClass' =>
        'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
])

<div class="space-y-1.5 {{ $class }}">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" @change="hasChanges = true" value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        class="{{ $inputClass }} @if ($readonly) cursor-not-allowed pointer-events-none @endif"
        @if ($required) required @endif @if ($readonly) readonly @endif>
</div>

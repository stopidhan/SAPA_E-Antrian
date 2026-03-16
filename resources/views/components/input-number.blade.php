@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'min' => null,
    'max' => null,
    'required' => false,
    'readonly' => false,
    'class' => '',
    'inputClass' =>
        'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none',
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
    <input type="number" name="{{ $name }}" x-model="form.{{ $name }}" @input="hasChanges = true"
        value="{{ $value }}" placeholder="{{ $placeholder }}"
        class="{{ $inputClass }} @if ($readonly) cursor-not-allowed pointer-events-none @endif"
        @if ($min !== null) min="{{ $min }}" @endif
        @if ($max !== null) max="{{ $max }}" @endif
        @if ($required) required @endif @if ($readonly) readonly @endif>
</div>

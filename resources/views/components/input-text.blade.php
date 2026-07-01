@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'type' => 'text',
    'required' => false,
    'readonly' => false,
    'error' => null,
    'class' => '',
    'xModel' => null,
    'inputClass' =>
        'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary',
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
        class="{{ $inputClass }} @if ($error) border-red-500 focus:ring-red-500 focus:border-red-500 @endif @if ($readonly) cursor-not-allowed pointer-events-none @endif"
        @if ($xModel) x-model="{{ $xModel }}" @endif
        @if ($required) required @endif @if ($readonly) readonly @endif>

    @if ($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif
</div>

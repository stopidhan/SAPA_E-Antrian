@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'rows' => 3,
    'required' => false,
    'readonly' => false,
    'error' => null,
    'class' => '',
    'textareaClass' =>
        'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary resize-none',
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
    <textarea name="{{ $name }}" @change="hasChanges = true" rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        class="{{ $textareaClass }} @if ($error) border-red-500 focus:ring-red-500 @endif @if ($readonly) cursor-not-allowed pointer-events-none @endif"
        @if ($required) required @endif @if ($readonly) readonly @endif
        {{ $attributes->except('class') }}>{{ $value }}</textarea>

    @if ($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif
</div>

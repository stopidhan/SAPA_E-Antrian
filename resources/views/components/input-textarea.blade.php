@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'rows' => 3,
    'required' => false,
    'readonly' => false,
    'class' => '',
    'textareaClass' =>
        'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none',
])

<div class="space-y-1.5 {{ $class }}">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif
    <textarea name="{{ $name }}" x-model="form.{{ $name }}" @input="hasChanges = true"
        rows="{{ $rows }}" placeholder="{{ $placeholder }}"
        class="{{ $textareaClass }} @if ($readonly) cursor-not-allowed pointer-events-none @endif"
        @if ($required) required @endif @if ($readonly) readonly @endif>{{ $value }}</textarea>
</div>

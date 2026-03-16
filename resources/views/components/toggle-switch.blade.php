@props([
    'name' => '',
    'checked' => false,
    'class' => '',
    'inputClass' => 'sr-only peer',
    'toggleClass' => 'w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[\'\'\] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all',
])

<label class="relative inline-flex items-center cursor-pointer {{ $class }}">
    <input type="checkbox" class="{{ $inputClass }}" x-model="{{ $name }}"
        @if ($checked) checked @endif>
    <div class="{{ $toggleClass }}"></div>
</label>

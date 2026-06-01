@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'disabled' => false,
    'xBind' => [],
    'xShow' => null,
    'xClick' => null,
])

@php
    $baseClasses = 'font-semibold rounded-lg transition-colors flex items-center justify-center gap-2';

    $sizeClasses = match ($size) {
        'xs' => 'py-1 px-2 text-xs',
        'sm' => 'py-1.5 px-3 text-xs',
        'md' => 'py-2 px-4 text-sm',
        'lg' => 'py-2.5 px-4 text-base',
        default => 'py-2 px-4 text-sm',
    };

    $variantClasses = match ($variant) {
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
        'success' => 'bg-green-600 hover:bg-green-700 text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'white' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300',
        'outline' => 'border border-blue-600 text-blue-600 hover:bg-blue-50 bg-transparent',
        'outline-danger'
            => 'border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-red-500 hover:border-red-200 bg-transparent',
        'dotted'
            => 'border-2 border-dashed border-gray-300 hover:border-blue-400 text-gray-500 hover:text-blue-600 bg-transparent',
        default => 'bg-blue-600 hover:bg-blue-700 text-white',
    };

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';

    $finalClasses =
        $baseClasses .
        ' ' .
        $sizeClasses .
        ' ' .
        $variantClasses .
        ' ' .
        $disabledClasses .
        ' ' .
        ($attributes['class'] ?? '');
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $finalClasses]) }}
    @if ($disabled) disabled @endif
    @if ($xBind) @foreach ($xBind as $directive => $expression)
            x-bind:{{ $directive }}="{{ $expression }}"
        @endforeach @endif
    @if ($xShow) x-show="{{ $xShow }}" @endif
    @if ($xClick) @click="{{ $xClick }}" @endif>
    @if ($icon)
        <span>{!! $icon !!}</span>
    @endif
    <span>{{ $slot }}</span>
</button>

@php
    if (!isset($instance)) {
        $instanceSlug = request()->route('instance_slug');
        $instance = $instanceSlug ? \App\Models\Instance::where('instance_slug', $instanceSlug)->first() : null;
        if (!$instance && auth()->check()) {
            $instance = auth()->user()->instance;
        }
    }

    if (!function_exists('hex2rgb')) {
        function hex2rgb($hex) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) == 3) {
                $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                $b = hexdec(substr($hex,2,1).substr($hex,2,1));
            } elseif (strlen($hex) == 6) {
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
            } else {
                return '37 99 235'; // default blue-600
            }
            return "$r $g $b";
        }
    }

    $primaryRgb = $instance && $instance->brand_color ? hex2rgb($instance->brand_color) : '37 99 235';
    $secondaryRgb = $instance && $instance->secondary_color ? hex2rgb($instance->secondary_color) : '16 185 129';
@endphp

<style>
    :root {
        --color-primary: {{ $primaryRgb }};
        --color-secondary: {{ $secondaryRgb }};
    }
</style>

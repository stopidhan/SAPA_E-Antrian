<x-guest-layout max-width="sm:max-w-5xl">
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Silakan pilih instansi/organisasi yang ingin Anda tuju.') }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($instances as $instance)
            <a href="{{ route('booking.register', ['instance_slug' => $instance->instance_slug]) }}"
                class="block rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden group border border-gray-200 bg-white flex flex-col">

                <div class="h-24 w-full" style="background-color: {{ $instance->brand_color ?? '#e5e7eb' }};"></div>

                <div class="relative z-10 flex flex-col items-center text-center px-6 pb-6 -mt-12 h-full">
                    @if ($instance->logo)
                        <div
                            class="h-24 w-24 mb-4 bg-white rounded-2xl p-2 shadow flex items-center justify-center ring-4 ring-white">
                            <img src="{{ Str::startsWith($instance->logo, ['http://', 'https://']) ? $instance->logo : Storage::url($instance->logo) }}"
                                alt="{{ $instance->instance_name }} Logo" class="max-h-full max-w-full object-contain">
                        </div>
                    @else
                        <div
                            class="h-24 w-24 mb-4 bg-white rounded-2xl p-3 shadow flex items-center justify-center ring-4 ring-white text-gray-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                    @endif

                    <h5
                        class="mb-2 text-xl font-bold tracking-tight text-gray-900 group-hover:text-indigo-700 transition-colors">
                        {{ $instance->instance_name }}</h5>
                    <p class="font-medium text-sm text-gray-600 mt-auto">
                        {{ $instance->address ?? 'Alamat tidak tersedia' }}</p>
                </div>
            </a>
        @endforeach
    </div>

    @if ($instances->isEmpty())
        <div class="p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300"
            role="alert">
            <span class="font-medium">Perhatian!</span> Belum ada instansi yang terdaftar.
        </div>
    @endif
</x-guest-layout>

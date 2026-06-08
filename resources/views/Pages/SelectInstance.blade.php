<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Silakan pilih instansi/organisasi yang ingin Anda tuju.') }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($instances as $instance)
            <a href="{{ route('booking.register', ['instance_slug' => $instance->instance_slug]) }}" 
               class="block p-6 max-w-sm bg-white rounded-lg border border-gray-200 shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $instance->instance_name }}</h5>
                <p class="font-normal text-gray-700 dark:text-gray-400">{{ $instance->address ?? 'Alamat tidak tersedia' }}</p>
            </a>
        @endforeach
    </div>

    @if($instances->isEmpty())
        <div class="p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
            <span class="font-medium">Perhatian!</span> Belum ada instansi yang terdaftar.
        </div>
    @endif
</x-guest-layout>
@props([
    'show' => false,
    'variant' => 'user',
    'itemName' => '',
    'processing' => false,
])

@php
    $configs = [
        'user' => [
            'title' => 'Hapus Pengguna',
            'placeholder' => 'Pengguna Ini',
            'description' => 'Semua data pengguna akan dihapus secara permanen. Tindakan ini tidak dapat dikembalikan.',
        ],
        'service' => [
            'title' => 'Hapus Layanan',
            'placeholder' => 'Layanan Ini',
            'description' => 'Semua data layanan akan dihapus secara permanen. Tindakan ini tidak dapat dikembalikan.',
        ],
    ];

    $variantConfig = $configs[$variant] ?? $configs['user'];
    $displayName = $itemName ?: $variantConfig['placeholder'];
    $displayTitle = $variantConfig['title'] . ' "' . $displayName . '"';
@endphp

<div x-data="{ show: @json($show), processing: @json($processing) }" @submit.document="show = false">
    @if ($show)
        <div class="fixed inset-0 p-3 sm:p-4 flex justify-center items-center w-full h-full z-[1000] before:fixed before:inset-0 before:w-full before:h-full before:bg-[rgba(0,0,0,0.5)] overflow-auto"
            @click="!processing && $dispatch('close-modal')">
            <div class="w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg bg-white shadow-lg rounded-xl p-4 sm:p-5 md:p-6 relative"
                @click.stop>
                <div class="flex items-start gap-3 sm:gap-3.5 md:gap-4">
                    <!-- Icon -->
                    <div
                        class="w-11 h-11 sm:w-12 sm:h-12 md:w-13 md:h-13 lg:w-14 lg:h-14 flex-shrink-0 flex items-center justify-center rounded-xl bg-[#FF383C]">
                        <svg class="w-6 h-6 sm:w-6.5 sm:h-6.5 md:w-7 md:h-7" viewBox="0 0 35 35" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M28.4375 8.02051L27.5338 22.6404C27.3028 26.3757 27.1874 28.2434 26.2512 29.5862C25.7881 30.25 25.1923 30.8103 24.501 31.2313C23.1031 32.083 21.2319 32.083 17.4894 32.083C13.742 32.083 11.8684 32.083 10.4694 31.2297C9.77783 30.808 9.18167 30.2467 8.71891 29.5817C7.78295 28.2368 7.67003 26.3665 7.44422 22.626L6.5625 8.02051"
                                stroke="white" stroke-width="2" stroke-linecap="round" />
                            <path
                                d="M4.375 8.02116H30.625M23.4146 8.02116L22.4191 5.96743C21.7578 4.6032 21.427 3.92108 20.8566 3.49567C20.7302 3.4013 20.5962 3.31736 20.456 3.24468C19.8244 2.91699 19.0664 2.91699 17.5503 2.91699C15.9962 2.91699 15.2192 2.91699 14.577 3.25842C14.4347 3.33409 14.2989 3.42143 14.171 3.51953C13.5941 3.96218 13.2718 4.66925 12.6271 6.08341L11.7438 8.02116"
                                stroke="white" stroke-width="2" stroke-linecap="round" />
                            <path d="M13.8535 24.0625V15.3125" stroke="white" stroke-width="2" stroke-linecap="round" />
                            <path d="M21.1465 24.0625V15.3125" stroke="white" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <!-- Text Content -->
                    <div class="flex-1 min-w-0">
                        <h3 class="subtitle-medium sm:body-medium md:heading4-medium leading-tight text-gray-900">
                            {{ $displayTitle }}
                        </h3>
                        <p class="text-gray-600 caption sm:subtitle-medium md:subtitle-medium mt-1 sm:mt-1.5 md:mt-2">
                            {{ $variantConfig['description'] }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <svg @click="!processing && $dispatch('close-modal')" xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 sm:w-4.5 sm:h-4.5 md:w-5 md:h-5 cursor-pointer shrink-0 fill-gray-400 hover:fill-red-500"
                        :class="{ 'opacity-50 cursor-not-allowed': processing }" viewBox="0 0 320.591 320.591">
                        <path
                            d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
                            data-original="#000000" />
                        <path
                            d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
                            data-original="#000000" />
                    </svg>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-2.5 md:gap-3 mt-6 sm:mt-7 md:mt-8 justify-end">
                    <button type="button" @click="!processing && $dispatch('close-modal')"
                        class="subtitle-medium sm:body-medium px-4 sm:px-5 md:px-6 py-2 rounded border border-gray-900 text-gray-900 hover:bg-gray-100"
                        :disabled="processing">
                        Kembali
                    </button>
                    <button type="button" @click="$dispatch('confirm-delete')"
                        class="subtitle-medium sm:body-medium px-4 sm:px-5 md:px-6 py-2 rounded bg-red-500 text-white hover:bg-red-600"
                        :disabled="processing">
                        {{ processing ? 'Menghapus...' : 'Hapus' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<style scoped>
    .modal-enter-active,
    .modal-leave-active {
        transition: opacity 0.3s ease;
    }

    .modal-enter-from,
    .modal-leave-to {
        opacity: 0;
    }

    .modal-enter-active .relative,
    .modal-leave-active .relative {
        transition: transform 0.3s ease;
    }

    .modal-enter-from .relative,
    .modal-leave-to .relative {
        transform: scale(0.95);
    }
</style>

@props([
    'show' => false,
    'variant' => 'user',
    'itemName' => '',
])

@php
    $configs = [
        'user' => [
            'title' => 'Ubah Status User',
            'placeholder' => 'User Ini',
            'description' => 'Tindakan ini akan mengubah status keaktifan user.',
        ],
        'service' => [
            'title' => 'Hapus Layanan',
            'placeholder' => 'Layanan Ini',
            'description' => 'Semua data layanan akan dihapus secara permanen. Tindakan ini tidak dapat dikembalikan.',
        ],
    ];

    $variantConfig = $configs[$variant] ?? $configs['user'];
    $displayName = $itemName ?: $variantConfig['placeholder'];
@endphp

<div @keydown.escape.window="closeToggleModal()">
    <x-modal name="toggle-user" :show="$show" maxWidth="md">
        <div class="p-6 space-y-4">
            <!-- Icon -->
            <div class="flex justify-center">
                <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center rounded-full bg-yellow-100">
                    <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-center text-gray-900">{{ $variantConfig['title'] }}</h3>

            <!-- Content -->
            <div class="space-y-3">
                <p class="text-sm text-center text-gray-600">{{ $variantConfig['description'] }}</p>

                <!-- User Info -->
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Nama User:</p>
                    <p class="font-semibold text-gray-900" x-text="selectedUser?.name || 'User'"></p>
                </div>

                <!-- Current Status -->
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Status Saat Ini:</p>
                    <div class="flex items-center gap-2">
                        <span x-show="selectedUser?.is_active"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktif
                        </span>
                        <span x-show="!selectedUser?.is_active"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Nonaktif
                        </span>
                    </div>
                </div>

                <!-- New Status -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Status Akan Diubah Menjadi:</p>
                    <div class="flex items-center gap-2">
                        <span x-show="selectedUser?.is_active"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Nonaktif
                        </span>
                        <span x-show="!selectedUser?.is_active"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                <x-button type="button" variant="secondary" @click="closeToggleModal()">
                    Batal
                </x-button>
                <x-button type="button" variant="primary" :xBind="['disabled' => 'isToggling']" @click="submitToggle()">
                    <span x-show="!isToggling">Konfirmasi</span>
                    <span x-show="isToggling" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Memproses...
                    </span>
                </x-button>
            </div>
        </div>
    </x-modal>
</div>

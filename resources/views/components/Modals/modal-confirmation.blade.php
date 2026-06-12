@props([
    'name' => 'toggle-user',
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
        'toggle-service' => [
            'title' => 'Ubah Status Layanan',
            'placeholder' => 'Layanan Ini',
            'description' => 'Tindakan ini akan mengubah status keaktifan layanan.',
        ],
        'content' => [
            'title' => 'Hapus Konten',
            'placeholder' => 'Konten Ini',
            'description' => 'Semua data konten akan dihapus secara permanen. Tindakan ini tidak dapat dikembalikan.',
        ],
        'logout' => [
            'title' => 'Konfirmasi Logout',
            'placeholder' => '',
            'description' => 'Apakah Anda yakin ingin keluar dari sistem? Sesi Anda akan diakhiri.',
        ],
        'close-session' => [
            'title' => 'Konfirmasi Tutup Sesi',
            'placeholder' => '',
            'description' => 'Apakah Anda yakin ingin menutup sesi loket ini?',
        ],
    ];

    $variantConfig = $configs[$variant] ?? $configs['user'];
    $displayName = $itemName ?: $variantConfig['placeholder'];
@endphp

<div x-data="{
    closeModal() {
        $dispatch('close-modal', '{{ $name }}');
    }
}" @keydown.escape.window="closeModal()">
    <x-modal :name="$name" :show="$show" maxWidth="md">
        <div class="p-6 space-y-4">
            <!-- Icon -->
            <div class="flex justify-center">
                <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center rounded-full {{ in_array($variant, ['user', 'toggle-service', 'logout', 'close-session']) ? 'bg-yellow-100' : 'bg-red-100' }}">
                    @if(in_array($variant, ['user', 'toggle-service', 'logout', 'close-session']))
                    <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @else
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    @endif
                </div>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-center text-gray-900">{{ $variantConfig['title'] }}</h3>

            <!-- Content -->
            <div class="space-y-3">
                <p class="text-sm text-center text-gray-600">{{ $variantConfig['description'] }}</p>

                @if($variant === 'user')
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
                @elseif($variant === 'toggle-service')
                    <!-- Service Info -->
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-1">Nama Layanan:</p>
                        <p class="font-semibold text-gray-900" x-text="selectedService?.service_name || 'Layanan'"></p>
                    </div>

                    <!-- Current Status -->
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-1">Status Saat Ini:</p>
                        <div class="flex items-center gap-2">
                            <span x-show="selectedService?.is_active"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                            <span x-show="!selectedService?.is_active"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Nonaktif
                            </span>
                        </div>
                    </div>

                    <!-- New Status -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-1">Status Akan Diubah Menjadi:</p>
                        <div class="flex items-center gap-2">
                            <span x-show="selectedService?.is_active"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Nonaktif
                            </span>
                            <span x-show="!selectedService?.is_active"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                        </div>
                    </div>
                @elseif(in_array($variant, ['logout', 'close-session']))
                    {{-- Logout dan Close Session variant — no extra info needed --}}
                @else
                    <!-- Other Variants Info -->
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500 mb-1">Item yang dipilih:</p>
                        <p class="font-semibold text-gray-900" id="delete-item-name">{{ $displayName }}</p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                @if(in_array($variant, ['user', 'toggle-service']))
                    <x-button type="button" variant="secondary" @click="closeModal()">
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
                @elseif($variant === 'logout')
                    <x-button type="button" variant="secondary" @click="closeModal()">
                        Batal
                    </x-button>
                    <form method="POST" action="{{ route('logout', ['instance_slug' => request()->route('instance_slug') ?? optional(auth()->user()->instance)->instance_slug ?? 'admin']) }}" class="m-0">
                        @csrf
                        <x-button type="submit" variant="danger">
                            Ya, Logout
                        </x-button>
                    </form>
                @elseif($variant === 'close-session')
                    <x-button type="button" variant="secondary" @click="closeModal()">
                        Batal
                    </x-button>
                    <x-button type="button" variant="danger" @click="$dispatch('confirm-close-session'); closeModal()">
                        Ya, Tutup Sesi
                    </x-button>
                @else
                    <x-button type="button" variant="secondary" @click="closeModal()">
                        Batal
                    </x-button>
                    <form id="deleteForm" method="POST" action="" class="m-0">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="primary">
                            Konfirmasi
                        </x-button>
                    </form>
                @endif
            </div>
        </div>
    </x-modal>
</div>

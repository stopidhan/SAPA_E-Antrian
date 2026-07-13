@extends('layouts.staff')

@section('title', 'Konfigurasi Sistem - SAPA')

@php
    $withSidebar = true;
@endphp

@section('content')
    <div class="bg-gray-50 flex flex-col" x-data="adminDashboard(@js($config), @js($services), @js($slots))">

        <main class="flex-1 overflow-auto">
            <div class="container mx-auto px-4 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- LEFT COLUMN: System Config + Services --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- System Configuration --}}
                        <div class="bg-white rounded-2xl border shadow-sm" id="config-section">
                            <div class="p-6 border-b">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <h2 class="text-lg font-bold">Konfigurasi Sistem Utama</h2>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Atur pengaturan dasar sistem antrean </p>
                            </div>

                            <form @submit.prevent="saveConfig" class="p-6 space-y-6">

                                {{-- TTS Toggle --}}
                                <div class="flex items-center justify-between">
                                    <div class="space-y-1">
                                        <label class="flex items-center gap-2 font-medium text-gray-700">
                                            Suara TTS Otomatis
                                        </label>
                                        <p class="text-sm text-gray-500">Aktifkan suara pemanggilan otomatis</p>
                                    </div>
                                    <x-toggle-switch name="config.ttsEnabled" />
                                </div>

                                {{-- Bahasa TTS --}}
                                <div x-show="config.ttsEnabled">
                                    @php
                                        $ttsOptions = [
                                            ['value' => 'id-ID', 'label' => 'Bahasa Indonesia (id-ID)'],
                                            ['value' => 'en-US', 'label' => 'English (en-US)'],
                                        ];
                                    @endphp
                                    <x-input-dropdown label="Bahasa TTS (Text-to-Speech)" :options="$ttsOptions"
                                        x-model="config.ttsLanguage" />
                                </div>

                                {{-- Maksimal Booking per Hari --}}
                                <div class="grid grid-cols-1 gap-4">
                                    <x-input-number label="Maksimal Antrean On-Site (Offline) / Hari"
                                        name="maxOfflineBookingsPerDay" placeholder="100" min="1" max="2000"
                                        x-model.number="config.maxOfflineBookingsPerDay" />
                                </div>

                                {{-- Zona Waktu --}}
                                <div>
                                    @php
                                        $timezoneOptions = [
                                            ['value' => 'Asia/Jakarta', 'label' => 'Asia/Jakarta (WIB)'],
                                            ['value' => 'Asia/Makassar', 'label' => 'Asia/Makassar (WITA)'],
                                            ['value' => 'Asia/Jayapura', 'label' => 'Asia/Jayapura (WIT)'],
                                        ];
                                    @endphp
                                    <x-input-dropdown label="Zona Waktu" :options="$timezoneOptions" x-model="config.timezone" />
                                </div>

                                <hr class="border-gray-200">

                                {{-- Jam Operasional --}}
                                <div>
                                    <div class="mb-4">
                                        <h3 class="text-md font-bold text-gray-900">Jam Operasional</h3>
                                        <p class="text-sm text-gray-500">Atur jadwal buka dan tutup instansi Anda setiap
                                            harinya.</p>
                                    </div>

                                    <div
                                        class="border border-gray-200 rounded-xl overflow-hidden bg-white max-w-2xl shadow-sm">
                                        {{-- Header --}}
                                        <div
                                            class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                                            <span class="text-sm font-semibold text-gray-700">Hari & Status</span>
                                            <span class="text-sm font-semibold text-gray-700 mr-8">Jam Layanan</span>
                                        </div>

                                        {{-- List of Days --}}
                                        <div class="divide-y divide-gray-100">
                                            <template x-for="(day, index) in config.operationalHours"
                                                :key="index">
                                                <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/50 transition-colors"
                                                    :class="!day.isOpen ? 'bg-gray-50/50' : ''">

                                                    {{-- Toggle & Day Name --}}
                                                    <div class="flex items-center gap-4 w-1/3">
                                                        <label
                                                            class="relative inline-flex items-center cursor-pointer shrink-0">
                                                            <input type="checkbox" x-model="day.isOpen"
                                                                class="sr-only peer">
                                                            <div
                                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                                            </div>
                                                        </label>
                                                        <span class="font-medium"
                                                            :class="day.isOpen ? 'text-gray-900' : 'text-gray-400'"
                                                            x-text="day.name"></span>
                                                    </div>

                                                    {{-- Time Range --}}
                                                    <div class="flex-1 flex justify-end">
                                                        <div x-show="day.isOpen" class="flex items-center gap-3">
                                                            <div class="relative">
                                                                <input type="time" x-model="day.openTime"
                                                                    class="w-28 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                                            </div>
                                                            <span class="text-gray-400 text-sm font-medium">s/d</span>
                                                            <div class="relative">
                                                                <input type="time" x-model="day.closeTime"
                                                                    class="w-28 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                                            </div>
                                                        </div>
                                                        <div x-show="!day.isOpen" class="w-[245px] flex justify-end pr-2">
                                                            <span
                                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-600 border border-red-100">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                                Tutup / Libur
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                        {{-- Manajemen Slot Antrean Online --}}
                        <div class="bg-white rounded-2xl border shadow-sm" id="slot-section">
                            <div class="p-6 border-b flex items-start justify-between sm:items-center gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h2 class="text-lg font-bold">Manajemen Slot Antrean Online</h2>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">Atur kapasitas dan durasi antrean online untuk
                                        aplikasi pengunjung.</p>
                                </div>
                                <x-button type="button" variant="primary" @click="openSlotDialog()"
                                    icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /> </svg>'>
                                    Tambah Slot Antrean
                                </x-button>
                            </div>

                            <div class="p-6">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                            <tr>
                                                <th class="px-6 py-3 font-semibold">Waktu Mulai</th>
                                                <th class="px-6 py-3 font-semibold">Waktu Selesai</th>
                                                <th class="px-6 py-3 font-semibold">Kapasitas (Orang)</th>
                                                <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="slot in slots" :key="slot.id">
                                                <tr class="border-b hover:bg-gray-50/50">
                                                    <td class="px-6 py-4 font-medium text-gray-900"
                                                        x-text="slot.start_time"></td>
                                                    <td class="px-6 py-4 font-medium text-gray-900"
                                                        x-text="slot.end_time"></td>
                                                    <td class="px-6 py-4">
                                                        <span
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                                                            <span x-text="slot.capacity"></span>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <div class="flex justify-end">
                                                            <x-action-buttons :edit="true"
                                                                editAction="openSlotDialog(slot)" :delete="true"
                                                                deleteAction="openDeleteSlotModal(slot)" />
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template x-if="slots.length === 0">
                                                <tr>
                                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                                                        Belum ada slot waktu yang dikonfigurasi.</td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Services Management (Integrated with Counters) --}}
                        <div class="bg-white rounded-2xl border shadow-sm">
                            <div class="p-6 border-b flex items-start justify-between sm:items-center gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <h2 class="text-lg font-bold">Manajemen Layanan & Loket</h2>
                                    </div>
                                <p class="text-sm text-gray-500 mt-1">Kelola layanan dan Loket yang tersedia di sistem
                                        antrean</p>
                                </div>
                                <x-button type="button" variant="primary" @click="openServiceDialog()"
                                    icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /> </svg>'>
                                    Tambah Layanan
                                </x-button>
                            </div>

                            <div class="p-6 space-y-4">

                                {{-- Empty state --}}
                                <template x-if="!isLoadingServices && services.length === 0">
                                    <div
                                        class="flex flex-col items-center justify-center py-16 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                        <div class="bg-white p-4 rounded-full shadow-sm mb-4">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Layanan</h3>
                                        <p class="text-sm text-gray-500 max-w-sm mb-6">Mulai dengan menambahkan layanan
                                            baru
                                            untuk mengelola antrean di instansi Anda.</p>
                                        <x-button type="button" variant="primary" @click="openServiceDialog()"
                                            icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /> </svg>'>
                                            Tambah Layanan Pertama
                                        </x-button>
                                    </div>
                                </template>

                                {{-- Service List with Counters --}}
                                <template x-for="service in services" :key="service.id">
                                    <div
                                        class="border rounded-xl overflow-hidden hover:border-blue-200 hover:shadow-md transition-all">

                                        {{-- Service Header --}}
                                        <div
                                            class="p-4 bg-gradient-to-r from-blue-50 to-transparent flex items-center justify-between">
                                            <div class="flex items-center gap-4 flex-1">
                                                <div
                                                    class="w-12 h-12 rounded-lg flex items-center justify-center bg-primary/10 text-primary font-bold text-sm">
                                                    <span x-text="service.queue_prefix"></span>
                                                </div>
                                                <div>
                                                    <div class="font-semibold" x-text="service.service_name"></div>
                                                    <p class="text-sm text-gray-500" x-text="service.description"></p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <template x-if="service.is_active">
                                                    <x-label-status :value="'active'" />
                                                </template>
                                                <template x-if="!service.is_active">
                                                    <x-label-status :value="'inactive'" />
                                                </template>

                                                <div class="flex items-center gap-2 ml-4">
                                                    <button @click="openToggleModal(service)"
                                                        class="p-2 text-gray-400 hover:text-primary transition-colors"
                                                        title="Toggle status">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                        </svg>
                                                    </button>
                                                    <button @click="editService(service)"
                                                        class="p-2 text-gray-400 hover:text-yellow-600 transition-colors"
                                                        title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button @click="openDeleteModal(service)"
                                                        class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                                        title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Service Counters List --}}
                                        <div class="border-t px-4 py-3 bg-gray-50">
                                            <div class="text-sm font-medium text-gray-600 mb-3">Loket Melayani Layanan
                                                Ini:</div>

                                            <template x-if="service.counters && service.counters.length > 0">
                                                <div class="flex flex-wrap gap-2">
                                                    <template x-for="counter in service.counters" :key="counter.id">
                                                        <div
                                                            class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-1">
                                                            <span class="text-sm font-medium text-gray-700"
                                                                x-text="counter.counter_number"></span>
                                                            <template x-if="counter.is_active">
                                                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                            </template>
                                                            <template x-if="!counter.is_active">
                                                                <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            <template x-if="!service.counters || service.counters.length === 0">
                                                <p class="text-sm text-gray-400 italic">Belum ada Loket yang melayani
                                                    layanan ini</p>
                                            </template>
                                        </div>

                                    </div>
                                </template>

                            </div>
                        </div>

                    </div>

                    {{-- RIGHT COLUMN: Summary & Actions --}}
                    <div class="space-y-6">

                        {{-- Summary Card --}}
                        <div class="bg-white rounded-2xl border shadow-sm p-6 space-y-4">
                            <h3 class="text-lg font-bold">Ringkasan Konfigurasi</h3>

                            <div>
                                <p class="text-sm text-gray-500">TTS Otomatis</p>
                                <p class="text-lg font-semibold"
                                    :class="savedConfig.ttsEnabled ? 'text-green-600' : 'text-gray-400'"
                                    x-text="savedConfig.ttsEnabled ? 'Aktif' : 'Non-aktif'">
                                </p>
                            </div>
                            <hr class="border-gray-100">
                            <div x-show="savedConfig.ttsEnabled">
                                <p class="text-sm text-gray-500">Bahasa TTS</p>
                                <p class="text-lg font-semibold text-gray-900" x-text="savedConfig.ttsLanguage"></p>
                            </div>
                            <hr class="border-gray-100" x-show="savedConfig.ttsEnabled">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Max Booking Online</p>
                                    <p class="text-3xl font-bold text-primary"
                                        x-text="slots.reduce((sum, slot) => sum + parseInt(slot.capacity || 0), 0)"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Max Booking Offline</p>
                                    <p class="text-3xl font-bold text-primary"
                                        x-text="savedConfig.maxOfflineBookingsPerDay"></p>
                                </div>
                            </div>
                            <hr class="border-gray-100">
                            <div>
                                <p class="text-sm text-gray-500">Zona Waktu</p>
                                <p class="text-lg font-semibold text-gray-900" x-text="savedConfig.timezone"></p>
                            </div>
                            <hr class="border-gray-100">
                            <div>
                                <p class="text-sm text-gray-500">Layanan Aktif</p>
                                <p class="text-3xl font-bold text-primary" x-text="activeServicesCount"></p>
                                <p class="text-sm text-gray-400">dari <span x-text="services.length"></span> layanan</p>
                            </div>
                            <hr class="border-gray-100">

                            <x-button type="button" size="lg" class="w-full" @click="saveConfig()">
                                Simpan Konfigurasi
                            </x-button>
                        </div>

                    </div>
                </div>
        </main>

        {{-- Service Form Modal --}}
        @include('components.Modals.modal_service-form')

        {{-- Confirmation Modals --}}
        @include('components.Modals.modal-confirmation', [
            'variant' => 'toggle-service',
            'name' => 'toggle-service-modal',
        ])
        @include('components.Modals.modal-confirmation', [
            'variant' => 'service',
            'name' => 'delete-service-modal',
        ])
        @include('components.Modals.modal-confirmation', [
            'variant' => 'slot',
            'name' => 'delete-slot-modal',
        ])

        <!-- Slot Form Modal -->
        @include('components.Modals.modal_slot_booking-form')

    </div>
@endsection

@push('scripts')
    <script>
        function adminDashboard(initialConfig, initialServices, initialSlots) {
            const defaultDays = [{
                    name: 'Senin',
                    isOpen: true,
                    openTime: '08:00',
                    closeTime: '16:00'
                },
                {
                    name: 'Selasa',
                    isOpen: true,
                    openTime: '08:00',
                    closeTime: '16:00'
                },
                {
                    name: 'Rabu',
                    isOpen: true,
                    openTime: '08:00',
                    closeTime: '16:00'
                },
                {
                    name: 'Kamis',
                    isOpen: true,
                    openTime: '08:00',
                    closeTime: '16:00'
                },
                {
                    name: 'Jumat',
                    isOpen: true,
                    openTime: '08:00',
                    closeTime: '16:00'
                },
                {
                    name: 'Sabtu',
                    isOpen: false,
                    openTime: '08:00',
                    closeTime: '14:00'
                },
                {
                    name: 'Minggu',
                    isOpen: false,
                    openTime: '08:00',
                    closeTime: '14:00'
                },
            ];

            const initialOpHours = initialConfig.operational_hours;

            return {
                services: initialServices,
                slots: initialSlots,
                isLoadingServices: false,
                editingService: null,
                selectedService: null,
                isToggling: false,
                serviceForm: {},
                countersList: [],
                slotsList: [],
                showServiceDialog: false,

                // Slot states
                editingSlot: null,
                isSavingSlot: false,
                slotForm: {
                    start_time: '08:00',
                    end_time: '09:00',
                    capacity: 10
                },

                config: {
                    ttsEnabled: initialConfig.tts_enabled,
                    maxOfflineBookingsPerDay: initialConfig.max_offline_bookings_per_day,
                    operationalHours: (initialOpHours && initialOpHours.length === 7) ? initialOpHours : defaultDays,
                    ttsLanguage: initialConfig.tts_language || 'id-ID',
                    timezone: initialConfig.timezone || 'Asia/Jakarta',
                },

                savedConfig: {
                    ttsEnabled: initialConfig.tts_enabled,
                    maxOfflineBookingsPerDay: initialConfig.max_offline_bookings_per_day,
                    operationalHours: (initialOpHours && initialOpHours.length === 7) ? initialOpHours : defaultDays,
                    ttsLanguage: initialConfig.tts_language || 'id-ID',
                    timezone: initialConfig.timezone || 'Asia/Jakarta',
                },

                get activeServicesCount() {
                    return this.services.filter(s => s.is_active).length;
                },

                init() {
                    // Data is pre-loaded via Blade
                },

                // ============================
                // Configuration
                // ============================

                async fetchConfig() {
                    try {
                        const response = await fetch("{{ route('instance.config.show') }}", {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.config.ttsEnabled = result.data.tts_enabled;
                            this.config.maxOnlineBookingsPerDay = result.data.max_online_bookings_per_day;
                            this.config.maxOfflineBookingsPerDay = result.data.max_offline_bookings_per_day;
                            this.config.operationalHours = result.data.operational_hours || defaultDays;
                            this.config.ttsLanguage = result.data.tts_language || 'id-ID';
                            this.config.timezone = result.data.timezone || 'Asia/Jakarta';

                            this.savedConfig.ttsEnabled = result.data.tts_enabled;
                            this.savedConfig.maxOnlineBookingsPerDay = result.data.max_online_bookings_per_day;
                            this.savedConfig.maxOfflineBookingsPerDay = result.data.max_offline_bookings_per_day;
                            this.savedConfig.operationalHours = result.data.operational_hours || defaultDays;
                            this.savedConfig.ttsLanguage = result.data.tts_language || 'id-ID';
                            this.savedConfig.timezone = result.data.timezone || 'Asia/Jakarta';
                        }
                    } catch (error) {
                        console.error('Error fetching config:', error);
                    }
                },

                async saveConfig() {
                    try {
                        const response = await fetch("{{ route('instance.config.update') }}", {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                            body: JSON.stringify({
                                tts_enabled: this.config.ttsEnabled,
                                max_offline_bookings_per_day: this.config.maxOfflineBookingsPerDay,
                                operational_hours: this.config.operationalHours,
                                tts_language: this.config.ttsLanguage,
                                timezone: this.config.timezone,
                            }),
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.savedConfig.ttsEnabled = this.config.ttsEnabled;
                            this.savedConfig.maxOfflineBookingsPerDay = this.config.maxOfflineBookingsPerDay;
                            this.savedConfig.operationalHours = this.config.operationalHours;
                            this.savedConfig.ttsLanguage = this.config.ttsLanguage;
                            this.savedConfig.timezone = this.config.timezone;
                            showToast(result.message, 'success');
                        } else {
                            showToast(result.message || 'Gagal menyimpan konfigurasi', 'error');
                        }
                    } catch (error) {
                        console.error('Error saving config:', error);
                        showToast('Terjadi kesalahan saat menyimpan konfigurasi', 'error');
                    }
                },

                // ============================
                // Services
                // ============================

                async fetchServices() {
                    this.isLoadingServices = true;
                    try {
                        const response = await fetch("{{ route('services.index') }}", {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.services = result.data;
                        }
                    } catch (error) {
                        console.error('Error fetching services:', error);
                    } finally {
                        this.isLoadingServices = false;
                    }
                },

                openServiceDialog(service = null) {
                    if (service) {
                        this.editingService = JSON.parse(JSON.stringify(service));
                        this.serviceForm = {
                            service_name: service.service_name,
                            queue_prefix: service.queue_prefix,
                            description: service.description,
                            slot_duration: service.slot_duration ?? 60,
                            slot_capacity: service.slot_capacity ?? 5,
                            is_active: service.is_active,
                            fast_max: service.performance_standards?.fast?.max ?? 5,
                            normal_max: service.performance_standards?.normal?.max ?? 10,
                            slot_duration: service.slot_duration ?? 120,
                            slot_capacity: service.slot_capacity ?? 10,
                        };
                        this.countersList = JSON.parse(JSON.stringify(service.counters || []));
                    } else {
                        this.editingService = null;
                        this.serviceForm = {
                            service_name: '',
                            queue_prefix: '',
                            description: '',
                            slot_duration: 60,
                            slot_capacity: 5,
                            is_active: true,
                            fast_max: 2,
                            normal_max: 5,
                            slot_duration: 15,
                            slot_capacity: 1,
                        };
                        this.countersList = [];
                    }
                    this.$dispatch('open-modal', 'service-form');
                },

                closeModal() {
                    this.$dispatch('close-modal', 'service-form');
                },

                addCounter() {
                    this.countersList.push({
                        id: null,
                        counter_number: '',
                    });
                },

                removeCounter(index) {
                    this.countersList.splice(index, 1);
                },

                async saveService() {
                    const url = this.editingService ?
                        `{{ route('services.update', ':id') }}`.replace(':id', this.editingService.id) :
                        "{{ route('services.store') }}";
                    const method = this.editingService ? 'PATCH' : 'POST';

                    const payload = {
                        ...this.serviceForm,
                        counters: this.countersList,
                    };

                    try {
                        const response = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                            body: JSON.stringify(payload),
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.fetchServices();
                            this.closeModal();
                            showToast(result.message, 'success');
                        } else if (result.errors) {
                            // Validation errors
                            const messages = Object.values(result.errors).flat().join('\n');
                            showToast(messages, 'error');
                        } else {
                            showToast(result.message || 'Terjadi kesalahan', 'error');
                        }
                    } catch (error) {
                        console.error('Error saving service:', error);
                        showToast('Terjadi kesalahan saat menyimpan layanan', 'error');
                    }
                },

                openToggleModal(service) {
                    this.selectedService = service;
                    this.$dispatch('open-modal', 'toggle-service-modal');
                },

                closeToggleModal() {
                    this.$dispatch('close-modal', 'toggle-service-modal');
                    this.selectedService = null;
                },

                async submitToggle() {
                    if (!this.selectedService || this.isToggling) return;
                    this.isToggling = true;
                    try {
                        const response = await fetch(`{{ route('services.toggle', ['service' => 999999]) }}`.replace('999999', this
                            .selectedService.id), {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.fetchServices();
                            this.closeToggleModal();
                            showToast(result.message, 'success');
                        } else {
                            showToast(result.message || 'Gagal mengubah status', 'error');
                        }
                    } catch (error) {
                        console.error('Error toggling service:', error);
                        showToast('Terjadi kesalahan saat mengubah status', 'error');
                    } finally {
                        this.isToggling = false;
                    }
                },

                openDeleteModal(service) {
                    this.selectedService = service;
                    const form = document.getElementById('deleteForm-delete-service-modal');
                    if (form) {
                        form.action = `{{ route('services.destroy', ['service' => 999999]) }}`.replace('999999', service.id);
                    }
                    const nameEl = document.getElementById('delete-item-name-delete-service-modal');
                    if (nameEl) {
                        nameEl.innerText = service.service_name;
                    }
                    this.$dispatch('open-modal', 'delete-service-modal');
                },

                editService(service) {
                    this.openServiceDialog(service);
                },

                // ============================
                // Slots
                // ============================
                openSlotDialog(slot = null) {
                    if (slot) {
                        this.editingSlot = slot;
                        this.slotForm = {
                            start_time: slot.start_time.substring(0, 5),
                            end_time: slot.end_time.substring(0, 5),
                            capacity: slot.capacity
                        };
                    } else {
                        this.editingSlot = null;
                        this.slotForm = {
                            start_time: '08:00',
                            end_time: '09:00',
                            capacity: 10
                        };
                    }
                    this.$dispatch('open-modal', 'slot-form');
                },

                closeSlotDialog() {
                    this.$dispatch('close-modal', 'slot-form');
                    setTimeout(() => {
                        this.editingSlot = null;
                    }, 300);
                },

                async saveSlot() {
                    this.isSavingSlot = true;
                    try {
                        const response = await fetch("{{ route('instance.slots.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                            body: JSON.stringify({
                                id: this.editingSlot ? this.editingSlot.id : null,
                                start_time: this.slotForm.start_time,
                                end_time: this.slotForm.end_time,
                                capacity: this.slotForm.capacity
                            }),
                        });
                        const result = await response.json();
                        if (result.success) {
                            // Update slot list directly without reload for better UX
                            if (this.editingSlot) {
                                const index = this.slots.findIndex(s => s.id === this.editingSlot.id);
                                if (index !== -1) {
                                    this.slots[index] = result.data;
                                }
                            } else {
                                this.slots.push(result.data);
                                // Sort slots by start_time
                                this.slots.sort((a, b) => a.start_time.localeCompare(b.start_time));
                            }
                            this.closeSlotDialog();
                            showToast(result.message, 'success');
                        } else {
                            const errorMsg = result.errors ? Object.values(result.errors).flat().join('\n') : (result
                                .message || 'Gagal menyimpan slot');
                            showToast(errorMsg, 'error');
                        }
                    } catch (error) {
                        console.error('Error saving slot:', error);
                        showToast('Terjadi kesalahan saat menyimpan slot', 'error');
                    } finally {
                        this.isSavingSlot = false;
                    }
                },

                openDeleteSlotModal(slot) {
                    const form = document.getElementById('deleteForm-delete-slot-modal');
                    if (form) {
                        form.action = `{{ route('instance.slots.destroy', ['slot' => 999999]) }}`.replace('999999', slot.id);
                    }
                    const nameEl = document.getElementById('delete-item-name-delete-slot-modal');
                    if (nameEl) {
                        nameEl.innerText =
                            `${slot.start_time.substring(0,5)} - ${slot.end_time.substring(0,5)} (Kapasitas: ${slot.capacity})`;
                    }
                    this.$dispatch('open-modal', 'delete-slot-modal');
                },
            };
        }
    </script>
@endpush

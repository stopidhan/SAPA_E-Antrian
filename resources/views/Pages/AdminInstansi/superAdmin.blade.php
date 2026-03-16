@extends('layouts.testes')

@section('title', 'Admin Dashboard - SAPA')

@php
    $withSidebar = true;
@endphp

@section('content')
    <div class="bg-gray-50 flex flex-col" x-data="adminDashboard()">

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

                            <form @submit.prevent class="p-6 space-y-6">

                                {{-- TTS Toggle --}}
                                <div class="flex items-center justify-between">
                                    <div class="space-y-1">
                                        <label class="flex items-center gap-2 font-medium text-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.536 8.464a5 5 0 010 7.072M12 6v12m-4.536-9.536a5 5 0 000 7.072" />
                                            </svg>
                                            Suara TTS Otomatis
                                        </label>
                                        <p class="text-sm text-gray-500">Aktifkan suara pemanggilan otomatis</p>
                                    </div>
                                    <x-toggle-switch name="config.ttsEnabled" checked="false" />
                                </div>

                                {{-- Maksimal Booking per Hari --}}
                                <x-input-number name="maxBookingsPerDay" label="Maksimal Booking Online per Hari"
                                    placeholder="5" min="1" max="50"></x-input-number>

                                {{-- <hr class="border-gray-100"> --}}

                                {{-- Repeat Count --}}
                                {{-- <div class="space-y-2">
                                    <label for="repeat-count" class="block font-medium text-gray-700">
                                        Jumlah Pengulangan Panggilan
                                    </label>
                                    <input id="repeat-count" type="number" min="1" max="5"
                                        x-model.number="config.callRepeatCount"
                                        class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <p class="text-sm text-gray-500">Berapa kali nomor akan dipanggil ulang</p>
                                </div> --}}

                            </form>
                        </div>

                        {{-- Services Management --}}
                        <div class="bg-white rounded-2xl border shadow-sm">
                            <div class="p-6 border-b flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold">Manajemen Layanan</h2>
                                    <p class="text-sm text-gray-500 mt-0.5">Kelola layanan yang tersedia di sistem antrean
                                    </p>
                                </div>
                                <x-button type="submit" variant="success"
                                    icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /> </svg>'>
                                    Tambah Layanan
                                </x-button>
                            </div>

                            <div class="p-6 space-y-4">

                                {{-- Empty state --}}
                                <template x-if="services.length === 0">
                                    <div class="text-center py-10 text-gray-500">
                                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="mb-4">Belum ada layanan yang ditambahkan</p>
                                        <button @click="openAddDialog()"
                                            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Tambah Layanan Pertama
                                        </button>
                                    </div>
                                </template>

                                {{-- Service list --}}
                                <template x-for="service in services" :key="service.id">
                                    <div
                                        class="border rounded-xl p-4 flex items-stretch hover:border-blue-200 transition-colors">
                                        {{-- Left Content --}}
                                        <div class="flex flex-col gap-3">
                                            {{-- Service Info --}}
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-xl"
                                                    :style="`background-color: ${service.color}`">
                                                    <span x-text="service.code"></span>
                                                </div>
                                                <div>
                                                    <div class="font-semibold mb-1" x-text="service.name"></div>
                                                    <template x-if="service.is_active">
                                                        <x-label-status :value="'active'" />
                                                    </template>
                                                    <template x-if="!service.is_active">
                                                        <x-label-status :value="'inactive'" />
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- Estimated Time --}}
                                            <div class="flex items-center gap-2 text-sm">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-gray-600">Estimasi Waktu :</span>
                                                <span class="font-medium text-gray-500">15 menit</span>
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex ml-auto items-center">
                                            <x-action-buttons :toggle="true" />
                                        </div>
                                    </div>
                                </template>

                            </div>
                        </div>

                        {{-- Services Category Management --}}
                        <div class="bg-white rounded-2xl border shadow-sm">
                            <div class="p-6 border-b flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold">Manajemen Kategori Layanan</h2>
                                    <p class="text-sm text-gray-500 mt-0.5">Kelola kategori layanan yang tersedia di sistem
                                        antrean
                                    </p>
                                </div>
                                <x-button type="submit" variant="success"
                                    icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /> </svg>'>
                                    Tambah Kategori Layanan
                                </x-button>
                            </div>

                            <div class="p-6 space-y-4">

                                {{-- Empty state --}}
                                <template x-if="services.length === 0">
                                    <div class="text-center py-10 text-gray-500">
                                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="mb-4">Belum ada layanan yang ditambahkan</p>
                                        <button @click="openAddDialog()"
                                            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Tambah Layanan Pertama
                                        </button>
                                    </div>
                                </template>

                                {{-- Service Category List --}}
                                <template x-for="service in services" :key="service.id">
                                    <div
                                        class="border rounded-xl p-4 flex items-stretch hover:border-blue-200 transition-colors">

                                        {{-- Service Info --}}
                                        <div class="flex items-center gap-3">
                                            <div class="font-semibold mb-1" x-text="service.name"></div>
                                            <template x-if="service.is_active">
                                                <x-label-status :value="'active'" />
                                            </template>
                                            <template x-if="!service.is_active">
                                                <x-label-status :value="'inactive'" />
                                            </template>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex ml-auto items-center">
                                            <x-action-buttons :toggle="true" />
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
                                    :class="config.ttsEnabled ? 'text-green-600' : 'text-gray-400'"
                                    x-text="config.ttsEnabled ? 'Aktif' : 'Non-aktif'">
                                </p>
                            </div>
                            <hr class="border-gray-100">
                            <div>
                                <p class="text-sm text-gray-500">Maksimal Booking Online per Hari</p>
                                <p class="text-3xl font-bold text-blue-600" x-text="activeServicesCount"></p>
                            </div>
                            <hr class="border-gray-100">
                            <div>
                                <p class="text-sm text-gray-500">Layanan Aktif</p>
                                <p class="text-3xl font-bold text-blue-600" x-text="activeServicesCount"></p>
                                <p class="text-sm text-gray-400">dari <span x-text="services.length"></span> layanan</p>
                            </div>
                            <hr class="border-gray-100">
                            <div>
                                <p class="text-sm text-gray-500">Kategori Layanan</p>
                                <p class="text-3xl font-bold text-blue-600" x-text="activeServicesCount"></p>
                                <p class="text-sm text-gray-400">dari <span x-text="services.length"></span> layanan</p>
                            </div>
                            <hr class="border-gray-100">

                            {{-- <div>
                                <p class="text-sm text-gray-500">Pengulangan Panggilan</p>
                                <p class="text-lg font-semibold"><span x-text="config.callRepeatCount"></span>x</p>
                            </div> --}}

                            <x-button type="submit" size="lg" class="w-full">
                                Simpan Konfigurasi
                            </x-button>
                        </div>

                    </div>
                </div>
            </div>
        </main>

    </div>
@endsection

@push('scripts')
    <script>
        function adminDashboard() {
            return {
                dialogOpen: false,
                editingService: null,
                config: {
                    ttsEnabled: true,
                    autoCallEnabled: false,
                    callRepeatCount: 3,
                },
                services: [{
                        id: 1,
                        name: 'Pendaftaran KTP',
                        code: 'A',
                        color: '#3B82F6',
                        is_active: true,
                        estimated_time: 10
                    },
                    {
                        id: 2,
                        name: 'Layanan SIM',
                        code: 'B',
                        color: '#10B981',
                        is_active: true,
                        estimated_time: 15
                    },
                    {
                        id: 3,
                        name: 'Layanan STNK',
                        code: 'C',
                        color: '#F59E0B',
                        is_active: false,
                        estimated_time: 20
                    },
                ],
                form: {
                    name: '',
                    code: '',
                    estimatedTime: 15,
                    color: '#3B82F6',
                },

                get activeServicesCount() {
                    return this.services.filter(s => s.is_active).length;
                },

                toggleService(service) {
                    service.is_active = !service.is_active;
                },

                deleteService(id) {
                    const service = this.services.find(s => s.id === id);
                    if (confirm(`Hapus layanan ${service ? service.name : ''}?`)) {
                        this.services = this.services.filter(s => s.id !== id);
                    }
                },

                saveService() {
                    if (this.editingService) {
                        const idx = this.services.findIndex(s => s.id === this.editingService.id);
                        if (idx !== -1) {
                            this.services[idx] = {
                                ...this.services[idx],
                                name: this.form.name,
                                code: this.form.code,
                                estimated_time: this.form.estimatedTime,
                                color: this.form.color,
                            };
                        }
                    } else {
                        this.services.push({
                            id: Date.now(),
                            name: this.form.name,
                            code: this.form.code,
                            estimated_time: this.form.estimatedTime,
                            color: this.form.color,
                            is_active: true,
                        });
                    }
                    this.dialogOpen = false;
                },

                openAddDialog() {
                    this.editingService = null;
                    this.form = {
                        name: '',
                        code: '',
                        estimatedTime: 15,
                        color: '#3B82F6'
                    };
                    this.dialogOpen = true;
                },

                openEditDialog(service) {
                    this.editingService = service;
                    this.form = {
                        name: service.name,
                        code: service.code,
                        estimatedTime: service.estimated_time,
                        color: service.color,
                    };
                    this.dialogOpen = true;
                },
            };
        }
    </script>
@endpush

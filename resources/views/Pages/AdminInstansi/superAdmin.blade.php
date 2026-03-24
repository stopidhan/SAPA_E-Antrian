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

                        {{-- Services Management (Integrated with Counters) --}}
                        <div class="bg-white rounded-2xl border shadow-sm">
                            <div class="p-6 border-b flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold">Manajemen Layanan & Konter</h2>
                                    <p class="text-sm text-gray-500 mt-0.5">Kelola layanan dan konter yang tersedia di
                                        sistem antrean</p>
                                </div>
                                <x-button type="button" variant="success" @click="openServiceDialog()"
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
                                        <button @click="openServiceDialog()"
                                            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Tambah Layanan Pertama
                                        </button>
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
                                                    class="w-12 h-12 rounded-lg flex items-center justify-center bg-blue-100 text-blue-700 font-bold text-sm">
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
                                                    <button @click="toggleService(service.id)"
                                                        class="p-2 text-gray-400 hover:text-blue-600 transition-colors"
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
                                                    <button @click="deleteService(service.id)"
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
                                            <div class="text-sm font-medium text-gray-600 mb-3">Konter Melayani Layanan
                                                Ini:</div>

                                            <template x-if="service.counters && service.counters.length > 0">
                                                <div class="flex flex-wrap gap-2">
                                                    <template x-for="counter in service.counters" :key="counter.id">
                                                        <div
                                                            class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-1">
                                                            <span class="text-sm font-medium text-gray-700"
                                                                x-text="`Konter ${counter.counter_number}`"></span>
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
                                                <p class="text-sm text-gray-400 italic">Belum ada konter yang melayani
                                                    layanan ini</p>
                                            </template>
                                        </div>

                                    </div>
                                </template>

                            </div>
                        </div>

                        {{-- Services Counter Management --}}
                        <div class="bg-white rounded-2xl border shadow-sm">
                            <div class="p-6 border-b flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold">Manajemen Konter Layanan</h2>
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
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
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
                services: [],
                editingService: null,
                serviceForm: {},
                countersList: [],
                showServiceDialog: false,

                init() {
                    this.fetchServices();
                },

                async fetchServices() {
                    try {
                        const response = await fetch('/services', {
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
                    }
                },

                openServiceDialog(service = null) {
                    if (service) {
                        this.editingService = JSON.parse(JSON.stringify(service));
                        this.serviceForm = {
                            service_name: service.service_name,
                            queue_prefix: service.queue_prefix,
                            description: service.description,
                            is_active: service.is_active,
                        };
                        this.countersList = JSON.parse(JSON.stringify(service.counters || []));
                    } else {
                        this.editingService = null;
                        this.serviceForm = {
                            service_name: '',
                            queue_prefix: '',
                            description: '',
                            is_active: true,
                        };
                        this.countersList = [];
                    }
                    this.showServiceDialog = true;
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
                        `/services/${this.editingService.id}` :
                        '/services';
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
                            this.showServiceDialog = false;
                        } else {
                            alert(result.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        console.error('Error saving service:', error);
                    }
                },

                async deleteService(id) {
                    if (!confirm('Hapus layanan dan semua counter-nya?')) return;

                    try {
                        const response = await fetch(`/services/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.fetchServices();
                        }
                    } catch (error) {
                        console.error('Error deleting service:', error);
                    }
                },

                async toggleService(id) {
                    try {
                        const response = await fetch(`/services/${id}/toggle`, {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.fetchServices();
                        }
                    } catch (error) {
                        console.error('Error toggling service:', error);
                    }
                },

                editService(service) {
                    this.openServiceDialog(service);
                },
            };
        }
    </script>
@endpush

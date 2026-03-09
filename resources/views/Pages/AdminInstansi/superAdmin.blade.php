@extends('layouts.testes')

@section('title', 'Admin Dashboard - SAPA')

@php
    $withSidebar = true;
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col" x-data="adminDashboard()">

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
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" x-model="config.ttsEnabled">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all">
                                        </div>
                                    </label>
                                </div>

                                <hr class="border-gray-100">


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

                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors">
                                    Simpan Konfigurasi
                                </button>
                            </form>
                        </div>

                        {{-- Services Management --}}
                        <div class="bg-white rounded-2xl border shadow-sm">
                            <div class="p-6 border-b flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold">Manajemen Layanan</h2>
                                    <p class="text-sm text-gray-500 mt-0.5">Kelola layanan yang tersedia di sistem antrean
                                        (FR-01)</p>
                                </div>
                                <button @click="openAddDialog()"
                                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Layanan
                                </button>
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
                                    <div class="border rounded-xl p-4 space-y-3 hover:border-blue-200 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-xl"
                                                    :style="`background-color: ${service.color}`">
                                                    <span x-text="service.code"></span>
                                                </div>
                                                <div>
                                                    <div class="font-semibold" x-text="service.name"></div>
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                                        :class="service.is_active ? 'bg-green-100 text-green-700' :
                                                            'bg-gray-100 text-gray-500'">
                                                        <template x-if="service.is_active">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </template>
                                                        <template x-if="!service.is_active">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </template>
                                                        <span x-text="service.is_active ? 'Aktif' : 'Non-aktif'"></span>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                {{-- Toggle Active --}}
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only peer"
                                                        :checked="service.is_active" @change="toggleService(service)">
                                                    <div
                                                        class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all">
                                                    </div>
                                                </label>

                                                <button @click="openEditDialog(service)"
                                                    class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                                    <svg class="w-4 h-4 text-gray-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <button @click="deleteService(service.id)"
                                                    class="p-2 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                                    <svg class="w-4 h-4 text-red-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Estimated Time --}}
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-600">Estimasi Waktu:</span>
                                            <div class="inline items-center gap-1">
                                                <input type="number" min="5" max="120"
                                                    x-model.number="service.estimated_time" :disabled="!service.is_active"
                                                    class="w-20 px-2 py-1 border border-gray-200 rounded text-sm focus:ring-1 focus:ring-blue-500"
                                                    :class="!service.is_active ? 'opacity-50 cursor-not-allowed' : ''">
                                                <span class="text-sm text-gray-500">menit</span>
                                            </div>
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
                                <p class="text-sm text-gray-500">Layanan Aktif</p>
                                <p class="text-3xl font-bold text-blue-600" x-text="activeServicesCount"></p>
                                <p class="text-sm text-gray-400">dari <span x-text="services.length"></span> layanan</p>
                            </div>
                            <hr class="border-gray-100">
                            <div>
                                <p class="text-sm text-gray-500">TTS Otomatis</p>
                                <p class="text-lg font-semibold"
                                    :class="config.ttsEnabled ? 'text-green-600' : 'text-gray-400'"
                                    x-text="config.ttsEnabled ? 'Aktif' : 'Non-aktif'">
                                </p>
                            </div>
                            {{-- <hr class="border-gray-100">
                            <div>
                                <p class="text-sm text-gray-500">Pengulangan Panggilan</p>
                                <p class="text-lg font-semibold"><span x-text="config.callRepeatCount"></span>x</p>
                            </div> --}}
                        </div>

                    </div>
                </div>
            </div>
        </main>

        {{-- ===== Add/Edit Service Modal ===== --}}
        <div x-show="dialogOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            @click.self="dialogOpen = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4" @click.stop>
                <div class="p-6 border-b">
                    <h3 class="text-lg font-bold" x-text="editingService ? 'Edit Layanan' : 'Tambah Layanan Baru'"></h3>
                    <p class="text-sm text-gray-500 mt-1"
                        x-text="editingService ? 'Perbarui detail layanan' : 'Isi detail layanan baru'"></p>
                </div>

                <form @submit.prevent="saveService()" class="p-6 space-y-4">

                    {{-- Name --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Nama Layanan</label>
                        <input type="text" x-model="form.name" placeholder="Contoh: Pendaftaran KTP"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    {{-- Code --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Kode Layanan (1 huruf)</label>
                        <input type="text" x-model="form.code"
                            @input="form.code = $event.target.value.toUpperCase().slice(0,1)" placeholder="Contoh: A"
                            maxlength="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 uppercase"
                            required>
                    </div>

                    {{-- Estimated Time --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Estimasi Waktu (menit)</label>
                        <input type="number" x-model.number="form.estimatedTime" min="5" max="120"
                            class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Color --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Warna Layanan</label>
                        <select x-model="form.color"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="#3B82F6">Biru</option>
                            <option value="#10B981">Hijau</option>
                            <option value="#F59E0B">Kuning</option>
                            <option value="#8B5CF6">Ungu</option>
                            <option value="#EF4444">Merah</option>
                            <option value="#06B6D4">Cyan</option>
                            <option value="#EC4899">Pink</option>
                            <option value="#F97316">Orange</option>
                        </select>
                        {{-- Color preview --}}
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-6 h-6 rounded" :style="`background-color: ${form.color}`"></div>
                            <span class="text-sm text-gray-500">Preview warna</span>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="dialogOpen = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 font-medium text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors">
                            <span x-text="editingService ? 'Simpan Perubahan' : 'Tambah Layanan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

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

@extends('layouts.testes')

@section('title', 'Kelola Data Instansi - SAPA')

@php
    $withSidebar = true;
@endphp

@section('content')
    <div class="bg-gray-50" x-data="instansiPage()" x-init="init()">

        <main class="container mx-auto px-4 py-8">
            <form id="instansi-form" @submit.prevent="saveAll" @input="markChanged" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PATCH">

                <div class="space-y-6">

                    {{-- ── Profil Instansi ── --}}
                    <div class="bg-white rounded-2xl border shadow-sm">
                        <div class="p-6 border-b">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <h2 class="text-lg font-bold">Profil Instansi</h2>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Informasi dasar tentang instansi/organisasi Anda</p>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-8">

                                {{-- Media uploads (kiri) --}}
                                <div class="flex flex-col gap-6 md:w-48 flex-shrink-0">
                                    {{-- Logo --}}
                                    <div class="flex flex-col items-center gap-4" x-data="{ preview: '{{ $instance->logo ? asset('storage/' . $instance->logo) : '' }}' }">
                                        <div
                                            class="w-36 h-36 bg-gray-100 rounded-xl overflow-hidden border-2 border-gray-200 flex items-center justify-center">
                                            <img x-show="preview" :src="preview" alt="Logo Instansi"
                                                class="w-full h-full object-cover" onerror="this.style.display='none'">
                                            <div x-show="!preview" class="text-center text-gray-400">
                                                <svg class="w-12 h-12 mx-auto mb-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="text-xs">Belum ada logo</p>
                                            </div>
                                        </div>

                                        <input type="file" name="logo" id="logo-input" class="hidden" accept="image/*"
                                            @change="
                                                if ($el.files[0]) {
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { preview = e.target.result; };
                                                    reader.readAsDataURL($el.files[0]);
                                                    $dispatch('input');
                                                }
                                            ">

                                        <button type="button"
                                            class="border-2 border-dashed border-gray-300 hover:border-blue-400 text-gray-500 hover:text-blue-600 bg-transparent font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 py-1.5 px-3 text-xs w-full"
                                            @click="document.getElementById('logo-input').click()">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <span>Upload Logo</span>
                                        </button>
                                        <p class="text-xs text-center text-gray-400 leading-tight mt-1">
                                            Maks 2MB<br>Format: JPG, PNG, SVG<br>Rekomendasi: 512x512px
                                        </p>
                                    </div>

                                    <hr class="border-gray-100">

                                    {{-- Favicon --}}
                                    <div class="flex flex-col items-center gap-4" x-data="{ previewFav: '{{ $instance->favicon ? asset('storage/' . $instance->favicon) : '' }}' }">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200 flex items-center justify-center">
                                            <img x-show="previewFav" :src="previewFav" alt="Favicon"
                                                class="w-full h-full object-cover" onerror="this.style.display='none'">
                                            <div x-show="!previewFav" class="text-center text-gray-400">
                                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                                </svg>
                                            </div>
                                        </div>

                                        <input type="file" name="favicon" id="favicon-input" class="hidden"
                                            accept="image/png, image/x-icon"
                                            @change="
                                                if ($el.files[0]) {
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { previewFav = e.target.result; };
                                                    reader.readAsDataURL($el.files[0]);
                                                    $dispatch('input');
                                                }
                                            ">

                                        <button type="button"
                                            class="border-2 border-dashed border-gray-300 hover:border-blue-400 text-gray-500 hover:text-blue-600 bg-transparent font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 py-1.5 px-3 text-xs w-full"
                                            @click="document.getElementById('favicon-input').click()">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <span>Upload Favicon</span>
                                        </button>
                                        <p class="text-xs text-center text-gray-400 leading-tight mt-1">
                                            Maks 512KB<br>Format: PNG, ICO<br>Rekomendasi: 32x32px
                                        </p>
                                    </div>
                                </div>

                                {{-- Divider vertikal --}}
                                <div class="hidden md:block w-px bg-gray-100 self-stretch"></div>

                                {{-- Form fields (kanan) --}}
                                <div class="flex-1 space-y-8">

                                    {{-- Identitas --}}
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-bold text-gray-900 border-b pb-2">Identitas & Branding</h3>
                                        <div class="grid grid-cols-4 sm:grid-cols-4 gap-4">
                                            <div class="col-span-4 sm:col-span-3">
                                                <x-input-text name="instance_name" label="Nama Instansi"
                                                    placeholder="Nama lengkap instansi"
                                                    value="{{ $instance->instance_name ?? '' }}" required="true" />
                                            </div>

                                            <div class="col-span-4 sm:col-span-1">
                                                <x-input-text name="instance_code" label="Kode Instansi"
                                                    placeholder="Kode instansi"
                                                    value="{{ $instance->instance_code ?? '' }}" readonly="true" />
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div x-data="{ color: '{{ $instance->brand_color ?? '#3B82F6' }}' }">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Warna Utama
                                                    (Primary)</label>
                                                <div class="flex items-center gap-3">
                                                    <input type="color" name="brand_color" x-model="color"
                                                        @input="markChanged"
                                                        class="h-10 w-16 rounded cursor-pointer border-0 p-0 bg-transparent shadow-sm focus:ring-2 focus:ring-blue-500">
                                                    <span
                                                        class="text-sm text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded border"
                                                        x-text="color.toUpperCase()"></span>
                                                </div>
                                            </div>

                                            <div x-data="{ color: '{{ $instance->secondary_color ?? '#10B981' }}' }">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Warna Sekunder
                                                    (Secondary)</label>
                                                <div class="flex items-center gap-3">
                                                    <input type="color" name="secondary_color" x-model="color"
                                                        @input="markChanged"
                                                        class="h-10 w-16 rounded cursor-pointer border-0 p-0 bg-transparent shadow-sm focus:ring-2 focus:ring-blue-500">
                                                    <span
                                                        class="text-sm text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded border"
                                                        x-text="color.toUpperCase()"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Kontak & Lokasi --}}
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-bold text-gray-900 border-b pb-2">Kontak & Lokasi</h3>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <x-input-text name="phone" label="Telepon" placeholder="(022) 1234567"
                                                value="{{ $instance->phone ?? '' }}" />

                                            <x-input-text name="whatsapp_number" label="Nomor WhatsApp"
                                                placeholder="081234567890"
                                                value="{{ $instance->whatsapp_number ?? '' }}" />

                                            <x-input-text name="email" label="Email" placeholder="info@instansi.go.id"
                                                value="{{ $instance->email ?? '' }}" type="email" />
                                        </div>

                                        {{-- <x-input-text name="website" label="Website" placeholder="https://instansi.go.id"
                                            value="{{ $instance->website ?? '' }}" type="url" /> --}}

                                        <x-input-textarea name="address" label="Alamat Lengkap"
                                            placeholder="Jalan, nomor, RT/RW" value="{{ $instance->address ?? '' }}"
                                            rows="3" />

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <x-input-text name="latitude" label="Latitude" placeholder="-6.200000"
                                                value="{{ $instance->latitude ?? '' }}" />

                                            <x-input-text name="longitude" label="Longitude" placeholder="106.816666"
                                                value="{{ $instance->longitude ?? '' }}" />
                                        </div>
                                    </div>

                                    {{-- Sosial Media --}}
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-bold text-gray-900 border-b pb-2">Sosial Media</h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                                                <div class="flex items-center">
                                                    <span
                                                        class="inline-flex items-center px-3 h-10 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                        @
                                                    </span>
                                                    <input type="text" name="instagram"
                                                        value="{{ $instance->instagram ?? '' }}"
                                                        class="flex-1 min-w-0 block w-full px-3 h-10 rounded-none rounded-r-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                        placeholder="username_ig">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                                                <div class="flex items-center">
                                                    <span
                                                        class="inline-flex items-center px-3 h-10 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                        fb.com/
                                                    </span>
                                                    <input type="text" name="facebook"
                                                        value="{{ $instance->facebook ?? '' }}"
                                                        class="flex-1 min-w-0 block w-full px-3 h-10 rounded-none rounded-r-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                        placeholder="halaman_fb">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 sticky bottom-4">
                        <div class="bg-white p-3 rounded-xl border shadow-lg w-full flex justify-end gap-3">
                            <button type="submit" :disabled="!hasChanges || isLoading"
                                class="font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isLoading">Simpan Semua Perubahan</span>
                                <span x-show="isLoading">Menyimpan...</span>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </main>

    </div>
@endsection

@push('scripts')
    <script>
        function instansiPage() {
            return {
                hasChanges: false,
                isLoading: false,

                init() {
                    window.addEventListener('beforeunload', (e) => {
                        if (this.hasChanges) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                },

                markChanged() {
                    this.hasChanges = true;
                },

                async saveAll() {
                    if (!this.hasChanges) {
                        showToast('Tidak ada perubahan untuk disimpan.', 'info');
                        return;
                    }

                    this.isLoading = true;
                    const formElement = document.getElementById('instansi-form');
                    const formData = new FormData(formElement);

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        if (!csrfToken) throw new Error('CSRF token tidak ditemukan. Silakan refresh halaman.');

                        const response = await fetch('{{ route('profile.instance.update') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const contentType = response.headers.get('content-type');
                        const isJson = contentType && contentType.includes('application/json');
                        const data = isJson ? await response.json() : null;

                        if (!response.ok) {
                            if (response.status === 422 && data && data.errors) {
                                let errorMsg = '';
                                for (const messages of Object.values(data.errors)) {
                                    errorMsg += `${messages.join(', ')} `;
                                }
                                throw new Error(errorMsg || 'Validasi gagal.');
                            }
                            throw new Error(data?.message || `Terjadi kesalahan server (${response.status})`);
                        }

                        if (data && data.success) {
                            showToast(data.message, 'success');
                            this.hasChanges = false;
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(data?.message || 'Gagal menyimpan perubahan.');
                        }
                    } catch (error) {
                        console.error('Error details:', error);
                        showToast(error.message, 'error');
                    } finally {
                        this.isLoading = false;
                    }
                },

                resetForm() {
                    location.reload();
                }
            };
        }
    </script>
@endpush

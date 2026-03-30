@extends('layouts.testes')

@section('title', 'Kelola Data Instansi - SAPA')

@php
    $withSidebar = true;
@endphp

@section('content')
    <div class="bg-gray-50" x-data="instansiPage()" x-init="init()">

        <main class="container mx-auto px-4 py-8">
            <form id="instansi-form" @submit.prevent="saveAll" @input="markChanged">
                <input type="hidden" name="_method" value="PATCH">

                <div class="space-y-6">

                    {{-- ── Profil Instansi ── --}}
                    <div class="bg-white rounded-2xl border shadow-sm">
                        <div class="p-6 border-b flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <div>
                                <h2 class="text-lg font-bold">Profil Instansi</h2>
                                <p class="text-sm text-gray-500">Informasi dasar tentang instansi/organisasi Anda</p>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-8">

                                {{-- Logo upload (kiri) --}}
                                <div class="flex flex-col items-center gap-4 md:w-48 flex-shrink-0" x-data="{ preview: '{{ $instance->logo ? asset('storage/' . $instance->logo) : '' }}' }">
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
                                        class="border-2 border-dashed border-gray-300 hover:border-blue-400 text-gray-500 hover:text-blue-600 bg-transparent font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 py-1.5 px-3 text-xs"
                                        @click="document.getElementById('logo-input').click()">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span>Upload Logo</span>
                                    </button>
                                    <p class="text-xs text-center text-gray-400 leading-tight">
                                        PNG/JPG/WEBP<br>Maks 2MB · 400×400px
                                    </p>
                                </div>

                                {{-- Divider vertikal --}}
                                <div class="hidden md:block w-px bg-gray-100 self-stretch"></div>

                                {{-- Form fields (kanan) --}}
                                <div class="flex-1 space-y-5">

                                    {{-- Nama & Kode Instansi --}}
                                    <div class="grid grid-cols-4 sm:grid-cols-4 gap-4">
                                        <div class="col-span-4 sm:col-span-3">
                                            <x-input-text name="instance_name" label="Nama Instansi"
                                                placeholder="Nama lengkap instansi"
                                                value="{{ $instance->instance_name ?? '' }}" required="true" />
                                        </div>

                                        <div class="col-span-4 sm:col-span-1">
                                            <x-input-text name="instance_code" label="Kode Instansi"
                                                placeholder="Kode instansi" value="{{ $instance->instance_code ?? '' }}"
                                                readonly="true" />
                                        </div>
                                    </div>

                                    {{-- Telepon & Email --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <x-input-text name="phone" label="Telepon" placeholder="(022) 1234567"
                                            value="{{ $instance->phone ?? '' }}" />

                                        <x-input-text name="email" label="Email" placeholder="info@instansi.go.id"
                                            value="{{ $instance->email ?? '' }}" type="email" />
                                    </div>

                                    {{-- Website --}}
                                    <x-input-text name="website" label="Website" placeholder="https://instansi.go.id"
                                        value="{{ $instance->website ?? '' }}" type="url" />

                                    <hr class="border-gray-100">

                                    {{-- Alamat Lengkap --}}
                                    <x-input-textarea name="address" label="Alamat Lengkap"
                                        placeholder="Jalan, nomor, RT/RW" value="{{ $instance->address ?? '' }}"
                                        rows="3" />

                                    <div class="flex justify-end gap-3">
                                        <button type="reset" @click="resetForm"
                                            class="font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 py-2 px-4 text-sm">
                                            Reset
                                        </button>
                                        <button type="submit" :disabled="!hasChanges || isLoading"
                                            class="font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span x-show="!isLoading">Simpan Perubahan</span>
                                            <span x-show="isLoading">Menyimpan...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
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
                        alert('Tidak ada perubahan untuk disimpan.');
                        return;
                    }

                    this.isLoading = true;
                    const formElement = document.getElementById('instansi-form');
                    const formData = new FormData(formElement);

                    console.log('Form data entries:');
                    for (const [key, value] of formData.entries()) {
                        console.log(`${key}:`, value);
                    }

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        if (!csrfToken) {
                            throw new Error('CSRF token tidak ditemukan. Silakan refresh halaman dan coba lagi.');
                        }

                        const response = await fetch('{{ route('profile.instance.update') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        let data;
                        const contentType = response.headers.get('content-type');

                        if (!response.ok) {
                            if (contentType?.includes('application/json')) {
                                data = await response.json();
                            } else {
                                const html = await response.text();
                                console.error('Server response (HTML):', html);
                                throw new Error(`Server error: ${response.status} ${response.statusText}`);
                            }
                        } else {
                            data = await response.json();
                        }

                        if (data.success) {
                            alert(data.message);
                            this.hasChanges = false;
                            setTimeout(() => location.reload(), 500);
                        } else {
                            if (data.errors) {
                                let errorMsg = (data.message || 'Validasi gagal') + '\n\n';
                                for (const [field, messages] of Object.entries(data.errors)) {
                                    errorMsg += `${field}: ${messages.join(', ')}\n`;
                                }
                                alert(errorMsg);
                                console.log('Validation errors:', data.errors);
                            } else {
                                alert(data.message || 'Gagal menyimpan perubahan.');
                            }
                        }
                    } catch (error) {
                        console.error('Error details:', error);
                        alert('Terjadi kesalahan: ' + error.message);
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

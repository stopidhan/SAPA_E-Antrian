@extends('layouts.testes')

@section('title', 'Kelola Data Instansi - SAPA')

@php
    $withSidebar = true;
@endphp

@section('content')
    <div class="bg-gray-50" x-data="instansiPage()" x-init="init()">

        <main class="container mx-auto px-4 py-8">
            <form id="instansi-form">

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
                                <div class="flex flex-col items-center gap-4 md:w-48 flex-shrink-0" x-data="{ preview: '' }">
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

                                    <x-button variant="dotted" size="sm" class="px-6"
                                        icon='<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>'>
                                        Upload Logo
                                    </x-button>
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

                                        <div class="col-span-4 sm:col-span-3 space-y-1.5">
                                            <x-input-text name="nama" label="Nama Instansi"
                                                placeholder="Nama lengkap instansi" value="" required="true" />
                                        </div>

                                        <div class="col-span-4 sm:col-span-1 space-y-1.5">
                                            <x-input-text name="kode" label="Kode Instansi" placeholder="Kode instansi"
                                                value="" readonly="true" />
                                        </div>
                                    </div>

                                    {{-- Deskripsi --}}
                                    <x-input-textarea name="deskripsi" label="Deskripsi"
                                        placeholder="Deskripsi singkat tentang instansi" rows="3" />

                                    <hr class="border-gray-100">

                                    {{-- Telepon & Email --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        {{-- Telepon --}}
                                        <x-input-text name="telepon" label="Telepon" placeholder="(022) 1234567" />

                                        {{-- Email --}}
                                        <x-input-text name="email" label="Email" placeholder="info@instansi.go.id" />
                                    </div>

                                    {{-- Website --}}
                                    <x-input-text name="website" label="Website" placeholder="https://instansi.go.id" />

                                    <hr class="border-gray-100">

                                    {{-- Alamat Lengkap --}}
                                    <x-input-textarea name="alamat" label="Alamat Lengkap"
                                        placeholder="Jalan, nomor, RT/RW" rows="2" />

                                    {{-- Kota, Provinsi, Kode Pos --}}
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <x-input-text name="kota" label="Kota / Kabupaten" placeholder="Bandung" />
                                        <x-input-text name="provinsi" label="Provinsi" placeholder="Jawa Barat"
                                            value="Jawa Barat" />
                                        <x-input-number name="kode_pos" label="Kode Pos" placeholder="40117" value="40117"
                                            maxlength="5" />
                                    </div>

                                    <div class="flex justify-end gap-3">
                                        <x-button type="reset" variant="white">
                                            Reset
                                        </x-button>
                                        <x-button type="submit" variant="primary">
                                            Simpan Perubahan
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Jam Operasional── --}}
                    {{-- <div class="bg-white rounded-2xl border shadow-sm">
                        <div class="p-6 border-b flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h2 class="text-lg font-bold">Jam Operasional</h2>
                                <p class="text-sm text-gray-500">Atur jam buka dan tutup untuk setiap hari (FR-26)</p>
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            @php
                                $hariList = [
                                    'senin' => ['label' => 'Senin', 'key' => 0],
                                    'selasa' => ['label' => 'Selasa', 'key' => 1],
                                    'rabu' => ['label' => 'Rabu', 'key' => 2],
                                    'kamis' => ['label' => 'Kamis', 'key' => 3],
                                    'jumat' => ['label' => "Jum'at", 'key' => 4],
                                    'sabtu' => ['label' => 'Sabtu', 'key' => 5],
                                    'minggu' => ['label' => 'Minggu', 'key' => 6],
                                ];

                                $jam = [
                                    'senin' => ['buka' => '08:00', 'tutup' => '16:00', 'libur' => false],
                                    'selasa' => ['buka' => '08:00', 'tutup' => '16:00', 'libur' => false],
                                    'rabu' => ['buka' => '08:00', 'tutup' => '16:00', 'libur' => false],
                                    'kamis' => ['buka' => '08:00', 'tutup' => '16:00', 'libur' => false],
                                    'jumat' => ['buka' => '08:00', 'tutup' => '16:00', 'libur' => false],
                                    'sabtu' => ['buka' => '08:00', 'tutup' => '12:00', 'libur' => false],
                                    'minggu' => ['buka' => '08:00', 'tutup' => '16:00', 'libur' => true],
                                ];
                            @endphp

                            @foreach ($hariList as $hari => $meta)
                                @php $jadwal = $jam[$hari]; @endphp
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100"
                                    x-data="{ libur: {{ $jadwal['libur'] ? 'true' : 'false' }} }">

                                    <div class="w-20 text-sm font-semibold text-gray-700 flex-shrink-0">
                                        {{ $meta['label'] }}
                                    </div>

                                    <div class="flex-1 grid grid-cols-2 gap-3" x-show="!libur">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-500 w-10">Buka</span>
                                            <input type="time" name="jam[{{ $hari }}][buka]"
                                                value="{{ $jadwal['buka'] }}" @change="$dispatch('form-changed')"
                                                class="flex-1 px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-400">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-500 w-10">Tutup</span>
                                            <input type="time" name="jam[{{ $hari }}][tutup]"
                                                value="{{ $jadwal['tutup'] }}" @change="$dispatch('form-changed')"
                                                class="flex-1 px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-400">
                                        </div>
                                    </div>

                                    <div x-show="libur" class="flex-1">
                                        <span
                                            class="px-3 py-1 bg-gray-200 text-gray-600 text-xs font-semibold rounded-full">
                                            Libur / Tutup
                                        </span>
                                    </div>

                                    <input type="hidden" name="jam[{{ $hari }}][libur]"
                                        :value="libur ? '1' : '0'">

                                    <button type="button" @click="libur = !libur; hasChanges = true"
                                        :class="libur
                                            ?
                                            'bg-blue-600 hover:bg-blue-700 text-white' :
                                            'border border-gray-300 text-gray-600 hover:bg-gray-100'"
                                        class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                        <span x-text="libur ? 'Aktifkan' : 'Libur'"></span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div> --}}

                </div>
            </form>
        </main>

        {{-- Listen to form changes from child elements --}}
        <div x-on:form-changed.window="hasChanges = true"></div>

    </div>
@endsection

@push('scripts')
    <script>
        function instansiPage() {
            return {
                hasChanges: false,
                form: {
                    nama: 'Dinas Pelayanan Publik Kota Bandung',
                    deskripsi: '',
                    telepon: '',
                    email: '',
                    website: '',
                },

                init() {
                    window.addEventListener('beforeunload', (e) => {
                        if (this.hasChanges) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                },

                saveAll() {
                    alert('Fitur simpan belum terhubung ke backend.');
                    this.hasChanges = false;
                },
            };
        }
    </script>
@endpush

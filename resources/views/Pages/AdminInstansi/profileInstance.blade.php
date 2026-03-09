@extends('layouts.testes')

@section('title', 'Kelola Data Instansi - SAPA')

@php
    $withSidebar = true;
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50" x-data="instansiPage()" x-init="init()">

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

                                    <label for="logo-upload"
                                        class="w-full flex items-center justify-center gap-1.5 px-3 py-2 border-2 border-dashed border-gray-300 hover:border-blue-400 text-gray-500 hover:text-blue-600 rounded-xl text-xs font-medium cursor-pointer transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        Upload Logo
                                        <input type="file" id="logo-upload" name="logo"
                                            accept="image/png,image/jpeg,image/webp" class="sr-only"
                                            @change="
                                                        hasChanges = true;
                                                        const file = $event.target.files[0];
                                                        if (file) {
                                                            const reader = new FileReader();
                                                            reader.onload = (ev) => { preview = ev.target.result; };
                                                            reader.readAsDataURL(file);
                                                        }
                                                    ">
                                    </label>
                                    <p class="text-xs text-center text-gray-400 leading-tight">
                                        PNG/JPG/WEBP<br>Maks 2MB · 400×400px
                                    </p>
                                </div>

                                {{-- Divider vertikal --}}
                                <div class="hidden md:block w-px bg-gray-100 self-stretch"></div>

                                {{-- Form fields (kanan) --}}
                                <div class="flex-1 space-y-5">

                                    {{-- Nama --}}
                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Nama Instansi <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nama" x-model="form.nama" @input="hasChanges = true"
                                            value="Dinas Pelayanan Publik Kota Bandung" placeholder="Nama lengkap instansi"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            required>
                                    </div>

                                    {{-- Deskripsi --}}
                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                        <textarea name="deskripsi" x-model="form.deskripsi" @input="hasChanges = true" rows="3"
                                            placeholder="Deskripsi singkat tentang instansi"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                    </div>

                                    <hr class="border-gray-100">

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        {{-- Telepon --}}
                                        <div class="space-y-1.5">
                                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                Telepon
                                            </label>
                                            <input type="tel" name="telepon" x-model="form.telepon"
                                                @input="hasChanges = true" value="" placeholder="(022) 1234567"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        {{-- Email --}}
                                        <div class="space-y-1.5">
                                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                Email
                                            </label>
                                            <input type="email" name="email" x-model="form.email"
                                                @input="hasChanges = true" value=""
                                                placeholder="info@instansi.go.id"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>

                                    {{-- Website --}}
                                    <div class="space-y-1.5">
                                        <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                            </svg>
                                            Website
                                        </label>
                                        <input type="url" name="website" x-model="form.website"
                                            @input="hasChanges = true" value=""
                                            placeholder="https://instansi.go.id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- ── Alamat ── --}}
                    <div class="bg-white rounded-2xl border shadow-sm">
                        <div class="p-6 border-b flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div>
                                <h2 class="text-lg font-bold">Alamat</h2>
                                <p class="text-sm text-gray-500">Lokasi kantor / tempat pelayanan</p>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                                <textarea name="alamat" @input="hasChanges = true" rows="2" placeholder="Jalan, nomor, RT/RW"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-gray-700">Kota / Kabupaten</label>
                                    <input type="text" name="kota" @input="hasChanges = true" value="Bandung"
                                        placeholder="Bandung"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                                    <input type="text" name="provinsi" @input="hasChanges = true" value="Jawa Barat"
                                        placeholder="Jawa Barat"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                                    <input type="text" name="kode_pos" @input="hasChanges = true" value="40117"
                                        placeholder="40117" maxlength="5"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Jam Operasional── --}}
                    <div class="bg-white rounded-2xl border shadow-sm">
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

                                    {{-- Nama hari --}}
                                    <div class="w-20 text-sm font-semibold text-gray-700 flex-shrink-0">
                                        {{ $meta['label'] }}
                                    </div>

                                    {{-- Input jam --}}
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

                                    {{-- Libur badge --}}
                                    <div x-show="libur" class="flex-1">
                                        <span
                                            class="px-3 py-1 bg-gray-200 text-gray-600 text-xs font-semibold rounded-full">
                                            Libur / Tutup
                                        </span>
                                    </div>

                                    {{-- Hidden flag --}}
                                    <input type="hidden" name="jam[{{ $hari }}][libur]"
                                        :value="libur ? '1' : '0'">

                                    {{-- Toggle libur button --}}
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
                    </div>

                    {{-- ── Pengaturan Booking Online ── --}}
                    <div class="bg-white rounded-2xl border shadow-sm">
                        <div class="p-6 border-b flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <h2 class="text-lg font-bold">Pengaturan Booking Online</h2>
                                <p class="text-sm text-gray-500">Konfigurasi sistem booking online (FR-06, FR-07)</p>
                            </div>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                {{-- Max Booking --}}
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Maksimal Booking per Hari
                                    </label>
                                    <input type="number" name="max_booking_per_day" @change="hasChanges = true"
                                        value="100" min="10" max="500"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500">Batasi jumlah booking online per hari</p>
                                </div>

                                {{-- Advance Days --}}
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Booking H-berapa?
                                    </label>
                                    <input type="number" name="booking_advance_days" @change="hasChanges = true"
                                        value="7" min="1" max="30"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500">Berapa hari ke depan bisa booking</p>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- WhatsApp Bot --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700">
                                    <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
                                    </svg>
                                    Nomor WhatsApp Bot
                                </label>
                                <div class="flex gap-2">
                                    <span
                                        class="flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-sm text-gray-500">
                                        +
                                    </span>
                                    <input type="text" name="whatsapp_bot_number" @input="hasChanges = true"
                                        value="" placeholder="628123456789"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <p class="text-xs text-gray-500">
                                    Nomor WhatsApp untuk integrasi bot booking (FR-06). Format: 628xxxxxxxxx (tanpa + di
                                    depan)
                                </p>
                            </div>

                            {{-- WhatsApp Bot Status --}}
                            <div
                                class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-green-600 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-green-800">WhatsApp Bot Integration
                                        </div>
                                        <div class="text-xs text-green-600">Koneksi aktif untuk booking online</div>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-green-600 text-white text-xs font-semibold rounded-full">
                                    ● Aktif
                                </span>
                            </div>
                        </div>
                    </div>


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

{{--
|--------------------------------------------------------------------------
| File: operator/partials/serving.blade.php
| Fase 3: MELAYANI — Pengunjung sedang dilayani di meja loket
| x-show="state === 'serving'"
|--------------------------------------------------------------------------
--}}
<div x-show="state === 'serving'"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-cloak>

    <div class="bg-white rounded-2xl border-2 border-emerald-500 shadow-sm shadow-emerald-100 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-emerald-100 bg-emerald-50/30">
            <h2 class="text-base font-bold text-gray-900">Antrean Saat Ini</h2>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-600 text-white text-xs font-semibold rounded-full">
                <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                Sedang Dilayani
            </span>
        </div>

        {{-- Body: Nomor + Info --}}
        <div class="px-6 pt-8 pb-4 text-center">
            <p class="text-emerald-600 text-7xl font-black tracking-tight leading-none mb-3"
               x-text="currentQueue?.nomor ?? '-'">-</p>
            <p class="text-lg font-bold text-gray-900 mb-2"
               x-text="currentQueue?.layanan ?? '-'">-</p>
            <div class="flex items-center justify-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full border"
                      :class="currentQueue?.tipe === 'online'
                          ? 'bg-emerald-50 text-emerald-600 border-emerald-200'
                          : 'bg-primary/10 text-primary border-blue-200'"
                      x-text="currentQueue?.tipe ?? 'onsite'"></span>
                <span class="text-sm font-medium text-gray-500 max-w-[200px] truncate" x-text="currentQueue?.nama ?? 'Anonim'"></span>
            </div>
        </div>

        {{-- Area Pelayanan --}}
        <div class="px-6 pb-6 space-y-4">

            {{-- Timer Banner --}}
            <div class="border rounded-xl px-5 py-3.5 flex items-center justify-between transition-colors duration-300 bg-emerald-50 border-emerald-200 text-emerald-700">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">Waktu Pelayanan:</span>
                </div>
                <span class="text-2xl font-black tabular-nums text-emerald-600" x-text="timerDisplay">00:00</span>
            </div>

            {{-- Area Kamera --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl overflow-hidden p-3 text-center">
                
                {{-- Mode Standby: Tombol Ambil Foto --}}
                <div x-show="!isCameraOpen && !photoBase64">
                    <button @click="startCamera()" class="w-full flex items-center justify-center gap-2 py-3 border-2 border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-white active:bg-gray-100 transition">
                        <span class="text-base">📷</span>
                        Ambil Foto Bukti Pelayanan
                    </button>
                </div>

                {{-- Mode Kamera Aktif --}}
                <div x-show="isCameraOpen && !photoBase64" class="relative">
                    <video x-ref="videoElement" class="w-full aspect-video object-cover rounded-lg bg-black" playsinline></video>
                    <div class="mt-3 flex gap-2">
                        <button @click="stopCameraStream(); isCameraOpen = false" class="flex-1 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 text-sm">
                            Batal
                        </button>
                        <button @click="takePhoto()" class="flex-1 py-2 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 text-sm flex items-center justify-center gap-1">
                            <span></span> Foto
                        </button>
                    </div>
                </div>

                {{-- Mode Pratinjau Foto Hasil --}}
                <div x-show="photoBase64" class="relative">
                    <img :src="photoBase64" class="w-full aspect-video object-cover rounded-lg border border-gray-300">
                    <button @click="retakePhoto()" class="mt-3 w-full py-2 bg-gray-800 text-white font-bold rounded-lg hover:bg-gray-900 text-sm flex items-center justify-center gap-1">
                        <span>🔄</span> Foto Ulang
                    </button>
                </div>

                {{-- Hidden Canvas for Capture --}}
                <canvas x-ref="canvasElement" style="display: none;"></canvas>
            </div>

            {{-- Dropdown Kategori Layanan --}}
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-1.5">Kategori Layanan <span class="text-red-500">*</span></label>
                <select x-model="serviceCategory" class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-sm text-gray-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition outline-none appearance-none cursor-pointer">
                    <option value="" disabled selected>Pilih kategori layanan</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->service_name }}">{{ $svc->service_name }}</option>
                    @endforeach
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            {{-- Textarea Catatan / Deskripsi --}}
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-1.5">Deskripsi / Catatan Layanan <span class="text-red-500">*</span></label>
                <textarea x-model="serviceDescription" rows="3"
                          placeholder="Catatan tambahan, keluhan..."
                          class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-sm text-gray-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition outline-none resize-none placeholder:text-gray-300"></textarea>
            </div>

            {{-- Tombol Aksi Akhir (Jika tombol Batal diaktifkan kembali, ubah class div menjadi: grid grid-cols-3 gap-3, dan button selesai menjadi col-span-2) --}}
            <div class="flex">
                {{-- Tombol Batalkan (Disembunyikan sementara) --}}
                {{--
                <button @click="batalkanAntrean()"
                        class="col-span-1 flex items-center justify-center gap-2 py-4 bg-red-100 hover:bg-red-200 active:bg-red-300 text-red-700 text-base font-bold rounded-xl shadow-sm transition border border-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    Batal
                </button>
                --}}

                {{-- Tombol Selesai --}}
                <button @click="stopServing()"
                        class="w-full flex items-center justify-center gap-2 py-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-base font-bold rounded-xl shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Selesai
                </button>
            </div>

        </div>
    </div>
</div>

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
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full border"
                  :class="currentQueue?.tipe === 'online'
                      ? 'bg-emerald-50 text-emerald-600 border-emerald-200'
                      : 'bg-blue-50 text-blue-600 border-blue-200'"
                  x-text="currentQueue?.tipe ?? 'onsite'"></span>
        </div>

        {{-- Area Pelayanan --}}
        <div class="px-6 pb-6 space-y-4">

            {{-- Timer Banner --}}
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold text-emerald-700">Waktu Pelayanan:</span>
                </div>
                <span class="text-2xl font-black text-emerald-600 tabular-nums" x-text="timerDisplay">00:00</span>
            </div>

            {{-- Tombol Ambil Foto --}}
            <button class="w-full flex items-center justify-center gap-2 py-3.5 border-2 border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 active:bg-gray-100 transition">
                <span class="text-base">📷</span>
                Ambil Foto
            </button>

            {{-- Dropdown Kategori Layanan --}}
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-1.5">Kategori Layanan <span class="text-red-500">*</span></label>
                <select class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-sm text-gray-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition outline-none appearance-none cursor-pointer">
                    <option value="" disabled selected>Pilih kategori layanan</option>
                    <option>Pembuatan KTP Baru</option>
                    <option>Perpanjangan KTP</option>
                    <option>Pergantian KTP</option>
                    <option>Cetak Ulang KTP</option>
                    <option>Lainnya</option>
                </select>
            </div>

            {{-- Textarea Catatan / Deskripsi --}}
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-1.5">Deskripsi / Catatan Layanan</label>
                <textarea rows="3"
                          placeholder="Catatan tambahan, keluhan..."
                          class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-sm text-gray-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition outline-none resize-none placeholder:text-gray-300"></textarea>
            </div>

            {{-- Tombol Selesai --}}
            <button @click="state = 'standby'; stopServing()"
                    class="w-full flex items-center justify-center gap-2 py-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-base font-bold rounded-xl shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Selesai
            </button>

        </div>
    </div>
</div>

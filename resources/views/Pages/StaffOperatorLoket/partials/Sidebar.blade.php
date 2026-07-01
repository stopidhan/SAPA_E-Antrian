{{--
|--------------------------------------------------------------------------
| File: operator/partials/sidebar.blade.php
| Kolom Kanan — Daftar Antrean Menunggu (Dynamic via Alpine.js)
|--------------------------------------------------------------------------
--}}

{{-- Card: Daftar Antrean Menunggu --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-900">Daftar Antrean Menunggu</h3>
        <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full"
              x-text="queue.length + ' antrean'">0 antrean</span>
    </div>
    <div class="p-4 space-y-3">

        {{-- Empty state --}}
        <div x-show="queue.length === 0" class="py-6 text-center">
            <p class="text-sm text-gray-400">Tidak ada antrean menunggu</p>
        </div>

        {{-- Dynamic queue items --}}
        <template x-for="(item, index) in queue" :key="item.id">
            <div class="flex items-center gap-3 rounded-xl px-4 py-3"
                 :class="index === 0 ? 'bg-primary/10 border border-blue-100' : 'bg-gray-50 border border-gray-100'">
                {{-- Nomor Urut --}}
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 shadow-sm"
                     :class="index === 0 ? 'bg-primary' : 'bg-gray-400'">
                    <span class="text-white text-sm font-black" x-text="index + 1"></span>
                </div>
                {{-- Info Antrean --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900" x-text="item.nomor + ' — ' + item.layanan"></p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-white text-[10px] font-semibold rounded border"
                              :class="item.tipe === 'onsite' ? 'text-primary border-blue-200' : 'text-emerald-600 border-emerald-200'"
                              x-text="item.tipe"></span>
                    </div>
                </div>
                {{-- Label Berikutnya (hanya item pertama) --}}
                <span x-show="index === 0" class="text-xs font-bold text-primary shrink-0">→ Berikutnya</span>
            </div>
        </template>

    </div>
</div>

{{-- Card: Riwayat Hari Ini --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mt-6">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-sm font-bold text-gray-900">Riwayat Hari Ini</h3>
        <span class="text-xs font-semibold text-gray-400 bg-white border border-gray-200 px-2 py-0.5 rounded-full"
              x-text="history.length + ' antrean'">0 antrean</span>
    </div>
    <div class="p-4 space-y-3 max-h-[300px] overflow-y-auto">

        {{-- Empty state --}}
        <div x-show="history.length === 0" class="py-6 text-center">
            <p class="text-sm text-gray-400">Belum ada riwayat pelayanan</p>
        </div>

        {{-- Dynamic history items --}}
        <template x-for="item in history" :key="item.id">
            <div class="flex items-center justify-between rounded-xl px-4 py-3 bg-gray-50 border border-gray-100">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900" x-text="item.nomor"></p>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="item.waktu"></p>
                </div>
                
                {{-- Status Badge --}}
                <span class="inline-flex items-center px-2 py-1 text-[10px] font-bold rounded-md border"
                      :class="{
                          'bg-emerald-50 text-emerald-600 border-emerald-200': item.status === 'completed',
                          'bg-amber-50 text-amber-600 border-amber-200': item.status === 'skipped',
                          'bg-red-50 text-red-600 border-red-200': item.status === 'cancelled'
                      }"
                      x-text="item.status === 'completed' ? 'Selesai' : (item.status === 'skipped' ? 'Dilewati' : 'Dibatalkan')">
                </span>
            </div>
        </template>

    </div>
</div>


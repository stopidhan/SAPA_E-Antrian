{{--
|--------------------------------------------------------------------------
| File: operator/partials/standby.blade.php
| Fase 1: STANDBY — Menunggu operator memanggil antrean berikutnya
| x-show="state === 'standby'"
|--------------------------------------------------------------------------
--}}
<div x-show="state === 'standby'"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100">

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">Antrean Saat Ini</h2>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">
                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                Standby
            </span>
        </div>

        {{-- Body: Empty State --}}
        <div class="px-6 py-16 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            </div>
            <p class="text-lg font-bold text-gray-400 mb-1">Tidak ada antrean saat ini</p>
            <p class="text-sm text-gray-300">Klik <strong>Panggil Berikutnya</strong> untuk memanggil antrean</p>
        </div>

        {{-- Tombol Panggil Berikutnya --}}
        <div class="px-6 pb-6">
            <button @click="panggilBerikutnya()"
                    :disabled="queue.length === 0"
                    class="w-full flex items-center justify-center gap-3 py-4 text-white text-base font-bold rounded-xl shadow-sm transition"
                    :class="queue.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800'">
                <span class="text-lg">📞</span>
                Panggil Nomor Berikutnya
            </button>
        </div>

    </div>
</div>

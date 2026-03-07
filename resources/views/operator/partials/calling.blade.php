{{--
|--------------------------------------------------------------------------
| File: operator/partials/calling.blade.php
| Fase 2: MEMANGGIL — Nomor antrean sedang dipanggil
| x-show="state === 'calling'"
|--------------------------------------------------------------------------
--}}
<div x-show="state === 'calling'"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-cloak>

    <div class="bg-white rounded-2xl border-2 border-blue-500 shadow-sm shadow-blue-100 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-blue-100 bg-blue-50/30">
            <h2 class="text-base font-bold text-gray-900">Antrean Saat Ini</h2>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-900 text-white text-xs font-semibold rounded-full">
                <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                Dipanggil
            </span>
        </div>

        {{-- Body: Nomor Raksasa --}}
        <div class="px-6 py-12 text-center">
            <p class="text-blue-600 text-8xl font-black tracking-tight leading-none mb-4"
               x-text="currentQueue?.nomor ?? '-'">-</p>
            <p class="text-xl font-bold text-gray-900 mb-2"
               x-text="currentQueue?.layanan ?? '-'">-</p>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full border"
                  :class="currentQueue?.tipe === 'online'
                      ? 'bg-emerald-50 text-emerald-600 border-emerald-200'
                      : 'bg-blue-50 text-blue-600 border-blue-200'"
                  x-text="currentQueue?.tipe ?? 'onsite'"></span>
        </div>

        {{-- Tombol Aksi (3 Kolom) --}}
        <div class="px-6 pb-6 grid grid-cols-3 gap-3">
            {{-- Panggil Ulang --}}
            <button @click="panggilUlang()"
                    class="flex items-center justify-center gap-2 py-3.5 border-2 border-blue-300 text-blue-600 text-sm font-bold rounded-xl hover:bg-blue-50 active:bg-blue-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/></svg>
                Panggil Ulang
            </button>

            {{-- Lewati --}}
            <button @click="lewatiAntrian()"
                    class="flex items-center justify-center gap-2 py-3.5 border-2 border-amber-300 text-amber-600 text-sm font-bold rounded-xl hover:bg-amber-50 active:bg-amber-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8.689c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062A1.125 1.125 0 013 16.811V8.69zM12.75 8.689c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062a1.125 1.125 0 01-1.683-.977V8.69z"/></svg>
                Lewati
            </button>

            {{-- Mulai Layani --}}
            <button @click="state = 'serving'; startServing()"
                    class="flex items-center justify-center gap-2 py-3.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-sm shadow-emerald-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                Mulai Layani
            </button>
        </div>

    </div>
</div>

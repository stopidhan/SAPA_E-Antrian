{{--
|--------------------------------------------------------------------------
| File: operator/partials/navbar.blade.php
| Navigasi Atas — Dashboard Operator Loket
|--------------------------------------------------------------------------
--}}
<nav class="bg-white shadow-sm sticky top-0 z-40 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">

        {{-- Kiri: Logo SAPA + Info Loket --}}
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-sm shadow-blue-200">
                    <span class="text-white text-sm font-black tracking-tight">SAPA</span>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-gray-900">Dashboard {{ $namaLoket ?? 'Operator Loket' }}</h1>
                    <p class="text-xs text-gray-400">Sistem Pelayanan Terpadu</p>
                </div>
            </div>
        </div>

        {{-- Kanan: Profil + Logout --}}
        <div class="flex items-center gap-3">
            <template x-if="counterId">
                <button type="button" @click="$dispatch('open-modal', 'confirm-close-session')"
                    class="flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Tutup Sesi
                </button>
            </template>

            @if (Auth::user() && in_array(Auth::user()->role, ['super_admin', 'admin_instansi']))
                <a href="{{ route('admininstance.dashboard', $instanceSlug ?? '') }}"
                    class="flex items-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg border border-gray-200 text-sm font-semibold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Admin
                </a>
            @endif

            <div class="flex items-center gap-2.5 bg-gray-50 rounded-lg px-3.5 py-2 border border-gray-100">
                <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'Operator' }}</span>
            </div>

            <template x-if="!counterId">
                <button type="button" @click="$dispatch('open-modal', 'confirm-logout')"
                    class="flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-500 text-sm font-semibold rounded-lg hover:bg-gray-50 hover:text-red-500 hover:border-red-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    Logout
                </button>
            </template>
        </div>

    </div>
</nav>

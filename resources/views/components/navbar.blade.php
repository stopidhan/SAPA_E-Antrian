@php
    $navMenuItems = [
        [
            'label' => 'Dashboard',
            'url' => 'superadmin',
            'icon' =>
                '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path d="M19.56 23.253H4.44a4.051 4.051 0 0 1-4.05-4.05v-9.115c0-1.317.648-2.56 1.728-3.315l7.56-5.292a4.062 4.062 0 0 1 4.644 0l7.56 5.292a4.056 4.056 0 0 1 1.728 3.315v9.115a4.051 4.051 0 0 1-4.05 4.05zM12 2.366a2.45 2.45 0 0 0-1.393.443l-7.56 5.292a2.433 2.433 0 0 0-1.037 1.987v9.115c0 1.34 1.09 2.43 2.43 2.43h15.12c1.34 0 2.43-1.09 2.43-2.43v-9.115c0-.788-.389-1.533-1.037-1.987l-7.56-5.292A2.438 2.438 0 0 0 12 2.377z"></path><path d="M16.32 23.253H7.68a.816.816 0 0 1-.81-.81v-5.4c0-2.83 2.3-5.13 5.13-5.13s5.13 2.3 5.13 5.13v5.4c0 .443-.367.81-.81.81zm-7.83-1.62h7.02v-4.59c0-1.933-1.577-3.51-3.51-3.51s-3.51 1.577-3.51 3.51z"></path></svg>',
        ],
        [
            'label' => 'Data Instansi',
            'url' => 'profile-instance',
            'icon' =>
                '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>',
        ],
        [
            'label' => 'Laporan & Statistik',
            'url' => 'report',
            'icon' =>
                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
        ],
        [
            'label' => 'Manajemen User',
            'url' => 'management-user',
            'icon' =>
                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
        ],
        [
            'label' => 'Activity Log',
            'url' => 'activity-log',
            'icon' =>
                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
        ],
    ];

    $navbarTitle = $title ?? 'Dashboard';
    $navbarSubtitle = $subtitle ?? null;
    $navbarUserName = $userName ?? 'Admin';
@endphp

@if (isset($withSidebar) && $withSidebar)

    {{-- ===== SIDEBAR ===== --}}
    <aside
        class="bg-white border-r border-gray-100 h-screen fixed top-0 left-0 z-50
                  transition-all duration-300 flex flex-col overflow-hidden"
        :class="sidebarOpen ? 'w-[250px] translate-x-0' : 'w-[64px] -translate-x-full lg:translate-x-0'">

        {{-- Logo Header --}}
        <div class="h-[57px] shrink-0 flex items-center border-b border-gray-100 px-3 gap-3"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <a href="#" class="shrink-0">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow-sm shadow-blue-200">
                    <span class="text-white text-xs font-black tracking-tight">SAPA</span>
                </div>
            </a>
            <span class="text-lg font-bold text-gray-900 whitespace-nowrap" x-show="sidebarOpen" x-cloak
                x-transition:enter="transition-opacity duration-200 delay-150" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-100"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">SAPA</span>
        </div>

        {{-- Menu Items --}}
        <div class="flex-1 overflow-y-auto overflow-x-hidden px-2 py-4">
            <ul class="space-y-1">
                @foreach ($navMenuItems as $item)
                    <li>
                        <a href="{{ $item['url'] ?? '#' }}" title="{{ $item['label'] }}"
                            class="font-medium text-sm flex items-center rounded-lg py-2.5 transition-all
                                {{ request()->is($item['url'])
                                    ? 'bg-blue-50 text-blue-700'
                                    : 'text-slate-700 hover:text-slate-900 hover:bg-gray-100' }}"
                            :class="sidebarOpen
                                ?
                                'px-3 gap-3' : 'justify-center px-0'">
                            {!! $item['icon'] ?? '' !!}
                            <span class="whitespace-nowrap" x-show="sidebarOpen" x-cloak
                                x-transition:enter="transition-opacity duration-200 delay-150"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Logout di bawah --}}
            {{-- <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="javascript:void(0)" title="Logout"
                    class="text-slate-700 hover:text-red-600 hover:bg-red-50 font-medium text-sm flex items-center rounded-lg py-2.5 transition-all"
                    :class="sidebarOpen ? 'px-3 gap-3' : 'justify-center px-0'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5"
                        class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    <span class="whitespace-nowrap" x-show="sidebarOpen" x-cloak
                        x-transition:enter="transition-opacity duration-200 delay-150"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity duration-100" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">Logout</span>
                </a>
            </div> --}}
        </div>
    </aside>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>

    {{-- Top Navbar --}}
    <nav class="bg-white shadow-sm sticky top-0 z-30 border-b border-gray-100 transition-all duration-300"
        :class="sidebarOpen ? 'lg:ml-[250px]' : 'lg:ml-[64px]'">
        <div class="px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-500 hover:text-gray-700"
                    :title="sidebarOpen ? 'Tutup sidebar' : 'Buka sidebar'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">{{ $navbarTitle }}</h1>
                    @if ($navbarSubtitle)
                        <p class="text-xs text-gray-500">{{ $navbarSubtitle }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2.5 bg-gray-50 rounded-lg px-3.5 py-2 border border-gray-100">
                    <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-700 hidden sm:block">{{ $navbarUserName }}</span>
                </div>
                <x-button variant="outline-danger" size="md"
                    icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>'>
                    Logout
                </x-button>
            </div>
        </div>
    </nav>
@else
    {{-- VERSION 1: Navbar Only --}}
    <nav class="bg-white shadow-sm sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-sm shadow-blue-200">
                    <span class="text-white text-xs font-black tracking-tight">SAPA</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $navbarTitle }}</h1>
                    @if ($navbarSubtitle)
                        <p class="text-sm text-gray-500">{{ $navbarSubtitle }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2.5 bg-gray-50 rounded-lg px-3.5 py-2 border border-gray-100">
                    <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ $navbarUserName }}</span>
                </div>
                <x-button variant="outline-danger" size="md"
                    icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>'>
                    Logout
                </x-button>
            </div>
        </div>
    </nav>
@endif

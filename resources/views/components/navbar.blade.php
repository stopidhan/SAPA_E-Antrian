@php
    $instanceSlug = request()->route('instance_slug');
    $instance = \App\Models\Instance::where('instance_slug', $instanceSlug)->first();
    $instanceName = $instance?->instance_name;
    $instanceLogo = $instance?->logo;

    $navMenuGroups = [
        'Sistem & Manajemen' => [
            [
                'label' => 'Konfigurasi Sistem',
                'url' => route('admininstance.dashboard', $instanceSlug),
                'active' => 'admininstance.dashboard',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path d="M19.56 23.253H4.44a4.051 4.051 0 0 1-4.05-4.05v-9.115c0-1.317.648-2.56 1.728-3.315l7.56-5.292a4.062 4.062 0 0 1 4.644 0l7.56 5.292a4.056 4.056 0 0 1 1.728 3.315v9.115a4.051 4.051 0 0 1-4.05 4.05zM12 2.366a2.45 2.45 0 0 0-1.393.443l-7.56 5.292a2.433 2.433 0 0 0-1.037 1.987v9.115c0 1.34 1.09 2.43 2.43 2.43h15.12c1.34 0 2.43-1.09 2.43-2.43v-9.115c0-.788-.389-1.533-1.037-1.987l-7.56-5.292A2.438 2.438 0 0 0 12 2.377z"></path><path d="M16.32 23.253H7.68a.816.816 0 0 1-.81-.81v-5.4c0-2.83 2.3-5.13 5.13-5.13s5.13 2.3 5.13 5.13v5.4c0 .443-.367.81-.81.81zm-7.83-1.62h7.02v-4.59c0-1.933-1.577-3.51-3.51-3.51s-3.51 1.577-3.51 3.51z"></path></svg>',
            ],
            [
                'label' => 'Profil Instansi',
                'url' => route('profile.instance', $instanceSlug),
                'active' => 'profile.instance',
                'icon' =>
                    '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>',
            ],
            [
                'label' => 'Laporan & Statistik',
                'url' => route('reports.index', $instanceSlug),
                'active' => 'reports.*',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            ],
            [
                'label' => 'Manajemen User',
                'url' => route('management.user', $instanceSlug),
                'active' => 'management.user',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            ],
            [
                'label' => 'Activity Log',
                'url' => route('activity.log', $instanceSlug),
                'active' => 'activity.log',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            ],
        ],
        'Layanan Publik' => [
            [
                'label' => 'Booking Online',
                'url' => route('booking.register', $instanceSlug),
                'active' => 'booking.*',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.974 0-5.699-.541-7.843-1.418m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>',
            ],
            [
                'label' => 'Booking Onsite',
                'url' => route('kiosk.home', $instanceSlug),
                'active' => 'kiosk.*',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>',
            ],
            [
                'label' => 'Monitor',
                'url' => route('monitor.display', $instanceSlug),
                'active' => 'monitor.*',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" /></svg>',
            ],
        ],
    ];

    $user = Auth::user();
    if ($user && in_array($user->role, ['super_admin', 'admin_instansi'])) {
        $navMenuGroups['Akses Role Lain'] = [
            [
                'label' => 'Staff Konten',
                'url' => route('content.index', $instanceSlug),
                'active' => 'content.*',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>',
            ],
            [
                'label' => 'Supervisor',
                'url' => route('supervisor.dashboard', $instanceSlug),
                'active' => 'supervisor.*',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>',
            ],
            [
                'label' => 'Operator',
                'url' => route('operator.dashboard', $instanceSlug),
                'active' => 'operator.*',
                'icon' =>
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>',
            ],
        ];
    }

    $activeItem = null;
    foreach ($navMenuGroups as $group) {
        $found = collect($group)->first(function ($item) {
            return request()->routeIs($item['active']);
        });
        if ($found) {
            $activeItem = $found;
            break;
        }
    }

    $navbarTitle = $title ?? ($activeItem['label'] ?? 'Dashboard');
    $navbarSubtitle = $subtitle ?? null;
    $navbarUserName = Auth::user()->name ?? 'Admin';
@endphp

@if (isset($withSidebar) && $withSidebar)

    <style>
        /* Sembunyikan scrollbar untuk sidebar menu */
        .sidebar-scroll::-webkit-scrollbar {
            display: none;
        }

        .sidebar-scroll {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        /*
        Custom slim scrollbar untuk sidebar menu.
        
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
            display: block;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #e2e8f0;
            border-radius: 10px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background-color: #cbd5e1;
        }
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
            -ms-overflow-style: auto;
        }
        */
    </style>

    {{-- ===== SIDEBAR ===== --}}
    <aside
        class="bg-white border-r border-gray-100 h-screen fixed top-0 left-0 z-50
              flex flex-col overflow-hidden"
        :class="[$nextTick && mounted ? 'transition-all duration-300' : '',
            sidebarOpen ? 'w-[250px] translate-x-0' : 'w-[64px] -translate-x-full lg:translate-x-0'
        ]">

        {{-- Logo Header --}}
        <div class="h-[57px] shrink-0 flex items-center border-b border-gray-100 px-3 gap-3"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <a href="#" class="shrink-0">
                @if ($instanceLogo)
                    <img src="{{ asset('storage/' . $instanceLogo) }}" alt="Logo"
                        class="w-9 h-9 rounded-xl object-contain shadow-sm bg-white border border-gray-100 p-0.5">
                @else
                    <img src="{{ asset('Icon-SAPA.jpeg') }}" alt="Logo SAPA"
                        class="w-9 h-9 rounded-xl object-contain shadow-sm bg-white border border-gray-100 p-0.5">
                @endif
            </a>
            <span class="text-sm font-bold text-gray-900 whitespace-nowrap" x-show="sidebarOpen" x-cloak
                x-transition:enter="transition-opacity duration-200 delay-150" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">{{ $instanceName ?? 'SAPA' }}</span>
        </div>

        {{-- Menu Items --}}
        <div class="flex-1 overflow-y-auto overflow-x-hidden px-2 py-4 space-y-6 sidebar-scroll">
            @foreach ($navMenuGroups as $groupName => $items)
                <div>
                    <div x-show="sidebarOpen" x-cloak
                        class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2"
                        x-transition:enter="transition-opacity duration-200 delay-150"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity duration-100" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        {{ $groupName }}
                    </div>
                    <ul class="space-y-1">
                        @foreach ($items as $item)
                            <li>
                                <a href="{{ $item['url'] ?? '#' }}" title="{{ $item['label'] }}"
                                    class="font-medium text-sm flex items-center rounded-lg py-2.5 transition-all
                                        {{ request()->routeIs($item['active'])
                                            ? 'bg-primary/10 text-primary'
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
                </div>
            @endforeach
        </div>

        {{-- System Footer --}}
        <div class="shrink-0 border-t border-gray-100 p-4 bg-gray-50/50">
            <div class="flex flex-col items-center justify-center text-center overflow-hidden">
                <span class="text-xs font-bold text-slate-700 whitespace-nowrap transition-all duration-300"
                    x-show="sidebarOpen" x-cloak>
                    SAPA E-Antrian
                </span>
                <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap transition-all duration-300"
                    x-show="!sidebarOpen" x-cloak>
                    SAPA
                </span>
                <span class="text-[10px] text-slate-500 whitespace-nowrap mt-0.5 transition-all duration-300"
                    x-show="sidebarOpen" x-cloak>
                    &copy; {{ date('Y') }} Hak Cipta Dilindungi
                </span>
            </div>
        </div>
    </aside>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>

    {{-- Top Navbar --}}
    <nav class="bg-white shadow-sm sticky top-0 z-30 border-b border-gray-100"
        :class="[mounted && 'transition-all duration-300',
            sidebarOpen ? 'lg:ml-[250px]' : 'lg:ml-[64px]'
        ]">
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
                @if (Session::has('impersonate_instance_id'))
                    <div
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200 shadow-sm">
                        <span class="flex h-2 w-2 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                        </span>
                        <span class="text-sm font-semibold whitespace-nowrap">Impersonating:
                            {{ Session::get('impersonate_instance_slug') }}</span>
                        <form action="{{ route('developer.stop-impersonating') }}" method="POST"
                            class="inline border-l border-yellow-300 pl-2 ml-1">
                            @csrf
                            <button type="submit"
                                class="text-xs font-bold hover:text-yellow-900 transition-colors">Exit</button>
                        </form>
                    </div>
                @endif
                <div class="flex items-center gap-2.5 bg-gray-50 rounded-lg px-3.5 py-2 border border-gray-100">
                    <div class="w-5 h-5 bg-primary/10 rounded-full flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-700 hidden sm:block">{{ $navbarUserName }}</span>
                </div>
                <x-button type="button" variant="outline-danger" size="md"
                    @click="$dispatch('open-modal', 'logout-confirmation')"
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
                @if ($instanceLogo)
                    <img src="{{ asset('storage/' . $instanceLogo) }}" alt="Logo"
                        class="w-10 h-10 rounded-xl object-contain shadow-sm bg-white border border-gray-100 p-0.5">
                @else
                    <img src="{{ asset('Icon-SAPA.jpeg') }}" alt="Logo SAPA"
                        class="w-10 h-10 rounded-xl object-contain shadow-sm bg-white border border-gray-100 p-0.5">
                @endif
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $instanceName }}</h1>
                    @if ($navbarSubtitle)
                        <p class="text-sm text-gray-500">{{ $navbarSubtitle }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden md:block text-right mr-2 border-r border-gray-200 pr-4">
                    <p class="text-xs font-bold text-slate-700">SAPA E-Antrian</p>
                    <p class="text-[10px] text-slate-500">&copy; {{ date('Y') }} Hak Cipta Dilindungi</p>
                </div>
                {{-- @if ($instanceName)
                    <div
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-100 shadow-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="text-sm font-semibold whitespace-nowrap">{{ $instanceName }}</span>
                    </div>
                @endif --}}
                @if (Session::has('impersonate_instance_id'))
                    <div
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200 shadow-sm">
                        <span class="flex h-2 w-2 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                        </span>
                        <span class="text-sm font-semibold whitespace-nowrap">Impersonating:
                            {{ Session::get('impersonate_instance_slug') }}</span>
                        <form action="{{ route('developer.stop-impersonating') }}" method="POST"
                            class="inline border-l border-yellow-300 pl-2 ml-1">
                            @csrf
                            <button type="submit"
                                class="text-xs font-bold hover:text-yellow-900 transition-colors">Exit</button>
                        </form>
                    </div>
                @endif
                @if (Auth::user() && in_array(Auth::user()->role, ['super_admin', 'admin_instansi']))
                    @if (request()->routeIs('content.*') || request()->routeIs('supervisor.*') || request()->routeIs('operator.*'))
                        <a href="{{ route('admininstance.dashboard', $instanceSlug) }}"
                            class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg border border-gray-200 shadow-sm text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Admin
                        </a>
                    @endif
                @endif
                <div class="flex items-center gap-2.5 bg-gray-50 rounded-lg px-3.5 py-2 border border-gray-100">
                    <div class="w-5 h-5 bg-primary/10 rounded-full flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ $navbarUserName }}</span>
                </div>
                <x-button type="button" variant="outline-danger" size="md"
                    @click="$dispatch('open-modal', 'logout-confirmation')"
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

{{-- Logout Confirmation Modal --}}
<x-modals.modal-confirmation name="logout-confirmation" variant="logout" />

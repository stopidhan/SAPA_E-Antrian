<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Developer</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional style to ensure no gradient and specific blue shades -->
    <style>
        .bg-dev-blue {
            background-color: #1e3a8a;
        }

        /* blue-900 */
        .text-dev-blue {
            color: #1e3a8a;
        }

        .bg-dev-blue-light {
            background-color: #2563eb;
        }

        /* blue-600 */
        .hover-bg-dev-blue-light:hover {
            background-color: #1d4ed8;
        }

        /* blue-700 */

        /* Custom Scrollbar for a cleaner look */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-dev-blue text-white flex-col hidden md:flex shadow-xl z-20">
            <div class="h-16 flex items-center px-6 border-b border-blue-800">
                <svg class="w-8 h-8 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
                <span class="text-xl font-bold tracking-wider">DEV PORTAL</span>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <p class="px-4 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">Management</p>
                <a href="{{ route('developer.instances.index') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('developer.instances.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }} rounded-lg transition-colors font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Instance
                </a>

                @php
                    $currentSlug =
                        request()->route('instance_slug') ??
                        (session('impersonate_instance_slug') ??
                            (optional(Auth::user()->instance)->instance_slug ??
                                (\App\Models\Instance::first()->instance_slug ?? 'admin')));
                @endphp

                <div class="pt-6 mt-6 border-t border-blue-800">
                    <p class="px-4 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">Aksi</p>
                    <form method="POST" action="{{ route('logout', ['instance_slug' => $currentSlug]) }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 hover:text-white rounded-lg transition-colors font-medium">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            Kembali
                        </button>
                    </form>
                </div>
            </nav>
            <div class="p-4 bg-blue-900 border-t border-blue-800">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 rounded-lg bg-blue-700 flex items-center justify-center font-bold text-sm border border-blue-600 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-blue-300 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Top Header -->
            <header class="h-16 bg-white shadow-sm z-10 flex items-center justify-between px-6 lg:px-8">
                <div class="flex items-center md:hidden">
                    <svg class="w-6 h-6 text-dev-blue mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    <span class="text-lg font-bold text-dev-blue tracking-wider">DEV PORTAL</span>
                </div>

                <div class="hidden md:block flex-1">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Instance Management
                    </h2>
                </div>

                <div class="flex items-center ml-4">
                    @if (Session::has('impersonate_instance_id'))
                        <div
                            class="mr-4 bg-yellow-50 px-3 py-1.5 rounded-lg text-sm font-medium text-yellow-800 flex items-center border border-yellow-200 shadow-sm">
                            <span class="flex h-2 w-2 relative mr-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                            </span>
                            <span class="mr-3">Impersonating:
                                <strong>{{ Session::get('impersonate_instance_slug') }}</strong></span>
                            <form action="{{ route('developer.stop-impersonating') }}" method="POST"
                                class="inline border-l border-yellow-300 pl-3">
                                @csrf
                                <button type="submit"
                                    class="text-yellow-700 hover:text-yellow-900 font-bold transition-colors">Exit</button>
                            </form>
                        </div>
                    @endif

                    <div class="md:hidden flex items-center">
                        <form method="POST" action="{{ route('logout', ['instance_slug' => $currentSlug]) }}"
                            class="inline">
                            @csrf
                            <button type="submit"
                                class="text-gray-500 hover:text-blue-600 bg-transparent border-none p-0 cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50/50 p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>

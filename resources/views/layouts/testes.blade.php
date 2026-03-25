<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SAPA - Sistem Antrean Publik')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.x/dist/chart.umd.min.js"></script>

    {{-- Custom Styles --}}
    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee {
            display: inline-block;
            animation: marquee 30s linear infinite;
        }

        @keyframes pulse-ring {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.4);
            }

            50% {
                box-shadow: 0 0 0 12px rgba(234, 179, 8, 0);
            }
        }

        .animate-pulse-ring {
            animation: pulse-ring 2s ease-in-out infinite;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 3px;
        }

        /* Tab active */
        .tab-btn.active {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            color: #1d4ed8;
        }
    </style>

    @stack('head-scripts')
</head>

<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ sidebarOpen: window.innerWidth >= 1024, mounted: false }" x-init="$nextTick(() => mounted = true)">

    @includeWhen(!isset($hideNavbar) || !$hideNavbar, 'components.navbar', [
        'withSidebar' => $withSidebar ?? false,
    ])

    {{-- Content — margin kiri otomatis ikut lebar sidebar --}}
    @if (isset($withSidebar) && $withSidebar)
        <div :class="mounted && 'transition-all duration-300'"
            :style="sidebarOpen ? 'margin-left: 250px' : 'margin-left: 64px'">
            @yield('content')
        </div>
    @else
        <div>
            @yield('content')
        </div>
    @endif

    {{-- Toast Notification Container --}}
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] space-y-2 pointer-events-none" style="min-width:280px">
    </div>

    {{-- Global Toast JS --}}
    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                warning: 'bg-yellow-500',
                info: 'bg-blue-600',
            };
            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ',
            };
            const toast = document.createElement('div');
            toast.className = `flex items-center gap-3 px-4 py-3 rounded-lg text-white shadow-lg pointer-events-auto
                           transition-all duration-300 ${colors[type] ?? colors.success}`;
            toast.innerHTML = `<span class="text-lg font-bold">${icons[type] ?? icons.success}</span>
                           <span class="text-sm font-medium">${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(40px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Flash messages from Laravel session
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', () => showToast('{{ session('success') }}', 'success'));
        @endif
        @if (session('error'))
            document.addEventListener('DOMContentLoaded', () => showToast('{{ session('error') }}', 'error'));
        @endif
        @if (session('warning'))
            document.addEventListener('DOMContentLoaded', () => showToast('{{ session('warning') }}', 'warning'));
        @endif
    </script>

    @stack('scripts')
</body>

</html>

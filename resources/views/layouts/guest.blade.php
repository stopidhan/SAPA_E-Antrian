<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100 min-h-screen selection:bg-indigo-500 selection:text-white">
        


        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10 px-4">
            <div class="w-full {{ $maxWidth }}">
                <div class="mb-8 flex justify-center">
                    <a href="/" class="group flex flex-col items-center gap-4 transition-transform duration-300 hover:scale-105">
                        <div class="p-4 bg-white rounded-3xl shadow-md ring-1 ring-gray-200">
                            <x-application-logo class="w-16 h-16 fill-current text-indigo-600 drop-shadow-md" />
                        </div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-slate-800 drop-shadow-sm">
                            SAPA E-Antrian
                        </h1>
                    </a>
                </div>

                <div class="w-full px-8 py-10 bg-white shadow-xl overflow-hidden sm:rounded-[2rem] ring-1 ring-gray-100">
                    {{ $slot }}
                </div>
                
                <p class="mt-10 text-center text-sm font-medium text-slate-500">
                    &copy; {{ date('Y') }} SAPA E-Antrian. All rights reserved.
                </p>
            </div>
        </div>


    </body>
</html>

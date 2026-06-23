<x-developer-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('developer.instances.index') }}"
                class="text-gray-500 hover:text-blue-600 mr-3 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambahkan Instansi Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Buat Instansi</h1>
            <p class="text-sm text-gray-500 mt-1">Siapkan instansi baru dan akun administrator utamanya.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat {{ count($errors->all()) }} kesalahan pada
                            pengiriman Anda</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('developer.instances.store') }}" method="POST">
                @csrf

                <div class="p-8">
                    <div class="flex items-center mb-6 pb-4 border-b border-gray-100">
                        <div class="bg-blue-100 p-2 rounded-lg text-blue-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Instansi</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 mb-10">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Instansi</label>
                            <input type="text" name="instance_name" value="{{ old('instance_name') }}" required
                                class="block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3 transition-colors"
                                placeholder="cth. RSUD Jakarta">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Slug Instansi (Awalan URL)</label>
                            <div class="flex rounded-md shadow-sm">
                                <span
                                    class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-200 bg-gray-100 text-gray-500 sm:text-sm">
                                    /
                                </span>
                                <input type="text" name="instance_slug" value="{{ old('instance_slug') }}" required
                                    class="flex-1 block w-full rounded-none rounded-r-lg border-gray-200 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3 transition-colors"
                                    placeholder="rsud-jakarta">
                            </div>
                            <p class="text-xs text-gray-500 mt-2 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Hanya huruf, angka, dan strip.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required
                                class="block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3 transition-colors"
                                placeholder="6281234567890">
                            <p class="text-xs text-gray-500 mt-2 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Untuk mengirimkan OTP ke pelanggan.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kode Instansi (UUID)</label>
                            <input type="text" name="instance_code"
                                value="{{ old('instance_code', Str::uuid()->toString()) }}" required
                                class="block w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 focus:ring-0 sm:text-sm px-4 py-3 cursor-not-allowed"
                                readonly>
                        </div>
                    </div>

                    <div class="flex items-center mb-6 pb-4 border-b border-gray-100">
                        <div class="bg-blue-100 p-2 rounded-lg text-blue-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Akun Admin Instansi</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Admin</label>
                            <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                                class="block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3 transition-colors"
                                placeholder="Nama Lengkap Admin">
                        </div>

                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Email Admin</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                            </path>
                                        </svg>
                                    </div>
                                    <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                                        required
                                        class="block w-full pl-10 rounded-lg border-gray-200 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3 transition-colors"
                                        placeholder="admin@example.com">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kata Sandi Admin</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <input type="password" name="admin_password" required minlength="8"
                                        class="block w-full pl-10 rounded-lg border-gray-200 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3 transition-colors"
                                        placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('developer.instances.index') }}"
                        class="px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Buat Instansi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-developer-layout>

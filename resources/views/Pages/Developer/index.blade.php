<x-developer-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Organisasi') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Instansi</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola semua instansi dan level aksesnya.</p>
            </div>
            <a href="{{ route('developer.instances.create') }}"
                class="mt-4 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-5 rounded-lg shadow-sm transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Instansi
            </a>
        </div>

        @if (session('success'))
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm flex items-center"
                role="alert">
                <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="text-blue-700 font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Info Instansi</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Slug / Domain</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Pengguna</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($instances as $instance)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 font-bold text-lg">
                                            {{ strtoupper(substr($instance->instance_name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $instance->instance_name }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5 font-mono">
                                                {{ $instance->instance_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                            </path>
                                        </svg>
                                        {{ $instance->instance_slug }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($instance->is_active)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mr-1.5"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-1.5"></span>
                                            Ditangguhkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                        {{ $instance->users_count ?? 0 }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <form action="{{ route('developer.instances.impersonate', $instance->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-md transition-colors text-xs font-semibold flex items-center">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                                Impersonasi
                                            </button>
                                        </form>
                                        <a href="{{ route('developer.instances.edit', $instance->id) }}"
                                            class="text-gray-600 hover:text-blue-600 bg-gray-50 hover:bg-blue-50 px-3 py-1.5 rounded-md transition-colors text-xs font-semibold flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($instances->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $instances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-developer-layout>

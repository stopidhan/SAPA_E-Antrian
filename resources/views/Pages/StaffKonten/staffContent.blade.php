@extends('layouts.testes')

@section('title', 'Dashboard Kepala Layanan - SAPA')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-3 gap-6">
            {{-- Kiri --}}
            <div class="col-span-2 space-y-6">
                <div class="p-6 text-gray-900 bg-white overflow-hidden shadow-lg sm:rounded-lg ">
                    <h3 class="text-lg font-semibold mb-4">Upload Konten</h3>
                    <x-upload-file />
                </div>
                <div class="p-6 text-gray-900 bg-white overflow-hidden shadow-lg sm:rounded-lg flex flex-col gap-6">
                    <h3 class="text-lg font-semibold">Daftar Konten</h3>

                    <div class="space-y-4">
                        <div class="border border-gray-300 p-4 rounded-lg">
                            <div class="flex flex-row gap-4">
                                <div class="flex-shrink-0 ">
                                    <img src="{{ asset('images/default-image.png') }}" alt="Preview"
                                        class="w-16 h-16 object-cover rounded">
                                </div>

                                <div class="space-y-3">
                                    <h4 class="font-medium">Judul Konten</h4>
                                    <p class="text-sm text-gray-600">Image 10s</p>
                                    <p class="text-sm text-gray-600">Jadwal : 08:00 - 11:00</p>
                                    <div class="flex items-center">
                                        <label class="relative cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" />
                                            <div
                                                class="w-11 h-6 flex items-center bg-gray-300 rounded-full peer peer-checked:after:translate-x-full after:absolute after:left-[2px] peer-checked:after:border-white after:bg-white after:border after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500">
                                            </div>
                                        </label>
                                        <p class="ml-4 text-sm font-medium text-black">Tampilkan di TV Monitor</p>
                                    </div>
                                </div>

                                <div class="ml-auto flex flex-col justify-between items-end">
                                    <div class="mb-2">
                                        <span
                                            class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span>
                                            Aktif
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <!-- Ikon Edit -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5 text-blue-500 cursor-pointer hover:text-blue-700 transition">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>

                                        <!-- Ikon Trash -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5 text-red-500 cursor-pointer hover:text-red-700 transition">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="border border-gray-300 p-4 rounded-lg">
                            <div class="flex flex-row gap-4">
                                <div class="flex-shrink-0 ">
                                    <img src="{{ asset('images/default-image.png') }}" alt="Preview"
                                        class="w-16 h-16 object-cover rounded">
                                </div>

                                <div class="space-y-3">
                                    <h4 class="font-medium">Judul Konten</h4>
                                    <p class="text-sm text-gray-600">Image 10s</p>
                                    <p class="text-sm text-gray-600">Jadwal : 08:00 - 11:00</p>
                                    <div class="flex items-center">
                                        <label class="relative cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" />
                                            <div
                                                class="w-11 h-6 flex items-center bg-gray-300 rounded-full peer peer-checked:after:translate-x-full after:absolute after:left-[2px] peer-checked:after:border-white after:bg-white after:border after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500">
                                            </div>
                                        </label>
                                        <p class="ml-4 text-sm font-medium text-black">Tampilkan di TV Monitor</p>
                                    </div>
                                </div>

                                <div class="ml-auto flex flex-col justify-between items-end">
                                    <div class="mb-2">
                                        <span
                                            class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span>
                                            Aktif
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <!-- Ikon Edit -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5 text-blue-500 cursor-pointer hover:text-blue-700 transition">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>

                                        <!-- Ikon Trash -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5 text-red-500 cursor-pointer hover:text-red-700 transition">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Kanan --}}
            <div class="space-y-6">
                <div class="p-6 text-gray-900 bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Statistik Media</h3>
                    <div>
                        <h4 class="font-medium">Total Media Terunggah</h4>
                        <p class="text-2xl font-bold mb-4 text-blue-500">3</p>
                        <hr class="w-full border-gray-300 my-2" />
                    </div>

                    <div>
                        <h4 class="font-medium">Media Aktif</h4>
                        <p class="text-2xl font-bold text-green-500">2</p>
                        <p class="text-gray-600 mb-4 text-sm">yang sedang ditayangkan</p>
                        <hr class="w-full border-gray-300 my-2" />
                    </div>

                    <div>
                        <h4 class="font-medium mb-3">Breakdown Tipe</h4>
                        <div class="flex flex-col gap-3">
                            <div class="flex flex-row items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 fill-slate-600" viewBox="0 0 24 24">
                                    <path
                                        d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                                </svg>
                                <p class="text-sm">Gambar</p>
                                <p class="text-lg font-bold ml-auto">2</p>
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 fill-slate-600" viewBox="0 0 24 24">
                                    <path
                                        d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z" />
                                </svg>
                                <p class="text-sm">Video</p>
                                <p class="text-lg font-bold ml-auto">1</p>
                            </div>
                        </div>
                    </div>

                </div>
                <div
                    class="p-6 text-gray-900 bg-blue-200/50 border border-blue-300 overflow-hidden shadow-lg sm:rounded-lg space-y-4">
                    <h3 class="text-lg font-semibold mb-2">Preview TV Monitor</h3>
                    <button
                        class="bg-white hover:bg-gray-200 border border-gray-300 text-black font-bold py-2 px-4 rounded-lg w-full">
                        Buka Preview
                    </button>
                    <p class="text-sm text-gray-600 text-center">
                        Lihat tampilan media di layar TV Monitor
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

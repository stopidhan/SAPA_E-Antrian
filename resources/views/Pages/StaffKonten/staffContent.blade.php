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
                                    <div class="flex items-center gap-3">
                                        <x-toggle-switch />
                                        <label class="text-sm font-medium text-black">Tampilkan di TV Monitor</label>
                                    </div>
                                </div>

                                <div class="ml-auto flex flex-col justify-between items-end">
                                    <div class="mb-2">
                                        <x-label-status :value="'active'" />
                                    </div>

                                    <x-action-buttons />
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
                                    <div class="flex items-center gap-3">
                                        <x-toggle-switch />
                                        <label class="text-sm font-medium text-black">Tampilkan di TV Monitor</label>
                                    </div>
                                </div>

                                <div class="ml-auto flex flex-col justify-between items-end">
                                    <div class="mb-2">
                                        <x-label-status :value="'active'" />
                                    </div>

                                    <x-action-buttons />
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

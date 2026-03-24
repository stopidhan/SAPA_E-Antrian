{{-- Service Dialog/Modal --}}
<template x-if="showServiceDialog">
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="showServiceDialog = false">
        <div class="bg-white rounded-2xl shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">

            {{-- Modal Header --}}
            <div class="p-6 border-b flex items-center justify-between sticky top-0 bg-white">
                <h3 class="text-lg font-bold" x-text="editingService ? 'Edit Layanan' : 'Tambah Layanan Baru'"></h3>
                <button @click="showServiceDialog = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-6">

                {{-- Service Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Layanan</label>
                    <input type="text" x-model="serviceForm.service_name"
                        placeholder="Contoh: Pelayanan Administrasi"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Queue Prefix --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Antrian (Prefix)</label>
                    <input type="text" x-model="serviceForm.queue_prefix" placeholder="Contoh: ADM" maxlength="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea x-model="serviceForm.description" placeholder="Deskripsi layanan (opsional)" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                {{-- Active Status --}}
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="serviceForm.is_active" class="rounded">
                        <span class="text-sm font-medium text-gray-700">Aktifkan Layanan</span>
                    </label>
                </div>

                <hr class="border-gray-200">

                {{-- Service Counters Section --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-900">Konter yang Melayani</h4>
                        <button @click="addCounter()"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Konter
                        </button>
                    </div>

                    <template x-if="countersList.length === 0">
                        <p class="text-sm text-gray-500 italic text-center py-4">Belum ada konter ditambahkan</p>
                    </template>

                    <div class="space-y-3">
                        <template x-for="(counter, index) in countersList" :key="index">
                            <div class="flex items-end gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Konter</label>
                                    <input type="text" x-model="counter.counter_number" placeholder="Contoh: 1, 2, 3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <button @click="removeCounter(index)"
                                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Hapus konter">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t flex items-center gap-3 justify-end sticky bottom-0 bg-white">
                <button @click="showServiceDialog = false"
                    class="px-6 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </button>
                <button @click="saveService()"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                    Simpan Layanan
                </button>
            </div>

        </div>
    </div>
</template>

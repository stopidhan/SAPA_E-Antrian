    <x-modal name="queue-detail-modal" maxWidth="lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Detail Antrean</h3>
                <button @click="$dispatch('close-modal', 'queue-detail-modal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <template x-if="detailLoading">
                <div class="flex items-center justify-center py-10">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                </div>
            </template>

            <template x-if="!detailLoading && detailData">
                <div class="space-y-4">
                    {{-- Queue Number Badge --}}
                    <div class="text-center bg-blue-50 rounded-xl p-4">
                        <div class="text-sm text-gray-500 mb-1">No. Antrean</div>
                        <div class="text-3xl font-bold text-blue-600" x-text="detailData.queue_number"></div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 mb-0.5">Layanan</div>
                            <div class="font-semibold text-sm" x-text="detailData.service_name"></div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-0.5">Kategori</div>
                            <div class="font-semibold text-sm" x-text="detailData.service_category || '-'"></div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-0.5">Pelanggan</div>
                            <div class="font-semibold text-sm" x-text="detailData.customer_name"></div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-0.5">Telepon</div>
                            <div class="font-semibold text-sm" x-text="detailData.customer_phone"></div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-0.5">Sumber</div>
                            <div class="font-semibold text-sm capitalize" x-text="detailData.queue_source"></div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-0.5">Tanggal</div>
                            <div class="font-semibold text-sm" x-text="detailData.queue_date"></div>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-sm font-semibold text-gray-700 mb-3">Timeline</div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Diambil</span>
                                <span class="font-mono" x-text="detailData.taken_time || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dipanggil</span>
                                <span class="font-mono" x-text="detailData.call_time || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Mulai Dilayani</span>
                                <span class="font-mono" x-text="detailData.service_start_time || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Selesai</span>
                                <span class="font-mono" x-text="detailData.service_end_time || '-'"></span>
                            </div>
                            <div class="flex justify-between border-t pt-2 mt-2">
                                <span class="text-gray-500 font-semibold">Durasi</span>
                                <span class="font-bold text-blue-600"
                                    x-text="(detailData.service_duration || 0) + ' menit'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Operator --}}
                    <div class="flex justify-between items-center bg-gray-50 rounded-xl p-4">
                        <div>
                            <div class="text-xs text-gray-500 mb-0.5">Operator</div>
                            <div class="font-semibold text-sm" x-text="detailData.operator_name"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500 mb-0.5">Loket</div>
                            <div class="font-semibold text-sm" x-text="detailData.counter_name || '-'"></div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <template x-if="detailData.service_description">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                            <div class="text-xs text-gray-500 mb-1">Catatan Layanan</div>
                            <div class="text-sm whitespace-pre-line" x-text="detailData.service_description"></div>
                        </div>
                    </template>

                    {{-- Photos --}}
                    <template x-if="detailData.photos && detailData.photos.length > 0">
                        <div>
                            <div class="text-sm font-semibold text-gray-700 mb-2">Foto Dokumentasi</div>
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="(photo, index) in detailData.photos" :key="index">
                                    <img :src="photo" class="rounded-lg object-cover w-full h-32"
                                        alt="Foto dokumentasi">
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </x-modal>

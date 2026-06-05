@props(['show' => false])

<div @keydown.escape.window="$dispatch('close-modal', 'service-detail')">
    <x-modal name="service-detail" :show="$show" maxWidth="md">
        <div class="p-6 space-y-4">
            <!-- Header -->
            <div class="border-b pb-4 flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Detail Antrean</h3>
                    <p class="text-sm text-gray-500 mt-1">Informasi lengkap data antrean</p>
                </div>
                <div class="bg-blue-100 text-blue-800 text-2xl font-black px-4 py-2 rounded-lg"
                    x-text="selectedQueue?.queue_number || '-'"></div>
            </div>

            <!-- Content -->
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg border">
                        <p class="text-xs text-gray-500 mb-1">Layanan</p>
                        <p class="font-semibold text-gray-900" x-text="selectedQueue?.service_name || '-'"></p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border">
                        <p class="text-xs text-gray-500 mb-1">Tipe Registrasi</p>
                        <p class="font-semibold text-gray-900 capitalize"
                            x-text="selectedQueue?.registration_type || '-'"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg border">
                        <p class="text-xs text-gray-500 mb-1">Waktu Daftar</p>
                        <p class="font-semibold text-gray-900"
                            x-text="selectedQueue?.taken_time || '-'">
                        </p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border">
                        <p class="text-xs text-gray-500 mb-1">Waktu Mulai Dilayani</p>
                        <p class="font-semibold text-gray-900"
                            x-text="selectedQueue?.start_at || '-'">
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg border">
                        <p class="text-xs text-gray-500 mb-1">Waktu Selesai Dilayani</p>
                        <p class="font-semibold text-gray-900"
                            x-text="selectedQueue?.completed_at || '-'">
                        </p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border">
                        <p class="text-xs text-gray-500 mb-1">Durasi Pelayanan</p>
                        <p class="font-semibold text-gray-900"
                            x-text="selectedQueue?.service_time ? selectedQueue.service_time + ' menit' : '-'"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg border">
                        <p class="text-xs text-gray-500 mb-1">Operator</p>
                        <p class="font-semibold text-gray-900" x-text="selectedQueue?.operator_name || '-'"></p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border">
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-md font-medium capitalize"
                            :class="{
                                'bg-green-100 text-green-800': selectedQueue?.status === 'completed',
                                'bg-yellow-100 text-yellow-800': selectedQueue?.status === 'waiting',
                                'bg-blue-100 text-blue-800': selectedQueue?.status === 'serving',
                                'bg-gray-100 text-gray-800': !['completed', 'waiting', 'serving'].includes(selectedQueue
                                    ?.status)
                            }"
                            x-text="selectedQueue?.status || '-'">
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 p-3 rounded-lg border">
                    <p class="text-xs text-gray-500 mb-1">Data Pelanggan</p>
                    <p class="font-semibold text-gray-900" x-text="selectedQueue?.customer_name || '-'"></p>
                    <p class="text-sm text-gray-600" x-text="selectedQueue?.customer_phone || '-'"></p>
                </div>

                <!-- Foto Antrean -->
                <div x-show="selectedQueue?.photos && selectedQueue.photos.length > 0"
                    class="bg-gray-50 p-3 rounded-lg border mt-4">
                    <p class="text-xs text-gray-500 mb-2">Foto Antrean</p>
                    <div class="flex flex-wrap gap-3">
                        <template x-for="(photo, index) in selectedQueue?.photos" :key="index">
                            <a :href="photo" target="_blank"
                                class="block border rounded-lg overflow-hidden shadow-sm hover:opacity-90 transition-opacity bg-white">
                                <img :src="photo" alt="Foto Antrean" class="h-24 w-24 object-cover">
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                <x-button type="button" variant="secondary" @click="$dispatch('close-modal', 'service-detail')">
                    Tutup
                </x-button>
            </div>
        </div>
    </x-modal>
</div>

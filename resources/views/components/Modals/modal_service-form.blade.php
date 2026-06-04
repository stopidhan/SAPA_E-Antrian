@props(['show' => false])

<div @keydown.escape.window="closeModal()">
    <x-modal name="service-form" :show="$show" maxWidth="xl">
        <div class="p-6 space-y-4">
            <!-- Header -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-bold text-gray-900"
                    x-text="editingService ? 'Edit Layanan' : 'Tambah Layanan Baru'"></h3>
                <p class="text-sm text-gray-500 mt-1"
                    x-text="editingService ? 'Perbarui informasi layanan dan konter' : 'Isi form di bawah untuk menambah layanan dan konter baru'">
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="saveService()" class="space-y-6">

                <!-- Service Name -->
                <x-input-text id="service_name" name="service_name" type="text" label="Nama Layanan"
                    placeholder="Contoh: Pelayanan Administrasi" x-model="serviceForm.service_name" required />

                <!-- Queue Prefix -->
                <x-input-text id="queue_prefix" name="queue_prefix" type="text" label="Kode Antrian (Prefix)"
                    placeholder="Contoh: ADM" x-model="serviceForm.queue_prefix" required maxlength="5"
                    class="uppercase" />

                <!-- Description -->
                <x-input-textarea id="description" name="description" label="Deskripsi"
                    placeholder="Deskripsi layanan (opsional)" x-model="serviceForm.description" rows="3" />

                <hr class="border-gray-200">

                <!-- Service Counters Section -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-900">Konter yang Melayani</h4>
                        <button type="button" @click="addCounter()"
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
                                    <x-input-text x-bind:id="'counter_' + index" x-bind:name="'counter[' + index + ']'"
                                        type="text" label="Nomor Konter" placeholder="Contoh: 1, 2, 3"
                                        x-model="counter.counter_number" required />
                                </div>
                                <button type="button" @click="removeCounter(index)"
                                    class="p-2 mb-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
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

                <!-- Action Buttons -->
                <div class="flex gap-3 justify-between pt-4 border-t">
                    <div class="flex gap-3 justify-end ml-auto">
                        <x-button type="button" variant="secondary" @click="closeModal()">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary">
                            <span>Simpan Layanan</span>
                        </x-button>
                    </div>
                </div>

            </form>
        </div>
    </x-modal>
</div>

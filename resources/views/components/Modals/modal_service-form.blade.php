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

                <!-- Performance Standards -->
                <div class="grid grid-cols-2 gap-4">
                    <x-input-text id="fast_max" name="fast_max" type="number" label="Target Waktu Cepat (Menit)"
                        placeholder="Contoh: 2" x-model="serviceForm.fast_max" required min="1" max="60" />

                    <x-input-text id="normal_max" name="normal_max" type="number" label="Target Waktu Normal (Menit)"
                        placeholder="Contoh: 5" x-model="serviceForm.normal_max" required min="2" max="120" />
                </div>

                <!-- Slot and Capacity -->
                <div class="grid grid-cols-2 gap-4">
                    <x-input-text id="slot_duration" name="slot_duration" type="number" label="Durasi Slot (Menit)"
                        placeholder="Contoh: 15" x-model="serviceForm.slot_duration" required min="1" max="120" />

                    <x-input-text id="slot_capacity" name="slot_capacity" type="number" label="Kapasitas per Slot"
                        placeholder="Contoh: 1" x-model="serviceForm.slot_capacity" required min="1" max="50" />
                </div>

                <hr class="border-gray-200">

                <!-- Konfigurasi Batas Waktu & Kuota Booking Online -->
                <div>
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 text-sm flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Batas Waktu Pelayanan & Kuota Booking Online
                        </h4>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Atur batas waktu pelayanan loket (yang juga menjadi interval durasi slot booking online) serta kuota kustomer per slot.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Target Waktu Pelayanan (SLA) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Target Waktu Pelayanan
                                <span class="ml-1 text-xs text-gray-400 font-normal">(menit)</span>
                            </label>
                            <input
                                type="number"
                                x-model.number="serviceForm.slot_duration"
                                min="1" max="480" step="1"
                                placeholder="10"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            <p class="text-[11px] text-gray-400 mt-1">
                                Batas waktu pelayanan loket sekaligus interval slot booking (contoh: 30 menit).
                            </p>
                        </div>
                        <!-- Kuota Booking per Slot -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kuota Booking per Slot
                                <span class="ml-1 text-xs text-gray-400 font-normal">(orang)</span>
                            </label>
                            <input
                                type="number"
                                x-model.number="serviceForm.slot_capacity"
                                min="1" max="500" step="1"
                                placeholder="5"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            <p class="text-[11px] text-gray-400 mt-1">
                                Jumlah kustomer online maksimal yang boleh mendaftar di setiap slot waktu.
                            </p>
                        </div>
                    </div>
                </div>

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

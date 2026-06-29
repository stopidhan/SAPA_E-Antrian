@props(['show' => false])

<div @keydown.escape.window="closeSlotDialog()">
    <x-modal name="slot-form" :show="$show" maxWidth="md">
        <div class="p-6 space-y-4">
            <!-- Header -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-bold text-gray-900" x-text="editingSlot ? 'Edit Slot Waktu' : 'Tambah Slot Waktu'"></h3>
                <p class="text-sm text-gray-500 mt-1"
                    x-text="editingSlot ? 'Perbarui informasi rentang waktu dan kapasitas slot' : 'Isi form di bawah untuk menambah rentang waktu slot antrean online'">
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="saveSlot" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mulai Waktu <span
                                class="text-red-500">*</span></label>
                        <input type="time" x-model="slotForm.start_time" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Selesai Waktu <span
                                class="text-red-500">*</span></label>
                        <input type="time" x-model="slotForm.end_time" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kapasitas (Orang) <span
                            class="text-red-500">*</span></label>
                    <input type="number" x-model.number="slotForm.capacity" min="1" max="1000"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    <p class="text-xs text-gray-500 mt-1">Batas maksimal pendaftar online untuk rentang waktu ini.
                    </p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="closeSlotDialog()"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 active:bg-blue-800 transition shadow-sm flex items-center gap-2">
                        <svg x-show="isSavingSlot" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Simpan Slot
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>

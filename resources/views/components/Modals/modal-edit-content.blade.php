@props(['show' => false])

<x-modal name="edit-content" :show="$show" maxWidth="2xl">
    <div class="p-6 space-y-4">
        <!-- Header -->
        <div class="border-b pb-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Edit Konten</h3>
            </div>
            <button @click="$dispatch('close-modal', 'edit-content')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PATCH')

            <x-input-text name="title" label="Judul Konten" placeholder="Masukkan judul konten" value="" required class="mb-4" />

            <x-input-number name="duration" label="Durasi Tampil (detik)" placeholder="Durasi Tampil (detik)" min="1" max="300" value="" class="mb-4" />

            <div class="mb-4 flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                        class="sr-only peer">
                    <div
                        class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                    </div>
                </label>
                <label class="text-sm font-medium text-gray-700">Aktifkan di TV Monitor</label>
            </div>

            <div class="mb-4">
                <label for="edit_file" class="block text-sm font-medium text-gray-700 mb-2">Ganti File (Opsional)</label>
                <input type="file" name="file" id="edit_file"
                    accept="image/png,image/jpeg,image/jpg,image/gif,video/mp4,video/avi,video/quicktime"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti file</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                <x-button type="button" variant="secondary" @click="$dispatch('close-modal', 'edit-content')">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>
</x-modal>

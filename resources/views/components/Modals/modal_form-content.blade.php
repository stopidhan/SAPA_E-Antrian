@props(['show' => false])

<x-modal name="content-form" :show="$show" maxWidth="2xl">
    <div class="p-6 space-y-4">
        <!-- Header -->
        <div class="border-b pb-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-900" id="modal-content-title">Form Konten</h3>
            </div>
            <button @click="$dispatch('close-modal', 'content-form')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="contentForm" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="form_method" value="POST" disabled>

            <x-input-text name="title" label="Judul Konten" placeholder="Masukkan judul konten" value=""
                required class="mb-4" />

            <div class="grid grid-cols-2 gap-4 mb-4">
                <x-input-number name="duration" label="Durasi Tampil (detik)" placeholder="10" min="1"
                    max="300" value="" required class="mb-0" />

                @php
                    $fitModeOptions = [
                        ['value' => 'object-cover', 'label' => 'Fill / Potong (Cover) - Default'],
                        ['value' => 'object-contain', 'label' => 'Fit / Pas Layar (Contain)'],
                        ['value' => 'object-fill', 'label' => 'Stretch / Tarik (Fill)'],
                        ['value' => 'object-none', 'label' => 'Center / Asli (None)'],
                    ];
                @endphp
                <x-input-dropdown name="fit_mode" label="Fitur Tampilan" :options="$fitModeOptions" value="object-cover"
                    class="mb-0" />
            </div>

            <div class="mb-4 flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                    <div
                        class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                    </div>
                </label>
                <label class="text-sm font-medium text-gray-700">Aktifkan di TV Monitor</label>
            </div>

            <!-- Upload Area from staffContent -->
            <div
                class="relative bg-gray-50 text-center px-4 py-8 rounded flex flex-col items-center justify-center border-2 border-gray-300 border-dashed mt-4">
                <p id="edit-file-note" class="text-sm text-primary mb-2 font-medium hidden">Kosongkan bagian ini jika
                    tidak ingin mengganti file</p>

                <!-- Tombol X Hapus File -->
                <button type="button" id="remove-file-btn" onclick="clearFilePreview()"
                    class="hidden absolute top-3 right-3 bg-red-100 hover:bg-red-200 text-red-600 rounded-full p-1.5 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Preview gambar -->
                <div id="preview-container" class="hidden w-full flex-col items-center">
                    <img id="preview-image" src="" alt="Preview" class="max-h-48 rounded mb-2 object-contain">
                    <p id="preview-filename-image" class="text-sm font-medium text-gray-700 w-full truncate px-4"></p>
                </div>

                <!-- Preview video -->
                <div id="video-preview-container" class="hidden w-full flex-col items-center">
                    <video id="previewVideo" class="w-full max-w-md rounded mb-2" controls></video>
                    <p id="preview-filename-video" class="text-sm font-medium text-gray-700 w-full truncate px-4"></p>
                </div>

                <div id="upload-placeholder" class="w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 mb-4 fill-slate-600 inline-block"
                        viewBox="0 0 24 24">
                        <path
                            d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 mb-4 fill-slate-600 inline-block"
                        viewBox="0 0 24 24">
                        <path
                            d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z" />
                    </svg>
                    <h4 class="text-base font-semibold text-slate-600">Drag and drop files here</h4>
                    <p class="text-xs text-slate-500 mt-4">PNG, JPG, GIF, MP4, AVI, MOV (Max: 50MB) <span
                            class="text-red-500" id="file-required-star">*</span></p>
                    <p class="text-xs text-slate-500 mt-1">Rekomendasi Resolusi: 1080x800 pixels (Rasio 4:3)</p>

                    <div class="mt-6">
                        <input type="file" name="file" id="file" accept="image/*,video/*"
                            onchange="previewFile()" class="hidden" />
                        <label for="file"
                            class="inline-block px-6 py-2.5 rounded text-white text-sm tracking-wider font-semibold border-none outline-none cursor-pointer bg-black hover:bg-gray-700 transition">
                            Browse Files
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end pt-4 border-t mt-6">
                <x-button type="button" variant="secondary" @click="$dispatch('close-modal', 'content-form')">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" id="submit-btn-text">
                    Simpan Konten
                </x-button>
            </div>
        </form>
    </div>
</x-modal>

@extends('layouts.testes')

@section('title', 'Dashboard Staff Konten - SAPA')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-3 gap-6">
            {{-- Kiri --}}
            <div class="col-span-2 space-y-6">
                {{-- Upload Konten --}}
                <div class="p-6 text-gray-900 bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Upload Konten</h3>

                    {{-- Upload Konten - Bagian Judul dan Durasi menggunakan komponen --}}
                    <form action="{{ route('content.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <x-input-text name="title" label="Judul Konten" placeholder="Masukkan judul konten"
                            value="{{ old('title') }}" required class="mb-4" />

                        <x-input-number name="duration" label="Durasi Tampil (detik)" placeholder="10" min="1"
                            max="300" value="{{ old('duration', 10) }}" class="mb-4" />

                        <div class="mb-4 flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active') ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                            <label class="text-sm font-medium text-gray-700">Aktifkan di TV Monitor</label>
                        </div>

                        <div
                            class="relative bg-gray-50 text-center px-4 py-8 rounded flex flex-col items-center justify-center border-2 border-gray-300 border-dashed">

                            <!-- Tombol X Hapus File -->
                            <button type="button" id="remove-file-btn" onclick="clearFilePreview()"
                                class="hidden absolute top-3 right-3 bg-red-100 hover:bg-red-200 text-red-600 rounded-full p-1.5 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Preview gambar -->
                            <div id="preview-container" class="hidden w-full flex-col items-center">
                                <img id="preview-image" src="" alt="Preview"
                                    class="max-h-48 rounded mb-2 object-contain">
                                <p id="preview-filename-image"
                                    class="text-sm font-medium text-gray-700 w-full truncate px-4"></p>
                            </div>

                            <!-- Preview video untuk MP4/video lainnya -->
                            <div id="video-preview-container" class="hidden w-full flex-col items-center">
                                <video id="previewVideo" class="w-full max-w-md rounded mb-2" controls></video>
                                <p id="preview-filename-video"
                                    class="text-sm font-medium text-gray-700 w-full truncate px-4"></p>
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
                                        class="text-red-500">*</span></p>

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

                        <div class="mt-4">
                            <x-button type="submit" variant="primary" size="lg" class="w-full mt-4">
                                Upload Konten
                            </x-button>
                        </div>
                    </form>

                </div>

                {{-- Daftar Konten --}}
                <div class="p-6 text-gray-900 bg-white overflow-hidden shadow-lg sm:rounded-lg flex flex-col gap-6">
                    <h3 class="text-lg font-semibold">Daftar Konten</h3>

                    @forelse($contents as $content)
                        <div class="space-y-4">
                            <div class="border border-gray-300 p-4 rounded-lg">
                                <div class="flex flex-row gap-4">
                                    <div class="flex-shrink-0">
                                        @if ($content->media_type === 'image')
                                            <img src="{{ Storage::url($content->file_path) }}" alt="{{ $content->title }}"
                                                class="w-16 h-16 object-cover rounded" />
                                        @elseif ($content->media_type === 'video')
                                            <video width="320" height="180" controls class="rounded">
                                                <source src="{{ Storage::url($content->file_path) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif
                                    </div>

                                    <div class="space-y-3 flex-grow">
                                        <h4 class="font-medium">{{ $content->title }}</h4>
                                        <p class="text-sm text-gray-600">
                                            {{ ucfirst($content->media_type) }}
                                            @if ($content->duration)
                                                • {{ $content->duration }}s
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Diupload: {{ $content->created_at->format('d M Y, H:i') }}
                                        </p>
                                        <div class="flex items-center gap-3">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer toggle-content"
                                                    data-id="{{ $content->id }}"
                                                    {{ $content->is_active ? 'checked' : '' }}>
                                                <div
                                                    class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                                </div>
                                            </label>
                                            <label class="text-sm font-medium text-black">Tampilkan di TV Monitor</label>
                                        </div>
                                    </div>

                                    <div class="ml-auto flex flex-col justify-between items-end">
                                        <div class="mb-2" id="status-label-{{ $content->id }}">
                                            @if ($content->is_active)
                                                <span
                                                    class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </div>

                                        <x-action-buttons :editAction="'openEditModal(' . $content->id . ')'" :deleteAction="'openDeleteModal(' . $content->id . ')'" />

                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>Belum ada konten yang diupload.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Kanan --}}
            <div class="space-y-6">
                {{-- Statistik Media --}}
                <div class="p-6 text-gray-900 bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Statistik Media</h3>
                    <div>
                        <h4 class="font-medium">Total Media Terunggah</h4>
                        <p class="text-2xl font-bold mb-4 text-blue-500">{{ $totalMedia ?? 0 }}</p>
                        <hr class="w-full border-gray-300 my-2" />
                    </div>

                    <div>
                        <h4 class="font-medium">Media Aktif</h4>
                        <p class="text-2xl font-bold text-green-500">{{ $activeMedia ?? 0 }}</p>
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
                                <p class="text-lg font-bold ml-auto">{{ $imageCount ?? 0 }}</p>
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 fill-slate-600" viewBox="0 0 24 24">
                                    <path
                                        d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z" />
                                </svg>
                                <p class="text-sm">Video</p>
                                <p class="text-lg font-bold ml-auto">{{ $videoCount ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Preview TV Monitor --}}
                <div
                    class="p-6 text-gray-900 bg-blue-200/50 border border-blue-300 overflow-hidden shadow-lg sm:rounded-lg space-y-4">
                    <h3 class="text-lg font-semibold mb-2">Preview TV Monitor</h3>
                    <a href="{{ route('monitor.display') }}" target="_blank"
                        class="block bg-white hover:bg-gray-200 border border-gray-300 text-black font-bold py-2 px-4 rounded-lg w-full text-center">
                        Buka Preview
                    </a>
                    <p class="text-sm text-gray-600 text-center">
                        Lihat tampilan media di layar TV Monitor
                    </p>
                </div>
            </div>
        </div>
    </div> {{-- Edit Modal Component --}}
    <x-modals.modal-edit-content />

    {{-- Delete Confirmation --}}
    <x-modals.modal-confirmation name="delete-content" variant="content" />

    {{-- JavaScript --}}
    <script>
        function clearFilePreview() {
            const input = document.getElementById('file');
            if (input) input.value = ''; // clear input file

            // hide previews & remove btn
            document.getElementById('preview-container').classList.replace('flex', 'hidden');
            document.getElementById('video-preview-container').classList.replace('flex', 'hidden');
            document.getElementById('remove-file-btn').classList.add('hidden');

            const videoEl = document.getElementById('previewVideo');
            if (videoEl) {
                videoEl.src = '';
                videoEl.pause?.();
            }
            const imgEl = document.getElementById('preview-image');
            if (imgEl) imgEl.src = '';

            // show placeholder
            document.getElementById('upload-placeholder').classList.remove('hidden');
        }

        function previewFile() {
            const input = document.getElementById('file');
            const file = input.files && input.files[0];

            if (!file) {
                clearFilePreview();
                return;
            }

            const imgEl = document.getElementById('preview-image');
            const videoEl = document.getElementById('previewVideo');
            const previewContainer = document.getElementById('preview-container');
            const videoPreviewContainer = document.getElementById('video-preview-container');
            const uploadPlaceholder = document.getElementById('upload-placeholder');
            const removeFileBtn = document.getElementById('remove-file-btn');
            const imgFileName = document.getElementById('preview-filename-image');
            const videoFileName = document.getElementById('preview-filename-video');

            // Bersihkan state sebelumnya
            if (imgEl) imgEl.src = '';
            if (previewContainer) previewContainer.classList.replace('flex', 'hidden');
            if (videoEl) {
                videoEl.src = '';
                videoEl.pause?.();
            }
            if (videoPreviewContainer) videoPreviewContainer.classList.replace('flex', 'hidden');

            const url = URL.createObjectURL(file);

            if (file.type.startsWith('image/')) {
                if (imgEl) imgEl.src = url;
                if (imgFileName) imgFileName.textContent = file.name;
                previewContainer.classList.replace('hidden', 'flex');
                if (uploadPlaceholder) uploadPlaceholder.classList.add('hidden');
                if (removeFileBtn) removeFileBtn.classList.remove('hidden');
            } else if (file.type.startsWith('video/')) {
                if (videoEl) videoEl.src = url;
                if (videoFileName) videoFileName.textContent = file.name;
                videoPreviewContainer.classList.replace('hidden', 'flex');
                if (uploadPlaceholder) uploadPlaceholder.classList.add('hidden');
                if (removeFileBtn) removeFileBtn.classList.remove('hidden');
            }
        }

        // Edit Modal
        const contentData = @json($contents);

        function openEditModal(id) {
            const content = contentData.find(c => c.id === id);
            if (!content) return;

            const form = document.getElementById('editForm');
            if (form) form.action = `{{ route('content.update', ':id') }}`.replace(':id', id);

            // Isi nilai untuk komponen input (membaca input dalam #editForm)
            const titleInput = form?.querySelector('input[name="title"]');
            const durationInput = form?.querySelector('input[name="duration"]');
            const isActiveInput = form?.querySelector('input[name="is_active"]');

            if (titleInput) titleInput.value = content.title;
            if (durationInput) durationInput.value = content.duration ?? '';
            if (isActiveInput) isActiveInput.checked = !!content.is_active;

            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'edit-content'
            }));
        }

        function closeEditModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'edit-content'
            }));
        }

        // Delete Modal
        function openDeleteModal(id) {
            const content = contentData.find(c => c.id === id);
            if (!content) return;

            const form = document.getElementById('deleteForm');
            if (form) form.action = `{{ route('content.destroy', ':id') }}`.replace(':id', id);

            const nameEl = document.getElementById('delete-item-name');
            if (nameEl) nameEl.textContent = content.title;

            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'delete-content'
            }));
        }

        function closeDeleteModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'delete-content'
            }));
        }

        // Toggle Content Status
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.toggle-content');

            toggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const contentId = this.getAttribute('data-id');
                    const isChecked = this.checked;

                    fetch(`{{ route('content.toggle', ':id') }}`.replace(':id', contentId), {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (typeof showToast === 'function') {
                                    showToast(data.message, 'success');
                                } else {
                                    console.log(data.message);
                                }
                                
                                // Update status badge dynamically
                                const statusLabel = document.getElementById(`status-label-${contentId}`);
                                if (statusLabel) {
                                    if (data.is_active) {
                                        statusLabel.innerHTML = `<span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Aktif</span>`;
                                    } else {
                                        statusLabel.innerHTML = `<span class="px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Nonaktif</span>`;
                                    }
                                }
                            } else {
                                this.checked = !isChecked;
                                if (typeof showToast === 'function') {
                                    showToast('Gagal mengubah status', 'error');
                                } else {
                                    alert('Gagal mengubah status');
                                }
                            }
                        })
                        .catch(error => {
                            this.checked = !isChecked;
                            if (typeof showToast === 'function') {
                                showToast('Terjadi kesalahan', 'error');
                            } else {
                                alert('Terjadi kesalahan');
                            }
                        });
                });
            });
        });
    </script>
@endsection

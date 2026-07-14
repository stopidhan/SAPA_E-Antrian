@extends('layouts.staff')

@section('title', 'Dashboard Staff Konten - SAPA')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-3 gap-6">
            {{-- Kiri --}}
            <div class="col-span-2 space-y-6">
                {{-- Daftar Konten --}}
                <div class="p-6 text-gray-900 bg-white overflow-hidden shadow-lg sm:rounded-lg flex flex-col gap-6">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-semibold">Daftar Konten</h3>
                        <div class="flex gap-4 items-center">
                            <form id="save-order-form" method="POST"
                                action="{{ route('content.updateOrder', ['instance_slug' => request()->route('instance_slug')]) }}"
                                class="hidden">
                                @csrf
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 text-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    Simpan Urutan
                                </button>
                            </form>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                                Drag & Drop urutan
                            </span>
                            <x-button type="button" variant="primary" onclick="openAddModal()">
                                + Tambah Konten
                            </x-button>
                        </div>
                    </div>

                    <div id="content-list" class="flex flex-col gap-4">
                        @forelse($contents as $content)
                            <div class="space-y-4 sortable-item cursor-move" data-id="{{ $content->id }}">
                                <div
                                    class="border border-gray-300 p-4 rounded-lg bg-white hover:border-blue-400 transition-colors">
                                    <div class="flex flex-row gap-4">
                                        <div
                                            class="flex-shrink-0 text-gray-400 px-2 cursor-move hidden sm:block self-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 8h16M4 16h16" />
                                            </svg>
                                        </div>
                                        <div class="flex-shrink-0 self-center">
                                            @if ($content->media_type === 'image')
                                                <img src="{{ Storage::url($content->file_path) }}"
                                                    alt="{{ $content->title }}"
                                                    class="w-16 h-16 object-cover rounded cursor-pointer hover:opacity-80 transition hover:ring-2 hover:ring-blue-400"
                                                    onclick="openImageModal('{{ Storage::url($content->file_path) }}', '{{ addslashes($content->title) }}')"
                                                    title="Klik untuk memperbesar" />
                                            @elseif ($content->media_type === 'video')
                                                <video width="320" height="180" controls class="rounded">
                                                    <source src="{{ Storage::url($content->file_path) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @endif
                                        </div>

                                        <div class="space-y-3 flex-grow py-1">
                                            <h4 class="font-medium">{{ $content->title }}</h4>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                    @if($content->media_type === 'image')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                    @endif
                                                    {{ ucfirst($content->media_type) }}
                                                </span>
                                                @if ($content->duration)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ $content->duration }}s
                                                    </span>
                                                @endif
                                                @if ($content->fit_mode)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                                        </svg>
                                                        Fit: {{ ucfirst(str_replace('object-', '', $content->fit_mode)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500">
                                                Diupload: {{ $content->created_at->format('d M Y, H:i') }}
                                            </p>
                                            <div class="flex items-center gap-3">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only peer toggle-content"
                                                        data-id="{{ $content->id }}"
                                                        {{ $content->is_active ? 'checked' : '' }}>
                                                    <div
                                                        class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                                    </div>
                                                </label>
                                                <label class="text-sm font-medium text-black">Tampilkan di TV
                                                    Monitor</label>
                                            </div>
                                        </div>

                                        <div class="ml-auto flex flex-col justify-between items-end py-1">
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
                            <div class="text-center py-8 text-gray-500 w-full">
                                <p>Belum ada konten yang diupload.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Kanan --}}
            <div class="space-y-6">
                {{-- Statistik Media --}}
                <div class="p-6 text-gray-900 bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Statistik Media</h3>
                    <div>
                        <h4 class="font-medium">Total Media Terunggah</h4>
                        <p class="text-2xl font-bold mb-4 text-primary">{{ $totalMedia ?? 0 }}</p>
                        <hr class="w-full border-gray-300 my-2" />
                    </div>

                    <div>
                        <h4 class="font-medium">Media Aktif</h4>
                        <p id="active-media-count" class="text-2xl font-bold text-green-500">{{ $activeMedia ?? 0 }}</p>
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
    <x-modals.modal_form-content />

    {{-- Delete Confirmation --}}
    <x-modals.modal-confirmation name="delete-content" variant="content" />

    {{-- Image Preview Modal --}}
    <x-modal name="image-preview" maxWidth="4xl">
        <div class="relative bg-black rounded-lg overflow-hidden flex flex-col">
            <button @click="$dispatch('close-modal', 'image-preview')"
                class="absolute top-4 right-4 text-white hover:text-gray-300 bg-black/50 hover:bg-black/70 rounded-full p-2 transition z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <div class="p-2 flex justify-center items-center min-h-[30vh]">
                <img id="modal-preview-image" src="" alt="Preview"
                    class="max-w-full max-h-[85vh] object-contain" />
            </div>
            <div class="bg-gray-900 text-white p-4 text-center border-t border-gray-800">
                <h4 id="modal-preview-title" class="text-lg font-semibold truncate"></h4>
            </div>
        </div>
    </x-modal>

    {{-- JavaScript --}}
    <script>
        const openModal = (id) => window.dispatchEvent(new CustomEvent('open-modal', {
            detail: id
        }));
        const closeModal = (id) => window.dispatchEvent(new CustomEvent('close-modal', {
            detail: id
        }));
        const el = (id) => document.getElementById(id);
        const toggleClass = (id, cls, force) => el(id)?.classList.toggle(cls, force);

        function clearFilePreview(keepFile = false) {
            if (!keepFile && el('file')) el('file').value = '';
            el('preview-container')?.classList.replace('flex', 'hidden');
            el('video-preview-container')?.classList.replace('flex', 'hidden');
            toggleClass('remove-file-btn', 'hidden', true);
            toggleClass('upload-placeholder', 'hidden', false);
            if (el('previewVideo')) {
                el('previewVideo').src = '';
                el('previewVideo').pause?.();
            }
            if (el('preview-image')) el('preview-image').src = '';
        }

        function previewFile() {
            const file = el('file')?.files[0];
            if (!file) return clearFilePreview();

            clearFilePreview(true);
            toggleClass('upload-placeholder', 'hidden', true);
            toggleClass('remove-file-btn', 'hidden', false);

            const url = URL.createObjectURL(file);
            if (file.type.startsWith('image/')) {
                el('preview-image').src = url;
                el('preview-filename-image').textContent = file.name;
                el('preview-container').classList.replace('hidden', 'flex');
            } else if (file.type.startsWith('video/')) {
                const videoEl = el('previewVideo');
                videoEl.src = url;
                el('preview-filename-video').textContent = file.name;
                el('video-preview-container').classList.replace('hidden', 'flex');
                
                videoEl.onloadedmetadata = function() {
                    const durationInput = document.querySelector('input[name="duration"]');
                    if (durationInput) {
                        durationInput.value = Math.ceil(videoEl.duration);
                    }
                };
            }
        }

        const contentData = @json($contents);

        function setupModalContent(isEdit, content = null) {
            const form = el('contentForm');
            if (!form) return;

            form.reset();
            form.action = isEdit ? `{{ route('content.update', ['content' => 999999]) }}`.replace('999999', content.id) :
                `{{ route('content.store') }}`;
            el('modal-content-title').textContent = isEdit ? 'Edit Konten' : 'Tambah Konten';
            el('submit-btn-text').textContent = isEdit ? 'Simpan Perubahan' : 'Upload Konten';
            el('form_method').disabled = !isEdit;
            el('form_method').value = isEdit ? 'PATCH' : 'POST';

            toggleClass('file-required-star', 'hidden', isEdit);
            toggleClass('edit-file-note', 'hidden', !isEdit);
            clearFilePreview();

            if (form.elements['title']) form.elements['title'].value = content?.title || '';
            if (form.elements['duration']) form.elements['duration'].value = content?.duration || '';
            if (form.elements['is_active']) form.elements['is_active'].checked = isEdit ? !!content.is_active : true;
            if (form.elements['fit_mode']) {
                form.elements['fit_mode'].value = content?.fit_mode || 'object-cover';
                form.elements['fit_mode'].dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }
        }

        const openAddModal = () => {
            setupModalContent(false);
            openModal('content-form');
        };
        const openEditModal = (id) => {
            const c = contentData.find(x => x.id === id);
            if (c) {
                setupModalContent(true, c);
                openModal('content-form');
            }
        };
        const closeEditModal = () => closeModal('content-form');

        function openDeleteModal(id) {
            const c = contentData.find(x => x.id === id);
            if (!c) return;
            if (el('deleteForm-delete-content')) el('deleteForm-delete-content').action = `{{ route('content.destroy', ['content' => 999999]) }}`.replace('999999', id);
            if (el('delete-item-name-delete-content')) el('delete-item-name-delete-content').textContent = c.title;
            openModal('delete-content');
        }
        const closeDeleteModal = () => closeModal('delete-content');

        function openImageModal(url, title) {
            if (el('modal-preview-image')) el('modal-preview-image').src = url;
            if (el('modal-preview-title')) el('modal-preview-title').textContent = title;
            openModal('image-preview');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Content Status
            document.querySelectorAll('.toggle-content').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const contentId = this.getAttribute('data-id'),
                        isChecked = this.checked;
                    fetch(`{{ route('content.toggle', ['content' => 999999]) }}`.replace('999999', contentId), {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                if (typeof showToast === 'function') showToast(data.message,
                                    'success');
                                if (el(`status-label-${contentId}`)) {
                                    el(`status-label-${contentId}`).innerHTML = data.is_active ?
                                        `<span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Aktif</span>` :
                                        `<span class="px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Nonaktif</span>`;
                                }
                                let activeCountEl = el('active-media-count');
                                if (activeCountEl) {
                                    let currentCount = parseInt(activeCountEl.innerText) || 0;
                                    activeCountEl.innerText = data.is_active ? currentCount + 1 : Math.max(0, currentCount - 1);
                                }
                            } else {
                                this.checked = !isChecked;
                                if (typeof showToast === 'function') showToast(data.message ||
                                    'Gagal mengubah status', 'error');
                            }
                        })
                        .catch(() => {
                            this.checked = !isChecked;
                            if (typeof showToast === 'function') showToast('Terjadi kesalahan',
                                'error');
                        });
                });
            });

            // Sortable JS
            if (el('content-list') && el('content-list').children.length > 0) {
                new Sortable(el('content-list'), {
                    animation: 150,
                    ghostClass: 'opacity-50',
                    handle: '.sortable-item',
                    onEnd: () => toggleClass('save-order-form', 'hidden', false)
                });
            }

            // Toast checking
            const toastData = localStorage.getItem('toastMessage');
            if (toastData) {
                try {
                    const {
                        message,
                        type
                    } = JSON.parse(toastData);
                    if (typeof showToast === 'function') showToast(message, type);
                } catch (e) {}
                localStorage.removeItem('toastMessage');
            }

            // Utility for AJAX Forms
            function handleAjaxForm(formId, loadingText, onSuccess, beforeSubmit = null) {
                const form = el(formId);
                if (!form) return;

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (beforeSubmit) beforeSubmit(this);

                    const submitBtn = this.querySelector('button[type="submit"]') || el('submit-btn-text');
                    const originalText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.innerHTML = loadingText;
                        submitBtn.disabled = true;
                    }

                    fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new FormData(this)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (submitBtn) {
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            }
                            if (data.success) onSuccess(data);
                            else if (typeof showToast === 'function') showToast(data.message ||
                                'Gagal memproses', 'error');
                        })
                        .catch(err => {
                            if (submitBtn) {
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            }
                            if (typeof showToast === 'function') showToast('Terjadi kesalahan',
                            'error');
                        });
                });
            }

            const reloadWithToast = (data) => {
                localStorage.setItem('toastMessage', JSON.stringify({
                    message: data.message,
                    type: 'success'
                }));
                window.location.reload();
            };

            handleAjaxForm('deleteForm-delete-content', 'Menghapus...', reloadWithToast);
            handleAjaxForm('contentForm', 'Menyimpan...', reloadWithToast);
            handleAjaxForm('save-order-form', 'Menyimpan...',
                (data) => {
                    if (typeof showToast === 'function') showToast(data.message, 'success');
                    toggleClass('save-order-form', 'hidden', true);
                },
                (form) => {
                    form.querySelectorAll('.order-input').forEach(e => e.remove());
                    document.querySelectorAll('.sortable-item').forEach((item, idx) => {
                        form.insertAdjacentHTML('beforeend',
                            `<input type="hidden" class="order-input" name="order[${idx}][id]" value="${item.getAttribute('data-id')}">`
                            );
                        form.insertAdjacentHTML('beforeend',
                            `<input type="hidden" class="order-input" name="order[${idx}][sort_order]" value="${idx}">`
                            );
                    });
                }
            );
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endsection

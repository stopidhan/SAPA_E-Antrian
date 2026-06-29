@extends('layouts.testes')

@section('title', 'Manajemen User - SAPA')

@php
    $withSidebar = true;

    $statCards = [
        [
            'label' => 'Total User',
            'value' => $stats['total'] ?? 0,
            'color' => 'text-gray-800',
            'icon' =>
                '<svg class="w-5 h-5 text-gray-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
        ],
        [
            'label' => 'Admin Instansi',
            'value' => $stats['admin_instansi'] ?? 0,
            'color' => 'text-red-700',
            'icon' =>
                '<svg class="w-5 h-5 text-red-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>',
        ],
        [
            'label' => 'Kepala Layanan',
            'value' => $stats['kepala_layanan'] ?? 0,
            'color' => 'text-green-700',
            'icon' =>
                '<svg class="w-5 h-5 text-green-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>',
        ],
        [
            'label' => 'Staff Operator',
            'value' => $stats['staff_operator'] ?? 0,
            'color' => 'text-blue-700',
            'icon' =>
                '<svg class="w-5 h-5 text-blue-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>',
        ],
        [
            'label' => 'Staff Konten',
            'value' => $stats['staff_konten'] ?? 0,
            'color' => 'text-purple-700',
            'icon' =>
                '<svg class="w-5 h-5 text-purple-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
        ],
    ];
@endphp

@section('content')
    <div class="bg-gray-50" x-data="userManagement()" x-init="init()">

        <div class="container mx-auto p-6 space-y-6">
            {{-- Statistic Card --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <x-card :cards="$statCards" />
            </div>

            <div class="bg-white rounded-2xl border shadow-sm">
                <div class="p-5 border-b flex flex-row justify-between items-center">
                    <h2 class="font-bold text-lg">Daftar User</h2>
                    <x-button type="button" variant="primary" size="md" @click="openAddModal()"
                        icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /> </svg>'>
                        Tambah User
                    </x-button>
                </div>

                {{-- Search & Filter --}}
                <div class="p-5 border-b bg-gray-50">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="w-full md:flex-1">
                            <x-search-bar name="search" placeholder="Cari nama atau email..." class="w-full" />
                        </div>

                        <div class="w-full md:w-64">
                            <x-input-dropdown name="filterRole" :options="[
                                ['value' => 'all', 'label' => 'Semua Role'],
                                ['value' => 'admin_instansi', 'label' => 'Admin Instansi'],
                                ['value' => 'kepala_layanan', 'label' => 'Kepala Layanan'],
                                ['value' => 'staff_operator', 'label' => 'Staff Operator'],
                                ['value' => 'staff_konten', 'label' => 'Staff Konten'],
                            ]" value="{{ request('filterRole', 'all') }}"
                                class="w-full" />
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <x-table :columns="['No', 'Nama', 'Email', 'Role', 'Status', 'Dibuat', 'Aksi']" :rows="$users" emptyMessage="Tidak ada data user">
                    @forelse ($users as $index => $user)
                        <tr class="user-row hover:bg-gray-50 transition-colors" data-name="{{ strtolower($user->name) }}"
                            data-email="{{ strtolower($user->email) }}" data-role="{{ $user->role }}"
                            data-user='@json($user)'>

                            {{-- No --}}
                            <td class="px-4 py-3 text-gray-500 text-sm">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                            </td>

                            {{-- Nama --}}
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ $user->name }}
                            </td>

                            {{-- Email --}}
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $user->email }}
                            </td>

                            {{-- Role --}}
                            <td class="px-4 py-3 text-sm">
                                <x-label-status :value="$user->role" />
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 text-sm">
                                @if ($user->is_active)
                                    <x-label-status value="active" />
                                @else
                                    <x-label-status value="inactive" />
                                @endif
                            </td>

                            {{-- Dibuat --}}
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $user->created_at->diffForHumans() }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 text-center">
                                @if (auth()->id() !== $user->id)
                                    <x-action-buttons :edit="true" editAction="openEditModal" :toggle="true"
                                        toggleAction="openToggleModal" />
                                @else
                                    <x-action-buttons :edit="true" editAction="openEditModal" :toggle="false" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                Tidak ada data user
                            </td>
                        </tr>
                    @endforelse
                </x-table>

            </div>
        </div>


        <!-- Include the three modals -->
        @include('components.Modals.modal_user-form')
        @include('components.Modals.modal-confirmation')

    </div>
@endsection

@push('scripts')
    <script>
        function userManagement() {
            return {
                search: @js(request('search', '')),
                filterRole: @js(request('filterRole', 'all')),
                selectedUser: null,
                isToggling: false,
                editForm: {
                    id: null,
                    name: '',
                    email: '',
                    role: ''
                },

                isSubmitting: false,
                isDeleting: false,
                isEditMode: false,
                formMethod: 'POST',
                form: {
                    id: null,
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: '',
                    is_active: true,
                },

                init() {
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                this.applyFilter();
                            }
                        });
                    }

                    const roleInput = document.querySelector('input[name="filterRole"]');
                    if (roleInput) {
                        const observer = new MutationObserver(() => {
                            if (this.filterRole !== roleInput.value) {
                                this.filterRole = roleInput.value;
                                this.applyFilter();
                            }
                        });
                        observer.observe(roleInput, {
                            attributes: true
                        });
                    }

                    this.$watch('editForm', (value) => {
                        if (value && value.id) {
                            this.setEditMode(value);
                        }
                    }, {
                        deep: true
                    });
                },

                applyFilter() {
                    let url = new URL(window.location.href);

                    if (this.search) {
                        url.searchParams.set('search', this.search);
                    } else {
                        url.searchParams.delete('search');
                    }

                    if (this.filterRole && this.filterRole !== 'all') {
                        url.searchParams.set('filterRole', this.filterRole);
                    } else {
                        url.searchParams.delete('filterRole');
                    }

                    url.searchParams.delete('page'); // Reset to page 1

                    window.location.href = url.toString();
                },

                openAddModal() {
                    this.setAddMode();
                    this.$dispatch('open-modal', 'user-form');
                },

                openEditModal(event) {
                    const row = event.currentTarget.closest('.user-row');
                    const user = JSON.parse(row.dataset.user);

                    this.editForm = {
                        id: user.id,
                        name: user.name,
                        email: user.email,
                        role: user.role
                    };
                    this.$dispatch('open-modal', 'user-form');
                },

                openToggleModal(event) {
                    const row = event.currentTarget.closest('.user-row');
                    const user = JSON.parse(row.dataset.user);
                    this.selectedUser = user;
                    this.$dispatch('open-modal', 'toggle-user');
                },

                closeToggleModal() {
                    this.$dispatch('close-modal', 'toggle-user');
                    this.selectedUser = null;
                },

                async submitToggle() {
                    if (!this.selectedUser || this.isToggling) {
                        return;
                    }

                    this.isToggling = true;

                    try {
                        const url = `{{ route('users.toggle', ['user' => 999999]) }}`.replace('999999', this.selectedUser.id);
                        const response = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (response.ok) {
                            showToast(data.message || 'Berhasil mengubah status user', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                            return;
                        }

                        showToast(data.message || 'Gagal mengubah status user', 'error');
                    } catch (error) {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan: ' + error.message, 'error');
                    } finally {
                        this.isToggling = false;
                    }
                },

                setEditMode(userData) {
                    this.isEditMode = true;
                    this.formMethod = 'PATCH';
                    this.form = {
                        id: userData.id,
                        name: userData.name,
                        email: userData.email,
                        password: '',
                        password_confirmation: '',
                        role: userData.role,
                        is_active: userData.is_active || true,
                    };

                    this.$nextTick(() => {
                        const roleInput = document.querySelector('input[name="role"]');
                        if (roleInput) {
                            roleInput.value = userData.role;
                            roleInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    });
                },

                setAddMode() {
                    this.isEditMode = false;
                    this.formMethod = 'POST';
                    this.form = {
                        id: null,
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        role: '',
                        is_active: true,
                    };
                },

                closeModal() {
                    this.$dispatch('close-modal', 'user-form');
                    this.resetForm();
                },

                resetForm() {
                    this.setAddMode();
                },

                async submitForm() {
                    const roleInput = document.querySelector('input[name="role"]');
                    if (roleInput && roleInput.value) {
                        this.form.role = roleInput.value;
                    }

                    if (!this.validateForm()) {
                        return;
                    }

                    this.isSubmitting = true;

                    try {
                        const url = this.isEditMode ?
                            `{{ route('users.update', ['user' => 999999]) }}`.replace('999999', this.form.id) :
                            '{{ route('users.store') }}';

                        const payload = {
                            name: this.form.name,
                            email: this.form.email,
                            role: this.form.role,
                            is_active: this.form.is_active,
                        };

                        if (this.form.password) {
                            payload.password = this.form.password;
                            payload.password_confirmation = this.form.password_confirmation;
                        }

                        const response = await fetch(url, {
                            method: this.isEditMode ? 'PATCH' : 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();

                        if (response.ok) {
                            showToast(data.message || 'Berhasil menyimpan user', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToast(data.message || 'Gagal menyimpan user', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan: ' + error.message, 'error');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                validateForm() {
                    if (!this.form.name.trim()) {
                        showToast('Nama tidak boleh kosong', 'warning');
                        return false;
                    }
                    if (!this.form.email.trim()) {
                        showToast('Email tidak boleh kosong', 'warning');
                        return false;
                    }

                    if (this.isEditMode) {
                        if (this.form.password && this.form.password.length < 8) {
                            showToast('Password minimal 8 karakter', 'warning');
                            return false;
                        }
                        if (this.form.password && this.form.password !== this.form.password_confirmation) {
                            showToast('Password tidak cocok', 'warning');
                            return false;
                        }
                    } else {
                        if (!this.form.password) {
                            showToast('Password tidak boleh kosong', 'warning');
                            return false;
                        }
                        if (this.form.password.length < 8) {
                            showToast('Password minimal 8 karakter', 'warning');
                            return false;
                        }
                        if (this.form.password !== this.form.password_confirmation) {
                            showToast('Password tidak cocok', 'warning');
                            return false;
                        }
                    }

                    return true;
                },
            };
        }
    </script>
@endpush

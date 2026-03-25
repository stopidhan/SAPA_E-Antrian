@extends('layouts.testes')

@section('title', 'Manajemen User - SAPA')

@php
    $withSidebar = true;

    $roleLabels = [
        'admin_instansi' => 'Admin Instansi',
        'kepala_layanan' => 'Kepala Layanan',
        'staff_operator' => 'Staff Operator',
        'staff_konten' => 'Staff Konten',
    ];

    $roleColors = [
        'admin_instansi' => 'bg-red-500',
        'kepala_layanan' => 'bg-green-500',
        'staff_operator' => 'bg-blue-500',
        'staff_konten' => 'bg-purple-500',
    ];

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
                            ]" value="all" class="w-full" />
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <x-table :columns="['No', 'Nama', 'Email', 'Role', 'Status', 'Dibuat', 'Aksi']" :rows="$users" emptyMessage="Tidak ada data user">
                    @forelse ($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition-colors" data-name="{{ strtolower($user->name) }}"
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
                                <x-action-buttons :edit="true" editAction="openEditModal" :toggle="true"
                                    toggleAction="openToggleModal" :delete="true" deleteAction="openDeleteModal" />
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

    </div>
@endsection

@push('scripts')
    <script>
        function userManagement() {
            return {
                search: '',
                filterRole: 'all',
                modals: {
                    add: false,
                    edit: false,
                    toggle: false,
                    delete: false
                },
                selectedUser: null,
                editForm: {
                    id: null,
                    name: '',
                    email: '',
                    role: 'staff_operator'
                },

                init() {
                    this.$watch('search', () => this.applyFilter());
                    this.$watch('filterRole', () => this.applyFilter());
                },

                applyFilter() {
                    const rows = document.querySelectorAll('.user-row');
                    rows.forEach(row => {
                        const name = row.dataset.name ?? '';
                        const email = row.dataset.email ?? '';
                        const role = row.dataset.role ?? '';

                        const matchSearch = !this.search ||
                            name.includes(this.search.toLowerCase()) ||
                            email.includes(this.search.toLowerCase());

                        const matchRole = this.filterRole === 'all' || role === this.filterRole;

                        row.style.display = (matchSearch && matchRole) ? '' : 'none';
                    });
                },

                openAddModal() {
                    this.modals.add = true;
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
                    this.modals.edit = true;
                },

                openToggleModal(event) {
                    const row = event.currentTarget.closest('.user-row');
                    const user = JSON.parse(row.dataset.user);
                    this.selectedUser = user;
                    this.modals.toggle = true;
                },

                openDeleteModal(event) {
                    const row = event.currentTarget.closest('.user-row');
                    const user = JSON.parse(row.dataset.user);
                    this.selectedUser = user;
                    this.modals.delete = true;
                }
            };
        }
    </script>
@endpush

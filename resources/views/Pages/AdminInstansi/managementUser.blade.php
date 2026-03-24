@extends('layouts.testes')

@section('title', 'Manajemen User - SAPA')

@php
    $withSidebar = true;

    $users = collect([
        (object) [
            'id' => 1,
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '081234567890',
            'role' => 'admin',
            'is_active' => true,
            'last_login_at' => '2 jam yang lalu',
        ],
        (object) [
            'id' => 2,
            'name' => 'Budi Santoso',
            'username' => 'budi.operator',
            'email' => 'budi@example.com',
            'phone' => '082345678901',
            'role' => 'operator',
            'is_active' => true,
            'last_login_at' => '1 hari yang lalu',
        ],
        (object) [
            'id' => 3,
            'name' => 'Siti Rahayu',
            'username' => 'siti.supervisor',
            'email' => 'siti@example.com',
            'phone' => '083456789012',
            'role' => 'supervisor',
            'is_active' => true,
            'last_login_at' => '3 hari yang lalu',
        ],
        (object) [
            'id' => 4,
            'name' => 'Ahmad Fauzi',
            'username' => 'ahmad.content',
            'email' => 'ahmad@example.com',
            'phone' => null,
            'role' => 'staff-content',
            'is_active' => false,
            'last_login_at' => null,
        ],
    ]);

    $statCards = [
        [
            'label' => 'Total User',
            'value' => $users->count(),
            'color' => 'text-gray-800',
            'icon' =>
                '<svg class="w-5 h-5 text-gray-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
        ],
        [
            'label' => 'Admin',
            'value' => $users->where('role', 'admin')->count(),
            'color' => 'text-red-700',
            'icon' =>
                '<svg class="w-5 h-5 text-red-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>',
        ],
        [
            'label' => 'Supervisor',
            'value' => $users->where('role', 'supervisor')->count(),
            'color' => 'text-green-700',
            'icon' =>
                '<svg class="w-5 h-5 text-green-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>',
        ],
        [
            'label' => 'Operator',
            'value' => $users->where('role', 'operator')->count(),
            'color' => 'text-blue-700',
            'icon' =>
                '<svg class="w-5 h-5 text-blue-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>',
        ],
        [
            'label' => 'Staff Konten',
            'value' => $users->where('role', 'staff-content')->count(),
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
                <div class="p-5 border-b flex flex-row justify-between">
                    <h2 class="font-bold text-lg">Daftar User</h2>
                    <x-button variant="primary"
                        icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>'>
                        Tambah User
                    </x-button>

                </div>

                {{-- Search & Filter --}}
                <div class="p-5 border-b bg-gray-50 flex flex-col md:flex-row gap-3 items-center">
                    <div class="w-full md:flex-1">
                        <x-search-bar name="search" placeholder="Cari nama atau username..." />
                    </div>

                    <div class="w-full md:w-52">
                        <x-input-dropdown name="filterRole" :options="[
                            ['value' => 'all', 'label' => 'Semua Role'],
                            ['value' => 'admin', 'label' => 'Admin'],
                            ['value' => 'staff-content', 'label' => 'Staff Konten'],
                            ['value' => 'operator', 'label' => 'Operator'],
                            ['value' => 'supervisor', 'label' => 'Supervisor'],
                        ]" value="all" />
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <x-table :columns="['No', 'Nama', 'Username', 'Email', 'Role', 'Status', 'Login Terakhir', 'Aksi']" :rows="$users" emptyMessage="Tidak ada data user">
                        @foreach ($users as $index => $user)
                            <tr class="hover:bg-gray-50 transition-colors user-row"
                                data-name="{{ strtolower($user->name) }}" data-username="{{ strtolower($user->username) }}"
                                data-role="{{ $user->role }}">
                                <td class="px-5 py-4 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0
                        {{ $user->role === 'admin'
                            ? 'bg-red-500'
                            : ($user->role === 'operator'
                                ? 'bg-blue-500'
                                : ($user->role === 'supervisor'
                                    ? 'bg-green-500'
                                    : 'bg-purple-500')) }}">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-mono text-gray-600">{{ $user->username }}</td>
                                <td class="px-5 py-4 text-gray-500">{{ $user->email ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <x-label-status :value="$user->role" />
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="flex items-center gap-1.5 text-xs
                    {{ $user->is_active ? 'text-green-700' : 'text-gray-400' }}">
                                        <span
                                            class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-xs">
                                    {{ $user->last_login_at ?? 'Belum pernah' }}
                                </td>
                                <td class="px-5 py-4">
                                    <x-action-buttons :toggle="true" />
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                </div>
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
                    reset: false,
                    delete: false
                },
                selectedUser: null,
                editForm: {
                    id: null,
                    name: '',
                    username: '',
                    role: 'operator',
                    email: '',
                    phone: ''
                },

                init() {
                    this.$watch('search', () => this.applyFilter());
                    this.$watch('filterRole', () => this.applyFilter());
                },

                applyFilter() {
                    const rows = document.querySelectorAll('.user-row');
                    rows.forEach(row => {
                        const name = row.dataset.name ?? '';
                        const username = row.dataset.username ?? '';
                        const role = row.dataset.role ?? '';

                        const matchSearch = !this.search ||
                            name.includes(this.search.toLowerCase()) ||
                            username.includes(this.search.toLowerCase());

                        const matchRole = this.filterRole === 'all' || role === this.filterRole;

                        row.style.display = (matchSearch && matchRole) ? '' : 'none';
                    });
                },

                openAddModal() {
                    this.modals.add = true;
                },

                openEditModal(user) {
                    this.editForm = {
                        id: user.id,
                        name: user.name,
                        username: user.username,
                        role: user.role,
                        email: user.email ?? '',
                        phone: user.phone ?? '',
                    };
                    this.modals.edit = true;
                },

                openResetModal(user) {
                    this.selectedUser = user;
                    this.modals.reset = true;
                },

                openDeleteModal(user) {
                    if (user.username === 'admin') {
                        alert('Admin default tidak dapat dihapus!');
                        return;
                    }
                    this.selectedUser = user;
                    this.modals.delete = true;
                },
            };
        }
    </script>
@endpush

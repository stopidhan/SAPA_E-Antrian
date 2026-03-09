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
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50" x-data="userManagement()" x-init="init()">

        <div class="container mx-auto px-4 py-6 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $statCards = [
                        [
                            'label' => 'Total User',
                            'value' => $users->count(),
                            'icon' => 'users',
                            'color' => 'text-gray-800',
                            'ico_color' => 'text-gray-400',
                        ],
                        [
                            'label' => 'Admin',
                            'value' => $users->where('role', 'admin')->count(),
                            'icon' => 'shield',
                            'color' => 'text-red-700',
                            'ico_color' => 'text-red-400',
                        ],
                        [
                            'label' => 'Operator',
                            'value' => $users->where('role', 'operator')->count(),
                            'icon' => 'usercog',
                            'color' => 'text-blue-700',
                            'ico_color' => 'text-blue-400',
                        ],
                        [
                            'label' => 'Supervisor',
                            'value' => $users->where('role', 'supervisor')->count(),
                            'icon' => 'chart',
                            'color' => 'text-green-700',
                            'ico_color' => 'text-green-400',
                        ],
                    ];
                @endphp
                @foreach ($statCards as $s)
                    <div class="bg-white rounded-2xl border shadow-sm p-5">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm text-gray-500">{{ $s['label'] }}</p>
                            <div class="{{ $s['ico_color'] }} opacity-60">
                                @if ($s['icon'] === 'users')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                @elseif($s['icon'] === 'shield')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                @elseif($s['icon'] === 'usercog')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <p class="text-3xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-2xl border shadow-sm">
                <div class="p-5 border-b flex flex-row justify-between">
                    <h2 class="font-bold text-lg">Daftar User</h2>
                    <button @click="openAddModal()"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah User
                    </button>
                </div>

                {{-- Search & Filter --}}
                <div class="p-5 border-b bg-gray-50 flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-model="search" placeholder="Cari nama atau username..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:w-52">
                        <select x-model="filterRole"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Role</option>
                            <option value="admin">Admin</option>
                            <option value="staff-content">Staff Konten</option>
                            <option value="operator">Operator</option>
                            <option value="supervisor">Supervisor</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left px-5 py-3 font-semibold text-gray-600 w-10">No</th>
                                <th class="text-left px-5 py-3 font-semibold text-gray-600">Nama</th>
                                <th class="text-left px-5 py-3 font-semibold text-gray-600">Username</th>
                                <th class="text-left px-5 py-3 font-semibold text-gray-600">Email</th>
                                <th class="text-left px-5 py-3 font-semibold text-gray-600">Role</th>
                                <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                                <th class="text-left px-5 py-3 font-semibold text-gray-600">Login Terakhir</th>
                                <th class="text-left px-5 py-3 font-semibold text-gray-600 w-44">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="user-table-body">
                            @forelse($users as $index => $user)
                                <tr class="hover:bg-gray-50 transition-colors user-row"
                                    data-name="{{ strtolower($user->name) }}"
                                    data-username="{{ strtolower($user->username) }}" data-role="{{ $user->role }}">
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
                                        @php
                                            $roleBadge = match ($user->role) {
                                                'admin' => [
                                                    'label' => 'Admin',
                                                    'class' => 'bg-red-100 text-red-800 border-red-200',
                                                ],
                                                'staff-content' => [
                                                    'label' => 'Staff Konten',
                                                    'class' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                ],
                                                'operator' => [
                                                    'label' => 'Operator',
                                                    'class' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                ],
                                                'supervisor' => [
                                                    'label' => 'Supervisor',
                                                    'class' => 'bg-green-100 text-green-800 border-green-200',
                                                ],
                                                default => [
                                                    'label' => $user->role,
                                                    'class' => 'bg-gray-100 text-gray-700',
                                                ],
                                            };
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 border rounded-full text-xs font-semibold {{ $roleBadge['class'] }}">
                                            {{ $roleBadge['label'] }}
                                        </span>
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
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            {{-- Edit --}}
                                            <button
                                                @click="openEditModal({{ json_encode(['id' => $user->id, 'name' => $user->name, 'username' => $user->username, 'role' => $user->role, 'email' => $user->email, 'phone' => $user->phone]) }})"
                                                class="flex items-center gap-1 px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>

                                            {{-- Toggle Active --}}
                                            <button type="button" @if ($user->username === 'admin') disabled @endif
                                                class="flex items-center gap-1 px-2.5 py-1.5 border rounded-lg text-xs transition-colors
                                                    {{ $user->is_active
                                                        ? 'border-orange-200 text-orange-600 hover:bg-orange-50'
                                                        : 'border-green-200 text-green-600 hover:bg-green-50' }}
                                                    disabled:opacity-40 disabled:cursor-not-allowed">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="{{ $user->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}" />
                                                </svg>
                                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>

                                            {{-- Delete --}}
                                            <button
                                                @click="openDeleteModal({{ json_encode(['id' => $user->id, 'name' => $user->name, 'username' => $user->username]) }})"
                                                @if ($user->username === 'admin') disabled @endif
                                                class="flex items-center gap-1 px-2.5 py-1.5 border border-red-200 rounded-lg text-xs text-red-600 hover:bg-red-50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12 text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p>Tidak ada data user</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Modal Add --}}
        <div x-show="modals.add" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            @click.self="modals.add = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
                <div class="p-6 border-b flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">Tambah User Baru</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Isi form untuk menambahkan pengguna baru</p>
                    </div>
                    <button @click="modals.add = false"
                        class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="#" class="p-6 space-y-4">

                    <div class="grid grid-cols-1 gap-4">
                        {{-- Nama --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" placeholder="Masukkan nama lengkap"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>

                        {{-- Username --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username" placeholder="Masukkan username (unik)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" placeholder="Minimal 8 karakter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required minlength="8">
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>

                        {{-- Role --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
                                required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">⚙ Administrator</option>
                                <option value="staff-content">📝 Staff Konten</option>
                                <option value="operator">🎧 Operator Loket</option>
                                <option value="supervisor">📊 Kepala Layanan</option>
                            </select>
                        </div>

                        {{-- Email --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">Email (Opsional)</label>
                            <input type="email" name="email" placeholder="email@example.com"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- Phone --}}
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700">No. Telepon (Opsional)</label>
                            <input type="tel" name="phone" placeholder="08xxxxxxxxxx"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="modals.add = false"
                            class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 text-sm font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors">
                            Tambah User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit --}}
        <div x-show="modals.edit" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            @click.self="modals.edit = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
                <div class="p-6 border-b flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">Edit User</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Update informasi pengguna</p>
                    </div>
                    <button @click="modals.edit = false"
                        class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="#" class="p-6 space-y-4">

                    {{-- Username (readonly) --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" :value="editForm.username"
                            class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-500 cursor-not-allowed"
                            disabled>
                    </div>

                    {{-- Nama --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" x-model="editForm.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    {{-- Role --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" x-model="editForm.role"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
                            required>
                            <option value="admin">⚙ Administrator</option>
                            <option value="staff-content">📝 Staff Konten</option>
                            <option value="operator">🎧 Operator Loket</option>
                            <option value="supervisor">📊 Kepala Layanan</option>
                        </select>
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" x-model="editForm.email" placeholder="email@example.com"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Phone --}}
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                        <input type="tel" name="phone" x-model="editForm.phone" placeholder="08xxxxxxxxxx"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="modals.edit = false"
                            class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 text-sm font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Delete --}}
        <div x-show="modals.delete" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            @click.self="modals.delete = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm" @click.stop>
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus User?</h3>
                    <p class="text-sm text-gray-500 mb-1">
                        Apakah Anda yakin ingin menghapus user
                        <strong class="text-gray-800" x-text="selectedUser?.name"></strong>?
                    </p>
                    <p class="text-xs text-red-500 mb-6">Tindakan ini tidak dapat dibatalkan.</p>

                    <form method="POST" action="#">
                        <div class="flex gap-3">
                            <button type="button" @click="modals.delete = false"
                                class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 text-sm font-medium">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-colors">
                                Ya, Hapus
                            </button>
                        </div>
                    </form>
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

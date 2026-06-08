<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with same instance_id
     */
    public function index(Request $request)
    {
        $instanceId = auth()->user()->instance_id;

        $query = User::where('instance_id', $instanceId)
            ->where('role', '!=', 'super_admin');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filterRole') && $request->filterRole !== 'all') {
            $query->where('role', $request->filterRole);
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => User::where('instance_id', $instanceId)->where('role', '!=', 'super_admin')->count(),
            'admin_instansi' => User::where('instance_id', $instanceId)->where('role', 'admin_instansi')->count(),
            'kepala_layanan' => User::where('instance_id', $instanceId)->where('role', 'kepala_layanan')->count(),
            'staff_operator' => User::where('instance_id', $instanceId)->where('role', 'staff_operator')->count(),
            'staff_konten' => User::where('instance_id', $instanceId)->where('role', 'staff_konten')->count(),
        ];

        return view('Pages.AdminInstansi.managementUser', compact('users', 'stats'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                'regex:/^[a-zA-Z0-9\s\.\-\'áàâäãåæèéêëìíîïðòóôõöøùúûüýþÿñçÁÀÂÄÃÅÆÈÉÊËÌÍÎÏÐÒÓÔÕÖØÙÚÛÜÝÞŸÑÇ]+$/',
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin_instansi,kepala_layanan,staff_operator,staff_konten'],
        ], [
            'name.required' => 'Nama user harus diisi.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, angka, spasi, dan tanda hubung.',
            'email.required' => 'Email harus diisi.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role user harus dipilih.',
        ]);

        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'instance_id' => auth()->user()->instance_id,
                'is_active' => true,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan.']);
            }

            return back()->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menambahkan user: ' . $e->getMessage()], 400);
            }

            return back()->withInput()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $instanceSlug, User $user): JsonResponse|RedirectResponse
    {
        if ($user->instance_id !== auth()->user()->instance_id) {
            return $request->wantsJson() 
                ? response()->json(['success' => false, 'message' => 'Unauthorized'], 403)
                : back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                'regex:/^[a-zA-Z0-9\s\.\-\'áàâäãåæèéêëìíîïðòóôõöøùúûüýþÿñçÁÀÂÄÃÅÆÈÉÊËÌÍÎÏÐÒÓÔÕÖØÙÚÛÜÝÞŸÑÇ]+$/',
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin_instansi,kepala_layanan,staff_operator,staff_konten'],
        ], [
            'name.required' => 'Nama user harus diisi.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, angka, spasi, dan tanda hubung.',
            'email.required' => 'Email harus diisi.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role user harus dipilih.',
        ]);

        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'User berhasil diperbarui.']);
            }

            return back()->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal memperbarui user: ' . $e->getMessage()], 400);
            }

            return back()->withInput()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, string $instanceSlug, User $user): JsonResponse|RedirectResponse
    {
        if ($user->instance_id !== auth()->user()->instance_id) {
            return $request->wantsJson() 
                ? response()->json(['success' => false, 'message' => 'Unauthorized'], 403)
                : back()->with('error', 'Unauthorized');
        }

        try {
            if ($user->role === 'super_admin') {
                $message = 'Tidak dapat menonaktifkan Super Admin.';
                return $request->wantsJson()
                    ? response()->json(['success' => false, 'message' => $message], 403)
                    : back()->with('error', $message);
            }

            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "User berhasil {$status}.",
                    'data' => [
                        'id' => $user->id,
                        'is_active' => $user->is_active,
                    ],
                ]);
            }

            return back()->with('success', "User berhasil {$status}.");
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengubah status user: ' . $e->getMessage()], 400);
            }

            return back()->with('error', 'Gagal mengubah status user: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, string $instanceSlug, User $user): JsonResponse|RedirectResponse
    {
        if ($user->instance_id !== auth()->user()->instance_id) {
            return $request->wantsJson() 
                ? response()->json(['success' => false, 'message' => 'Unauthorized'], 403)
                : back()->with('error', 'Unauthorized');
        }

        try {
            if ($user->role === 'super_admin') {
                $message = 'Tidak dapat menghapus Super Admin.';
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $message], 403) : back()->with('error', $message);
            }

            if ($user->id === auth()->id()) {
                $message = 'Anda tidak dapat menghapus akun sendiri.';
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $message], 403) : back()->with('error', $message);
            }

            $user->delete();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
            }

            return back()->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus user: ' . $e->getMessage()], 400);
            }

            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    public function resetPassword(Request $request, string $instanceSlug, User $user): JsonResponse|RedirectResponse
    {
        if ($user->instance_id !== auth()->user()->instance_id) {
            return $request->wantsJson() 
                ? response()->json(['success' => false, 'message' => 'Unauthorized'], 403)
                : back()->with('error', 'Unauthorized');
        }

        try {
            $tempPassword = str()->random(12);

            $user->update(['password' => Hash::make($tempPassword)]);

            $message = "Password user berhasil direset. Password sementara: {$tempPassword}";

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mereset password: ' . $e->getMessage()], 400);
            }

            return back()->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }
}

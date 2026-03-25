<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with same instance_id
     */
    public function index()
    {
        $instanceId = auth()->user()->instance_id;

        $users = User::where('instance_id', $instanceId)
            ->where('role', '!=', 'super_admin')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => User::where('instance_id', $instanceId)->count(),
            'admin_instansi' => User::where('instance_id', $instanceId)
                ->where('role', 'admin_instansi')
                ->count(),
            'kepala_layanan' => User::where('instance_id', $instanceId)
                ->where('role', 'kepala_layanan')
                ->count(),
            'staff_operator' => User::where('instance_id', $instanceId)
                ->where('role', 'staff_operator')
                ->count(),
            'staff_konten' => User::where('instance_id', $instanceId)
                ->where('role', 'staff_konten')
                ->count(),
        ];

        return view('Pages.AdminInstansi.managementUser', compact('users', 'stats'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\'áàâäãåæèéêëìíîïðòóôõöøùúûüýþÿñçÁÀÂÄÃÅÆÈÉÊËÌÍÎÏÐÒÓÔÕÖØÙÚÛÜÝÞŸÑÇ]+$/',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                'string',
                'in:admin_instansi,kepala_layanan,staff_operator,staff_konten',
            ],
        ], [
            'name.required' => 'Nama user harus diisi.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, dan tanda hubung.',
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

            return back()->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $instanceId = auth()->user()->instance_id;
        $user = User::where('instance_id', $instanceId)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\'áàâäãåæèéêëìíîïðòóôõöøùúûüýþÿñçÁÀÂÄÃÅÆÈÉÊËÌÍÎÏÐÒÓÔÕÖØÙÚÛÜÝÞŸÑÇ]+$/',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                'string',
                'in:admin_instansi,kepala_layanan,staff_operator,staff_konten',
            ],
        ], [
            'name.required' => 'Nama user harus diisi.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, dan tanda hubung.',
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

            return back()->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus($id)
    {
        $instanceId = auth()->user()->instance_id;
        $user = User::where('instance_id', $instanceId)
            ->findOrFail($id);

        try {
            if ($user->role === 'super_admin') {
                return back()->with('error', 'Tidak dapat menonaktifkan Super Admin.');
            }

            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "User berhasil {$status}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status user: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified user
     */
    public function destroy($id)
    {
        $instanceId = auth()->user()->instance_id;
        $user = User::where('instance_id', $instanceId)
            ->findOrFail($id);

        try {
            if ($user->role === 'super_admin') {
                return back()->with('error', 'Tidak dapat menghapus Super Admin.');
            }

            if ($user->id === auth()->id()) {
                return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
            }

            $user->delete();

            return back()->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword($id)
    {
        $instanceId = auth()->user()->instance_id;
        $user = User::where('instance_id', $instanceId)
            ->findOrFail($id);

        try {
            $tempPassword = str()->random(12);

            $user->update(['password' => Hash::make($tempPassword)]);

            return back()->with('success', "Password user berhasil direset. Password sementara: {$tempPassword}");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }
}

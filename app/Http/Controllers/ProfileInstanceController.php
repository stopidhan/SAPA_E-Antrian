<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class ProfileInstanceController extends Controller
{
    /**
     * Show the profile instance edit form.
     */
    public function edit()
    {
        $instance = auth()->user()->instance ?? new Instance();

        return view('Pages.AdminInstansi.profileInstance', [
            'instance' => $instance,
        ]);
    }

    /**
     * Update the instance profile information.
     */
    public function update(Request $request)
    {
        $instance = auth()->user()->instance;

        // Jika tidak ada instance, return error
        if (!$instance) {
            return response()->json([
                'success' => false,
                'message' => 'Data instansi tidak ditemukan. Silakan hubungi administrator.',
            ], 404);
        }

        $validated = $request->validate([
            'instance_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'logo' => [
                'nullable',
                File::image()
                    ->max(2 * 1024)
                    ->extensions(['jpg', 'jpeg', 'png', 'webp']),
            ],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($instance->logo && Storage::disk('public')->exists($instance->logo)) {
                Storage::disk('public')->delete($instance->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $instance->update([
            'instance_name' => $validated['instance_name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'logo' => $validated['logo'] ?? $instance->logo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil instansi berhasil diperbarui.',
            'data' => $instance,
        ]);
    }
}

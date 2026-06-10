<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileInstanceController extends Controller
{
    /**
     * Show the profile instance edit form.
     */
    public function edit(Request $request, string $instanceSlug)
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance() ?? new Instance();

        return view('Pages.AdminInstansi.profileInstance', [
            'instance' => $instance,
        ]);
    }

    /**
     * Update the instance profile information.
     */
    public function update(Request $request, string $instanceSlug)
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,webp|max:512',
            'brand_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($instance->logo && Storage::disk('public')->exists($instance->logo)) {
                Storage::disk('public')->delete($instance->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($instance->favicon && Storage::disk('public')->exists($instance->favicon)) {
                Storage::disk('public')->delete($instance->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('favicons', 'public');
        }

        $instance->update([
            'instance_name' => $validated['instance_name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'logo' => $validated['logo'] ?? $instance->logo,
            'favicon' => $validated['favicon'] ?? $instance->favicon,
            'brand_color' => $validated['brand_color'] ?? null,
            'secondary_color' => $validated['secondary_color'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil instansi berhasil diperbarui.',
            'data' => $instance,
        ]);
    }
}

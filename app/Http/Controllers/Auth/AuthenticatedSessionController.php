<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = auth()->user();

        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'status' => 'success',
                'action_label' => 'Login',
                'ip_address' => $request->ip(),
            ])
            ->log("User {$user->name} berhasil login.");

        // [BUG FIX] Hapus "intended URL" jika mengarah ke endpoint API
        // Mencegah bug redirect ke halaman JSON akibat polling AJAX yang masuk
        // saat sesi user lain sedang aktif di background (Intended URL Poisoning)
        $intended = session()->pull('url.intended');
        if ($intended && (str_contains($intended, '/api/') || str_contains($intended, 'api/queues'))) {
            $intended = null; // Buang intended URL yang tidak valid
        }

        // Redirect berdasarkan role user yang baru login
        $instanceSlug = $user->instance?->instance_slug;

        if ($user->role === 'staff_operator' && $instanceSlug) {
            return redirect()->route('operator.dashboard', ['instance_slug' => $instanceSlug]);
        }

        if ($user->role === 'admin_instansi' && $instanceSlug) {
            return redirect()->route('admininstance.dashboard', ['instance_slug' => $instanceSlug]);
        }

        if ($user->role === 'staff_konten' && $instanceSlug) {
            return redirect()->route('content.index', ['instance_slug' => $instanceSlug]);
        }

        if ($user->role === 'kepala_layanan' && $instanceSlug) {
            return redirect()->route('supervisor.dashboard', ['instance_slug' => $instanceSlug]);
        }

        if ($user->role === 'super_admin') {
            return redirect()->route('developer.instances.index');
        }

        // Untuk role lain (admin, kepala layanan, dll)
        if ($intended) {
            return redirect($intended);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            activity('auth')
                ->causedBy($user)
                ->withProperties([
                    'status' => 'success',
                    'action_label' => 'Logout',
                    'ip_address' => $request->ip(),
                ])
                ->log("User {$user->name} berhasil logout.");
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

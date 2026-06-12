<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Instance;
use Illuminate\Support\Facades\Auth;

class CheckCustomerInstance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $customer = Auth::guard('customer')->user();
        $instanceSlug = $request->route('instance_slug');

        if ($customer && $instanceSlug) {
            $instance = Instance::where('instance_slug', $instanceSlug)->first();
            
            // Jika instansi di URL tidak ditemukan, atau
            // Jika instance_id customer tidak cocok dengan id instansi dari URL
            if (!$instance || $customer->instance_id !== $instance->id) {
                // Tendang paksa (Logout)
                Auth::guard('customer')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Arahkan kembali ke halaman login instansi yang dituju
                return redirect()->route('booking.login', ['instance_slug' => $instanceSlug])
                    ->withErrors(['whatsapp' => 'Sesi Anda tidak valid untuk instansi ini. Silakan login kembali.']);
            }
        }

        return $next($request);
    }
}


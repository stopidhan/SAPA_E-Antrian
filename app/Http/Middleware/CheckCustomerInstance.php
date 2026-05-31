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
        $instanceCode = $request->route('instance_code');

        if ($customer && $instanceCode) {
            $instance = Instance::where('instance_code', $instanceCode)->first();
            
            // Jika instansi di URL tidak ditemukan, atau
            // Jika instance_id customer tidak cocok dengan id instansi dari URL
            if (!$instance || $customer->instance_id !== $instance->id) {
                // Tendang paksa (Logout)
                Auth::guard('customer')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Arahkan kembali ke halaman login instansi yang dituju
                return redirect()->route('booking.login', ['instance_code' => $instanceCode])
                    ->withErrors(['whatsapp' => 'Sesi Anda tidak valid untuk instansi ini. Silakan login kembali.']);
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantManager;

class IdentifyTenant
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $instanceSlug = $request->route('instance_slug');

        if ($instanceSlug) {
            $instance = $this->tenantManager->resolve($instanceSlug);

            if (!$instance) {
                abort(404, 'Instansi tidak ditemukan.');
            }

            // Cross-tenant access prevention for authenticated staff
            if (auth()->check()) {
                $user = auth()->user();
                
                // Allow super_admin to access anything
                if (!$user->isSuperAdmin()) {
                    if ($user->instance_id !== $instance->id) {
                        abort(403, 'Akses tidak diizinkan untuk instansi ini.');
                    }
                }
            }

            // Also check for customer auth if we have customer routes here
            if (auth('customer')->check()) {
                $customer = auth('customer')->user();
                if ($customer->instance_id !== $instance->id) {
                     abort(403, 'Akses tidak diizinkan untuk instansi ini.');
                }
            }
        }

        return $next($request);
    }
}

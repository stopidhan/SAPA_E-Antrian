<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantManager;

class CheckInstanceStatus
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
        $instance = $this->tenantManager->getInstance();

        if ($instance && !$instance->is_active) {
            // Allow super_admin to bypass this check if they are impersonating
            if (auth()->check() && auth()->user()->isSuperAdmin()) {
                return $next($request);
            }
            
            abort(403, 'Organisasi ini sedang dinonaktifkan.');
        }

        return $next($request);
    }
}

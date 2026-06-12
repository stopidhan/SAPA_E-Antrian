<?php

namespace App\Services;

use App\Models\Instance;
use Illuminate\Support\Facades\Session;

class TenantManager
{
    protected ?Instance $instance = null;

    /**
     * Resolve and set the current tenant instance based on instance_slug.
     */
    public function resolve(string $instanceSlug): ?Instance
    {
        $this->instance = Instance::where('instance_slug', $instanceSlug)->first();
        return $this->instance;
    }

    /**
     * Get the currently resolved instance.
     */
    public function getInstance(): ?Instance
    {
        return $this->instance;
    }

    /**
     * Get the current instance ID, considering super_admin impersonation.
     */
    public function getInstanceId(): ?int
    {
        if (Session::has('impersonate_instance_id') && auth()->check() && method_exists(auth()->user(), 'isSuperAdmin') && auth()->user()->isSuperAdmin()) {
            return Session::get('impersonate_instance_id');
        }

        return $this->instance ? $this->instance->id : null;
    }

    /**
     * Set the current instance explicitly.
     */
    public function setInstance(Instance $instance): void
    {
        $this->instance = $instance;
    }
}

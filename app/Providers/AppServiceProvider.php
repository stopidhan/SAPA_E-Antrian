<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\TenantManager::class, function ($app) {
            return new \App\Services\TenantManager();
        });
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::matched(function (\Illuminate\Routing\Events\RouteMatched $event) {
            if ($event->route->hasParameter('instance_slug')) {
                $slug = $event->route->parameter('instance_slug');
                \Illuminate\Support\Facades\URL::defaults([
                    'instance_slug' => $slug
                ]);
                session(['last_instance_slug' => $slug]);
            }
        });
    }
}

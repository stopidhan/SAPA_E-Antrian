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
        //
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::matched(function (\Illuminate\Routing\Events\RouteMatched $event) {
            if ($event->route->hasParameter('instance_code')) {
                \Illuminate\Support\Facades\URL::defaults([
                    'instance_code' => $event->route->parameter('instance_code')
                ]);
            }
        });
    }
}

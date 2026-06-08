<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'customer.instance' => \App\Http\Middleware\CheckCustomerInstance::class,
            'identify.tenant' => \App\Http\Middleware\IdentifyTenant::class,
        ]);

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            $instance = $request->route('instance_slug');
            
            // if we somehow are not inside a tenant route but try to access protected stuff
            if (!$instance && session('last_instance_slug')) {
                $instance = session('last_instance_slug');
            }

            if (!$instance) {
                return route('select.instance');
            }

            if ($request->is('*/booking') || $request->is('*/booking/*')) {
                return route('booking.login', ['instance_slug' => $instance]);
            }

            return route('login', ['instance_slug' => $instance]);
        });

        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            $instance = $request->route('instance_slug');
            
            if (!$instance && session('last_instance_slug')) {
                $instance = session('last_instance_slug');
            }

            if (!$instance) {
                 return route('select.instance');
            }

            // Jika pengunjung yang sedang dicek ternyata sudah ter-autentikasi sebagai customer
            if (\Illuminate\Support\Facades\Auth::guard('customer')->check() && ($request->is('*/booking') || $request->is('*/booking/*'))) {
                return route('booking.dashboard', ['instance_slug' => $instance]);
            }

            return route('dashboard', ['instance_slug' => $instance]);
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->back()->withInput($request->except('_token'))->withErrors([
                'booking_register' => 'Halaman terlalu lama didiamkan sehingga sesi berakhir (Kedaluwarsa). Silakan coba lagi.'
            ]);
        });
    })->create();

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
        ]);

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('*/remoteuser') || $request->is('*/remoteuser/*') || $request->is('booking/*')) {
                // Gunakan default route jika parameter URL tidak tersedia (misal saat error session)
                $instance = $request->route('instance_code') ?? 'demo';
                return route('booking.login', ['instance_code' => $instance]);
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            // Jika pengunjung yang sedang dicek ternyata sudah ter-autentikasi sebagai customer
            if (\Illuminate\Support\Facades\Auth::guard('customer')->check() && ($request->is('*/remoteuser') || $request->is('*/remoteuser/*') || $request->is('booking/*'))) {
                $instance = $request->route('instance_code') ?? 'demo';
                return route('booking.dashboard', ['instance_code' => $instance]);
            }

            return route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->back()->withInput($request->except('_token'))->withErrors([
                'booking_register' => 'Halaman terlalu lama didiamkan sehingga sesi berakhir (Kedaluwarsa). Silakan coba lagi.'
            ]);
        });
    })->create();

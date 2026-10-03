<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/tenant-portal.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'             => \App\Http\Middleware\EnsureUserHasRole::class,
            'tenant.has-lease' => \App\Http\Middleware\EnsureTenantHasLease::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('espace-locataire*') || $request->routeIs('tenant-portal.*')) {
                return route('tenant-portal.login');
            }
            return $request->routeIs('customer.*') ? '/connexion-client' : '/connexion';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

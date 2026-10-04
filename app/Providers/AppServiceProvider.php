<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cahier des charges, §7 : 10 envois de preuve par heure et par locataire.
        // Clé par locataire (et non par IP) : derrière un même réseau mobile,
        // plusieurs locataires partagent la même adresse et se bloqueraient.
        RateLimiter::for('tenant-proofs', fn (Request $request) => Limit::perHour(10)
            ->by('tenant-proofs:'.($request->user('tenant')?->id ?? $request->ip())));
    }
}

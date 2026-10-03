<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantHasLease
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = auth()->guard('tenant')->user();

        if (! $tenant || ! $tenant->activeLease) {
            return redirect()->route('tenant-portal.no-lease');
        }

        return $next($request);
    }
}

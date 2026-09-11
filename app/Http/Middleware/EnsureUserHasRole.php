<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /** L'administrateur passe partout : il n'a pas à être listé sur chaque route. */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if ($user->role === Role::Admin || in_array($user->role->value, $roles, true)) {
            return $next($request);
        }

        abort(403);
    }
}

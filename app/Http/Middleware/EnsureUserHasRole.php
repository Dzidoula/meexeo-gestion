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
        // Explicitement le garde web : un compte authentifié sur un autre garde
        // (locataire, client) n'a pas de rôle, et le lire ferait planter la requête
        // au lieu de la refuser.
        $user = $request->user('web');

        if (! $user || ! $user->role instanceof Role) {
            abort(403);
        }

        if ($user->role === Role::Admin || in_array($user->role->value, $roles, true)) {
            return $next($request);
        }

        abort(403);
    }
}

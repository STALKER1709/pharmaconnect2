<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage : ->middleware('role:client') ou ->middleware('role:client,pharmacie')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, "Accès réservé au(x) rôle(s) : ".implode(', ', $roles).'.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Empêche les comptes en attente de validation (pharmacies/livreurs)
 * ou suspendus d'accéder à l'application.
 */
class EnsureUserActif
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->estActif() && ! $user->estAdmin()) {
            auth()->logout();

            return redirect()->route('login')->with('statut', 'en_attente');
        }

        return $next($request);
    }
}

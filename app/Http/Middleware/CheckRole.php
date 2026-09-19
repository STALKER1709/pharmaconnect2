<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage : ->middleware('role:client') ou 'role:admin,pharmacie'.
 * Redirige vers l'espace de l'utilisateur s'il est connecté avec un autre
 * rôle, sinon vers la connexion.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $roleDemandes = array_map(fn (string $r) => Role::tryFrom(trim($r)), $roles);
        $roleDemandes = array_filter($roleDemandes);

        if (! in_array($user->role, $roleDemandes, true)) {
            // Le compte pharmacie/livreur en attente de validation voit une page dédiée
            if ($user->isEnAttente()) {
                return redirect()->route('compte.en-attente');
            }

            return redirect()->to($user->homeRoute())
                ->with('erreur', "Vous n'avez pas accès à cet espace.");
        }

        if (! $user->isActif()) {
            auth()->logout();

            return redirect()->route('login')
                ->with('statut', 'Votre compte a été suspendu par un administrateur.');
        }

        return $next($request);
    }
}

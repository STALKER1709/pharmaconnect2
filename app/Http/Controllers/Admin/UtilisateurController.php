<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CompteValide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UtilisateurController extends Controller
{
    public function index(Request $request): View
    {
        $statut = $request->query('statut', 'en_attente');
        $role = $request->query('role');

        $users = User::query()
            ->whereIn('role', ['pharmacie', 'livreur', 'client'])
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->when($role, fn ($q) => $q->where('role', $role))
            ->with(['pharmacie', 'livreur'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.utilisateurs', [
            'users' => $users,
            'statut' => $statut,
            'role' => $role,
        ]);
    }

    public function show(User $user): View
    {
        $user->load(['pharmacie.horaires', 'livreur']);

        return view('admin.utilisateur', ['user' => $user]);
    }

    /** Valider un compte pharmacie ou livreur (cas d'utilisation admin). */
    public function valider(Request $request, User $user): RedirectResponse
    {
        abort_if($user->estAdmin(), 403);

        $user->update(['statut' => 'actif']);

        if ($user->estPharmacie()) {
            $user->pharmacie?->update(['statut' => 'actif']);
        }
        if ($user->estLivreur()) {
            $user->livreur?->update(['statut' => 'actif', 'disponibilite' => 'hors_ligne']);
        }

        $user->notify(new CompteValide($user->role));

        return back()->with('succes', "Compte de {$user->name} validé.");
    }

    public function suspendre(Request $request, User $user): RedirectResponse
    {
        abort_if($user->estAdmin(), 403);

        $user->update(['statut' => 'suspendu']);

        return back()->with('succes', "Compte de {$user->name} suspendu.");
    }

    public function reactiver(Request $request, User $user): RedirectResponse
    {
        abort_if($user->estAdmin(), 403);

        $user->update(['statut' => 'actif']);
        $user->pharmacie?->update(['statut' => 'actif']);
        $user->livreur?->update(['statut' => 'actif']);

        return back()->with('succes', "Compte de {$user->name} réactivé.");
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->estAdmin(), 403);

        $user->delete();

        return redirect()->route('admin.utilisateurs')->with('succes', 'Compte supprimé.');
    }
}

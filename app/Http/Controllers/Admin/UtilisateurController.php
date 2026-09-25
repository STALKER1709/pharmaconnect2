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
        $type = $request->query('type');           // demandes : pharmacie | livreur
        $statut = $request->query('statut');       // répertoire : actif | suspendu
        $q = trim((string) $request->query('q', ''));

        $recherche = fn ($query) => $query->when($q !== '', fn ($w) => $w->where(fn ($x) => $x
            ->where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->orWhere('telephone', 'like', "%{$q}%")
            ->orWhereHas('pharmacie', fn ($p) => $p->where('nom', 'like', "%{$q}%")->orWhere('quartier', 'like', "%{$q}%"))));

        // Demandes d'inscription en attente (pharmacies et livreurs)
        $demandes = User::query()
            ->whereIn('role', ['pharmacie', 'livreur'])
            ->where('statut', 'en_attente')
            ->when(in_array($type, ['pharmacie', 'livreur'], true), fn ($query) => $query->where('role', $type))
            ->tap($recherche)
            ->with(['pharmacie', 'livreur'])
            ->oldest()
            ->paginate(10, ['*'], 'demandes')
            ->withQueryString();

        // Répertoire des comptes accrédités (actifs ou suspendus)
        $repertoire = User::query()
            ->whereIn('role', ['pharmacie', 'livreur', 'client'])
            ->whereIn('statut', in_array($statut, ['actif', 'suspendu'], true) ? [$statut] : ['actif', 'suspendu'])
            ->tap($recherche)
            ->with(['pharmacie', 'livreur'])
            ->orderByRaw("statut = 'suspendu' desc")
            ->latest()
            ->paginate(10, ['*'], 'page')
            ->withQueryString();

        $compter = fn (array $conditions) => User::whereIn('role', ['pharmacie', 'livreur', 'client'])->where($conditions)->count();

        return view('admin.utilisateurs', [
            'demandes' => $demandes,
            'repertoire' => $repertoire,
            'type' => $type,
            'statut' => $statut,
            'q' => $q,
            'nbPharmaciesAttente' => $compter([['role', 'pharmacie'], ['statut', 'en_attente']]),
            'nbLivreursAttente' => $compter([['role', 'livreur'], ['statut', 'en_attente']]),
            'nbActifs' => $compter([['statut', 'actif']]),
            'nbSuspendus' => $compter([['statut', 'suspendu']]),
            'nbActifsMois' => User::whereIn('role', ['pharmacie', 'livreur', 'client'])->where('statut', 'actif')->where('created_at', '>=', now()->startOfMonth())->count(),
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

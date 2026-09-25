<?php

namespace App\Http\Controllers\Livreur;

use App\Enums\LivraisonStatut;
use App\Http\Controllers\Controller;
use App\Models\Livraison;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $livreur = $request->user()->livreur;

        // Livraisons disponibles (non assignées, commandes prêtes côté pharmacie)
        $disponibles = Livraison::whereNull('livreur_id')
            ->whereIn('statut', [LivraisonStatut::Disponible])
            ->whereHas('commande', fn ($q) => $q->whereIn('statut', ['prete', 'assignee', 'confirmee']))
            ->with(['commande.pharmacie', 'commande.client.user'])
            ->orderBy('created_at')
            ->take(10)
            ->get();

        // Mes livraisons actives
        $actives = Livraison::where('livreur_id', $livreur->id)
            ->whereIn('statut', [LivraisonStatut::Assignee, LivraisonStatut::Acceptee, LivraisonStatut::EnRoute, LivraisonStatut::Arrivee])
            ->with(['commande.pharmacie.user', 'commande.client.user', 'commande.lignes'])
            ->orderBy('created_at')
            ->get();

        $livreesDuJour = Livraison::where('livreur_id', $livreur->id)
            ->where('statut', 'livree')
            ->whereDate('livree_at', today())
            ->with('commande:id,frais_livraison')
            ->get();

        // Historique
        $terminees = Livraison::where('livreur_id', $livreur->id)
            ->whereIn('statut', [LivraisonStatut::Livree, LivraisonStatut::Echec])
            ->with('commande')
            ->latest('livree_at')
            ->paginate(10);

        return view('livreur.dashboard', [
            'livreur' => $livreur,
            'disponibles' => $disponibles,
            'actives' => $actives,
            'terminees' => $terminees,
            'totalLivrees' => Livraison::where('livreur_id', $livreur->id)->where('statut', 'livree')->count(),
            'aujourdhui' => $livreesDuJour->count(),
            'gainsDuJour' => (int) $livreesDuJour->sum(fn ($l) => $l->commande?->frais_livraison ?? 0),
            'distanceDuJour' => (float) $livreesDuJour->sum('distance_km'),
        ]);
    }
}

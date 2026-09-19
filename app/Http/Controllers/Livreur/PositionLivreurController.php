<?php

namespace App\Http\Controllers\Livreur;

use App\Events\PositionLivreurMiseAJour;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Reçoit la position du livreur (envoyée périodiquement par le navigateur
 * via la Geolocation API) et la diffuse sur le canal de la commande.
 */
class PositionLivreurController extends Controller
{
    public function __invoke(Request $request)
    {
        $livreur = Auth::user()->livreur;

        $donnees = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'commande_id' => ['nullable', 'integer', 'exists:commandes,id'],
        ]);

        // Mémorise la dernière position du livreur
        $livreur->update(['latitude' => $donnees['latitude'], 'longitude' => $donnees['longitude']]);

        // Diffuse sur la commande en cours si fournie
        $commandeId = $donnees['commande_id'] ?? null;
        if ($commandeId !== null && $commandeId > 0) {
            $livraison = $livreur->livraisons()
                ->where('commande_id', $commandeId)
                ->whereIn('statut', [\App\Enums\StatutLivraison::Acceptee, \App\Enums\StatutLivraison::EnCours])
                ->first();

            if ($livraison !== null) {
                broadcast(new PositionLivreurMiseAJour(
                    (int) $commandeId,
                    (float) $donnees['latitude'],
                    (float) $donnees['longitude'],
                ))->toOthers();
            }
        }

        return response()->json(['ok' => true]);
    }
}

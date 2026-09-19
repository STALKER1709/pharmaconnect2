<?php

namespace App\Http\Controllers;

use App\Events\PositionLivreurMiseAJour;
use App\Models\Commande;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuiviController extends Controller
{
    /** Page de suivi avec carte Leaflet temps réel. */
    public function show(Request $request, Commande $commande): View
    {
        $this->authorize('view', $commande);

        $commande->load(['pharmacie', 'livreur.user', 'livraison.positions', 'client']);

        return view('suivi.show', compact('commande'));
    }

    /** Dernière position du livreur (polling JSON de secours si Reverb est arrêté). */
    public function position(Request $request, Commande $commande): JsonResponse
    {
        $this->authorize('view', $commande);

        $livraison = $commande->livraison;
        $derniere = $livraison?->dernierePosition();

        return response()->json([
            'statut_livraison' => $livraison?->statut->value,
            'statut_label' => $livraison?->statut->label(),
            'position' => $derniere ? [
                'lat' => (float) $derniere->latitude,
                'lng' => (float) $derniere->longitude,
                'at' => $derniere->signalee_at?->toIso8601String(),
            ] : null,
            'arrivee' => $livraison ? [
                'lat' => (float) $livraison->latitude_arrivee,
                'lng' => (float) $livraison->longitude_arrivee,
            ] : null,
        ]);
    }

    /**
     * Étendue : le client partage sa position (géolocalisation navigateur)
     * pour affiner l'adresse de livraison.
     */
    public function partagerPosition(Request $request, Commande $commande): JsonResponse
    {
        $this->authorize('view', $commande);

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $commande->update([
            'latitude_livraison' => $validated['latitude'],
            'longitude_livraison' => $validated['longitude'],
        ]);

        $commande->client()->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        $commande->livraison()->update([
            'latitude_arrivee' => $validated['latitude'],
            'longitude_arrivee' => $validated['longitude'],
        ]);

        return response()->json(['succes' => true, 'message' => 'Position enregistrée ✓']);
    }
}

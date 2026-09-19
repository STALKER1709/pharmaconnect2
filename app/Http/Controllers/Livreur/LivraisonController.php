<?php

namespace App\Http\Controllers\Livreur;

use App\Enums\CommandeStatut;
use App\Enums\LivraisonStatut;
use App\Events\CommandeStatutChange;
use App\Events\PositionLivreurMiseAJour;
use App\Http\Controllers\Controller;
use App\Models\Livraison;
use App\Models\PositionLivreur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LivraisonController extends Controller
{
    public function show(Request $request, Livraison $livraison): View
    {
        $this->authorize('view', $livraison);

        $livraison->load(['commande.pharmacie.user', 'commande.client.user', 'commande.lignes', 'positions']);

        return view('livreur.livraison', compact('livraison'));
    }

    /** Accepter une livraison (disponible ou assignée). */
    public function accepter(Request $request, Livraison $livraison): RedirectResponse
    {
        $this->authorize('accepter', $livraison);

        $livreur = $request->user()->livreur;

        $livraison->update([
            'livreur_id' => $livreur->id,
            'statut' => LivraisonStatut::Acceptee,
            'acceptee_at' => now(),
        ]);

        $livreur->update(['disponibilite' => 'en_course']);
        $livraison->commande?->update(['livreur_id' => $livreur->id]);

        return redirect()->route('livreur.livraison', $livraison)->with('succes', 'Livraison acceptée — mettez-vous en route dès que la commande est prête.');
    }

    /** Démarrer la route vers le client (diffuse la position en temps réel). */
    public function demarrer(Request $request, Livraison $livraison): RedirectResponse
    {
        $this->authorize('demarrer', $livraison);

        $livraison->update([
            'statut' => LivraisonStatut::EnRoute,
            'en_route_at' => now(),
        ]);

        $livraison->commande?->update([
            'statut' => CommandeStatut::EnLivraison,
            'en_livraison_at' => now(),
        ]);

        event(new CommandeStatutChange($livraison->commande, 'prete'));

        return back()->with('succes', 'Livraison en route — activez le partage de position sur la carte.');
    }

    public function marquerArrivee(Request $request, Livraison $livraison): RedirectResponse
    {
        $this->authorize('marquerArrivee', $livraison);

        $livraison->update(['statut' => LivraisonStatut::Arrivee, 'arrivee_at' => now()]);

        return back()->with('succes', 'Arrivé sur place — remettez la commande au client puis marquez « Livrée ».');
    }

    /** Livrée : le client confirmera la réception pour clôturer. */
    public function livrer(Request $request, Livraison $livraison): RedirectResponse
    {
        $this->authorize('livrer', $livraison);

        $livraison->update(['statut' => LivraisonStatut::Livree, 'livree_at' => now()]);

        return redirect()->route('livreur.dashboard')->with('succes', 'Colis remis — en attente de la confirmation du client.');
    }

    /**
     * Le livreur signale sa position (Geolocation API côté navigateur).
     * Diffusion temps réel via Reverb + historique en base.
     */
    public function signalerPosition(Request $request, Livraison $livraison): \Illuminate\Http\JsonResponse
    {
        $this->authorize('signalerPosition', $livraison);

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $position = PositionLivreur::create([
            'livreur_id' => $livraison->livreur_id,
            'livraison_id' => $livraison->id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'signalee_at' => now(),
        ]);

        $request->user()->livreur->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'derniere_position_at' => now(),
        ]);

        // Garder un historique raisonnable par livraison
        if ($livraison->positions()->count() > 200) {
            $livraison->positions()->oldest('signalee_at')->limit(50)->delete();
        }

        broadcast(new PositionLivreurMiseAJour($position));

        return response()->json(['succes' => true]);
    }

    /** Bascule de disponibilité du livreur. */
    public function basculerDisponibilite(Request $request): RedirectResponse
    {
        $livreur = $request->user()->livreur;

        $livreur->update([
            'disponibilite' => $livreur->disponibilite === 'disponible' ? 'hors_ligne' : 'disponible',
        ]);

        return back()->with('succes', $livreur->disponibilite === 'disponible'
            ? 'Vous êtes en ligne : les nouvelles livraisons peuvent vous être proposées.'
            : 'Vous êtes hors ligne.');
    }
}

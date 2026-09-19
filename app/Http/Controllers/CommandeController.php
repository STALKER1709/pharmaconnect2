<?php

namespace App\Http\Controllers;

use App\Enums\CommandeStatut;
use App\Enums\LivraisonStatut;
use App\Events\CommandeStatutChange;
use App\Models\Avis;
use App\Models\Commande;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommandeController extends Controller
{
    public function index(Request $request): View
    {
        $commandes = Commande::query()
            ->where('client_id', $request->user()->client->id)
            ->with(['pharmacie', 'livraison.livreur.user', 'paiement', 'lignes'])
            ->latest()
            ->paginate(10);

        return view('client.commandes', ['commandes' => $commandes, 'statuts' => CommandeStatut::cases()]);
    }

    public function show(Request $request, Commande $commande): View
    {
        $this->authorize('view', $commande);

        $commande->load(['pharmacie.user', 'livreur.user', 'lignes.medicament', 'paiement', 'livraison.positions', 'avis']);

        return view('client.commande', [
            'commande' => $commande,
            'distanceKm' => \App\Support\Fcfa::distanceKm(
                $commande->pharmacie->latitude,
                $commande->pharmacie->longitude,
                $commande->latitude_livraison,
                $commande->longitude_livraison
            ),
        ]);
    }

    /** Étendue : le client annule sa commande (avant préparation). */
    public function annuler(Request $request, Commande $commande): RedirectResponse
    {
        $this->authorize('annuler', $commande);

        $commande->update(['statut' => CommandeStatut::Annulee, 'annulee_at' => now()]);
        event(new CommandeStatutChange($commande, 'annulee'));

        return back()->with('succes', 'Commande annulée. Le remboursement Mobile Money sera traité sous 72 h.');
    }

    /** Étendue : confirmer la réception (clôture la livraison). */
    public function confirmerReception(Request $request, Commande $commande): RedirectResponse
    {
        $this->authorize('confirmerReception', $commande);

        $commande->update(['statut' => CommandeStatut::Livree, 'livree_at' => now()]);
        $commande->livraison?->update(['statut' => LivraisonStatut::Livree, 'livree_at' => now()]);
        $commande->livreur?->update(['disponibilite' => 'disponible']);

        event(new CommandeStatutChange($commande, $commande->statut->value));

        return back()->with('succes', 'Réception confirmée — merci ! Vous pouvez laisser un avis.');
    }

    /** Spécialisation : avis sur la pharmacie / le livreur / un médicament. */
    public function storeAvis(Request $request, Commande $commande): RedirectResponse
    {
        $this->authorize('laisserAvis', $commande);

        $validated = $request->validate([
            'type' => ['required', 'in:pharmacie,medicament,livreur'],
            'note' => ['required', 'integer', 'between:1,5'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
            'medicament_id' => ['required_if:type,medicament', 'nullable', 'integer'],
        ]);

        $data = [
            'client_id' => $request->user()->client->id,
            'commande_id' => $commande->id,
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'],
        ];

        match ($validated['type']) {
            'pharmacie' => $data['pharmacie_id'] = $commande->pharmacie_id,
            'medicament' => $data['medicament_id'] = $validated['medicament_id'],
            'livreur' => $data['livreur_id'] = $commande->livreur_id,
        };

        if ($validated['type'] === 'livreur' && ! $commande->livreur_id) {
            return back()->with('erreur', 'Aucun livreur à noter pour cette commande.');
        }

        // Un seul avis par client et par cible
        $existant = Avis::where('client_id', $data['client_id'])
            ->where($validated['type'].'_id', $data[$validated['type'].'_id'])
            ->first();

        if ($existant) {
            $existant->update(['note' => $data['note'], 'commentaire' => $data['commentaire']]);
        } else {
            Avis::create($data);
            $this->recalculerNoteMoyenne($validated['type'], $data[$validated['type'].'_id']);
        }

        return back()->with('succes', 'Merci pour votre avis ⭐');
    }

    protected function recalculerNoteMoyenne(string $type, int $id): void
    {
        $colonne = ['pharmacie' => Pharmacie::class, 'medicament' => Medicament::class, 'livreur' => Livreur::class][$type];

        $cible = $colonne::find($id);
        if (! $cible) {
            return;
        }

        $moyenne = round((float) $cible->avis()->avg('note'), 2);
        $cible->update(['note_moyenne' => $moyenne, 'nb_avis' => $cible->avis()->count()]);
    }
}

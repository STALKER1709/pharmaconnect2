<?php

namespace App\Http\Controllers\Pharmacie;

use App\Enums\CommandeStatut;
use App\Events\CommandeStatutChange;
use App\Events\LivraisonAssignee;
use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Livreur;
use App\Models\PharmacieMedicament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommandeController extends Controller
{
    public function index(Request $request): View
    {
        $pharmacie = $request->user()->pharmacie;

        $commandes = Commande::where('pharmacie_id', $pharmacie->id)
            ->with(['client.user', 'livreur.user', 'lignes', 'paiement', 'livraison'])
            ->latest()
            ->paginate(15);

        $enAttente = (clone $commandes)->getCollection()->filter(fn ($c) => $c->statut === CommandeStatut::EnAttente);

        return view('pharmacie.commandes', [
            'pharmacie' => $pharmacie,
            'commandes' => $commandes,
            'livreursDisponibles' => Livreur::where('statut', 'actif')->where('disponibilite', 'disponible')->with('user')->get(),
        ]);
    }

    public function show(Request $request, Commande $commande): View
    {
        $this->authorize('update', $commande);

        $commande->load(['client.user', 'lignes.medicament', 'paiement', 'livraison', 'livreur.user']);

        return view('pharmacie.commande', [
            'commande' => $commande,
            'livreursDisponibles' => Livreur::where('statut', 'actif')->where('disponibilite', 'disponible')->with('user')->get(),
        ]);
    }

    /** Accepter / refuser / marquer prête — machine à états du cas d'utilisation. */
    public function changerStatut(Request $request, Commande $commande): RedirectResponse
    {
        $this->authorize('update', $commande);

        $validated = $request->validate([
            'statut' => ['required', 'in:confirmee,refusee,prete,assignee'],
            'livreur_id' => ['nullable', 'exists:livreurs,id'],
        ]);

        $nouveau = CommandeStatut::from($validated['statut']);
        $ancien = $commande->statut;

        $transitions = [
            CommandeStatut::EnAttente->value => [CommandeStatut::Confirmee, CommandeStatut::Refusee],
            CommandeStatut::Confirmee->value => [CommandeStatut::Prete, CommandeStatut::Refusee],
            CommandeStatut::Prete->value => [CommandeStatut::Assignee],
        ];

        $permis = $transitions[$ancien->value] ?? [];

        if (! in_array($nouveau, $permis, true)) {
            return back()->with('erreur', "Transition impossible : « {$ancien->label()} » → « {$nouveau->label()} ».");
        }

        if ($nouveau === CommandeStatut::Assignee) {
            $request->validate(['livreur_id' => ['required', 'exists:livreurs,id']]);

            $livreur = Livreur::findOrFail($validated['livreur_id']);
            abort_unless($livreur->statut === 'actif' && $livreur->disponibilite === 'disponible', 422, 'Livreur indisponible.');

            $commande->update([
                'statut' => CommandeStatut::Assignee,
                'livreur_id' => $livreur->id,
                'assignee_at' => now(),
            ]);

            $commande->livraison?->update([
                'livreur_id' => $livreur->id,
                'statut' => \App\Enums\LivraisonStatut::Assignee,
                'latitude_depart' => $commande->pharmacie->latitude,
                'longitude_depart' => $commande->pharmacie->longitude,
            ]);

            $livreur->update(['disponibilite' => 'en_course']);

            broadcast(new LivraisonAssignee($commande->livraison));
        }

        if ($nouveau === CommandeStatut::Confirmee) {
            $commande->update(['statut' => $nouveau, 'confirmee_at' => now()]);
        } elseif ($nouveau === CommandeStatut::Prete) {
            $commande->update(['statut' => $nouveau, 'prete_at' => now()]);
        } elseif ($nouveau === CommandeStatut::Refusee) {
            // La commande était payée (stock déjà décrémenté) : on le restitue.
            DB::transaction(function () use ($commande) {
                foreach ($commande->lignes as $ligne) {
                    PharmacieMedicament::query()
                        ->where('pharmacie_id', $commande->pharmacie_id)
                        ->where('medicament_id', $ligne->medicament_id)
                        ->increment('quantite', $ligne->quantite);
                }

                $commande->update(['statut' => $nouveau]);
                $commande->livraison?->update(['statut' => \App\Enums\LivraisonStatut::Annulee]);
            });
        }

        event(new CommandeStatutChange($commande, $ancien->value));

        return back()->with('succes', "Commande {$commande->numero} → {$nouveau->label()}.");
    }
}

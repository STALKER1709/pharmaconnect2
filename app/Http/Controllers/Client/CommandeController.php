<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Parametre;
use App\Services\CommandeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommandeController extends Controller
{
    /** Formulaire « Passer commande » (adresse + géolocalisation + ordonnance). */
    public function create()
    {
        $client = Auth::user()->client;
        $panier = $client->ouPanier();
        $lignes = $panier->lignes()->with(['medicament.pharmacie.user'])->get();

        abort_if($lignes->isEmpty(), 302, 'Votre panier est vide.');

        return view('client.commande-creer', [
            'lignes' => $lignes,
            'pharmacies' => $lignes->groupBy(fn ($l) => $l->medicament->pharmacie_id),
            'sousTotal' => $panier->sousTotal(),
            'fraisLivraison' => Parametre::getInt(Parametre::FRAIS_LIVRAISON, 1000),
            'client' => $client,
        ]);
    }

    /** Crée les commandes (une par pharmacie) depuis le panier. */
    public function store(Request $request, CommandeService $service)
    {
        $client = Auth::user()->client;

        $donnees = $request->validate([
            'adresse' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'ordonnance' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        if ($request->hasFile('ordonnance')) {
            $donnees['ordonnance_path'] = $service->stockerOrdonnance($request->file('ordonnance'));
        }

        $commandes = $service->passerDepuisPanier($client, $donnees);

        if ($commandes->count() === 1) {
            return redirect()
                ->route('client.commandes.show', $commandes->first())
                ->with('succes', 'Votre commande a bien été enregistrée. Vous pouvez maintenant la payer.');
        }

        return redirect()
            ->route('client.commandes.index')
            ->with('succes', 'Votre panier a été découpé en '.$commandes->count().' commandes (une par pharmacie). Vous pouvez maintenant les payer.');
    }

    /** Historique des commandes du client. */
    public function index()
    {
        $client = Auth::user()->client;

        $commandes = Commande::query()
            ->where('client_id', $client->id)
            ->with(['pharmacie.user', 'livreur.user', 'lignes.medicament', 'paiement', 'livraison'])
            ->latest('date')
            ->paginate(10);

        return view('client.commandes', ['commandes' => $commandes]);
    }

    /** Détail + suivi temps réel de la position du livreur. */
    public function show(Commande $commande)
    {
        $client = Auth::user()->client;
        Gate::authorize('view', $commande);

        $commande->load([
            'pharmacie.user',
            'livreur.user',
            'lignes.medicament',
            'paiement',
            'livraison',
        ]);

        return view('client.commande-detail', [
            'commande' => $commande,
            'client' => $client,
        ]);
    }

    /** Extension « Partager sa position » : adresse de livraison via GPS ou carte. */
    public function partagerPosition(Request $request, Commande $commande)
    {
        $client = Auth::user()->client;
        Gate::authorize('view', $commande);

        $donnees = $request->validate([
            'adresse' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $commande->update($donnees);
        $client->update([
            'adresse' => $donnees['adresse'],
            'latitude' => $donnees['latitude'],
            'longitude' => $donnees['longitude'],
        ]);

        return back()->with('succes', 'Adresse de livraison mise à jour.');
    }

    /** Extension « Confirmer réception ». */
    public function confirmerReception(Commande $commande, CommandeService $service)
    {
        $client = Auth::user()->client;
        Gate::authorize('confirmerReception', $commande);

        $service->confirmerReception($commande);

        return back()->with('succes', 'Réception confirmée. Merci ! Vous pouvez maintenant laisser un avis.');
    }

    public function annuler(Commande $commande, CommandeService $service)
    {
        $client = Auth::user()->client;
        Gate::authorize('annuler', $commande);

        $service->annuler($commande);

        return redirect()
            ->route('client.commandes.index')
            ->with('succes', 'La commande a été annulée.');
    }
}

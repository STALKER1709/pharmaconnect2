<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LignePanier;
use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PanierController extends Controller
{
    public function index()
    {
        $client = Auth::user()->client;
        $panier = $client->ouPanier();

        $lignes = $panier->lignes()->with(['medicament.pharmacie.user'])->get();

        return view('client.panier', [
            'panier' => $panier,
            'lignes' => $lignes,
            'sousTotal' => $panier->sousTotal(),
            'fraisLivraison' => \App\Models\Parametre::getInt(\App\Models\Parametre::FRAIS_LIVRAISON, 1000),
        ]);
    }

    public function ajouter(Request $request, Medicament $medicament)
    {
        $request->validate([
            'quantite' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $client = Auth::user()->client;

        // Contrôles métier
        abort_if($medicament->estPerime(), 422, 'Ce médicament est périmé.');
        abort_if($medicament->quantite_stock < 1, 422, 'Ce médicament est en rupture de stock.');

        $panier = $client->ouPanier();
        $quantite = min((int) $request->input('quantite'), $medicament->quantite_stock);

        DB::transaction(function () use ($panier, $medicament, $quantite) {
            $ligne = $panier->lignes()->where('medicament_id', $medicament->id)->first();

            if ($ligne !== null) {
                $ligne->update(['quantite' => min($ligne->quantite + $quantite, $medicament->quantite_stock)]);
            } else {
                $panier->lignes()->create([
                    'medicament_id' => $medicament->id,
                    'quantite' => $quantite,
                ]);
            }
        });

        return back()->with('succes', "« {$medicament->nom} » ajouté au panier.");
    }

    public function majQuantite(Request $request, LignePanier $lignePanier)
    {
        $client = Auth::user()->client;
        abort_unless($lignePanier->panier->client_id === $client->id, 403);

        $request->validate(['quantite' => ['required', 'integer', 'min:0', 'max:99']]);
        $quantite = (int) $request->input('quantite');

        if ($quantite === 0) {
            $lignePanier->delete();

            return back()->with('succes', 'Article retiré du panier.');
        }

        $lignePanier->update(['quantite' => min($quantite, $lignePanier->medicament->quantite_stock)]);

        return back()->with('succes', 'Quantité mise à jour.');
    }

    public function supprimer(LignePanier $lignePanier)
    {
        $client = Auth::user()->client;
        abort_unless($lignePanier->panier->client_id === $client->id, 403);

        $lignePanier->delete();

        return back()->with('succes', 'Article retiré du panier.');
    }
}

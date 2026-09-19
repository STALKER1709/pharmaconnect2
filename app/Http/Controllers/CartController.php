<?php

namespace App\Http\Controllers;

use App\Models\Pharmacie;
use App\Models\PharmacieMedicament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Panier stocké en session — limité à UNE pharmacie par commande
 * (chaque pharmacie gère son stock, ses prix et sa livraison).
 */
class CartController extends Controller
{
    public function index(Request $request): View
    {
        $panier = $request->session()->get('panier', ['pharmacie_id' => null, 'items' => []]);
        $pharmacie = $panier['pharmacie_id'] ? Pharmacie::find($panier['pharmacie_id']) : null;

        $lignes = collect($panier['items'] ?? [])->map(function ($item) {
            $stock = PharmacieMedicament::with(['medicament', 'pharmacie'])->find($item['stock_id']);

            return $stock ? ['stock' => $stock, 'quantite' => $item['quantite'], 'sous_total' => $stock->prix * $item['quantite']] : null;
        })->filter()->values();

        $sousTotal = $lignes->sum('sous_total');
        $frais = $pharmacie?->frais_livraison ?? 0;

        return view('panier.index', [
            'pharmacie' => $pharmacie,
            'lignes' => $lignes,
            'sousTotal' => $sousTotal,
            'fraisLivraison' => $frais,
            'total' => $sousTotal + $frais,
        ]);
    }

    public function ajouter(Request $request, PharmacieMedicament $stock): RedirectResponse
    {
        $request->validate(['quantite' => ['required', 'integer', 'min:1', 'max:'.$stock->quantite]]);

        if (! $stock->estEnStock()) {
            return back()->with('erreur', 'Ce médicament n\'est plus disponible dans cette pharmacie.');
        }

        $quantite = (int) $request->input('quantite', 1);
        $panier = $request->session()->get('panier', ['pharmacie_id' => null, 'items' => []]);

        // Une seule pharmacie à la fois
        if ($panier['pharmacie_id'] !== null && (int) $panier['pharmacie_id'] !== $stock->pharmacie_id) {
            return back()->with('erreur', 'Votre panier contient déjà des articles d\'une autre pharmacie. Videz-le pour commander ici.');
        }

        $panier['pharmacie_id'] = $stock->pharmacie_id;

        $items = collect($panier['items']);
        $existante = $items->firstWhere('stock_id', $stock->id);

        if ($existante) {
            $nouvelle = min($existante['quantite'] + $quantite, $stock->quantite);
            $items = $items->map(fn ($i) => $i['stock_id'] === $stock->id ? ['stock_id' => $stock->id, 'quantite' => $nouvelle] : $i);
        } else {
            $items->push(['stock_id' => $stock->id, 'quantite' => $quantite]);
        }

        $panier['items'] = $items->values()->all();
        $request->session()->put('panier', $panier);

        return back()->with('succes', 'Ajouté au panier ✓');
    }

    public function modifier(Request $request): RedirectResponse
    {
        $request->validate([
            'stock_id' => ['required', 'integer'],
            'quantite' => ['required', 'integer', 'min:0'],
        ]);

        $panier = $request->session()->get('panier', ['pharmacie_id' => null, 'items' => []]);

        $panier['items'] = collect($panier['items'])
            ->map(function ($item) use ($request) {
                if ($item['stock_id'] !== (int) $request->stock_id) {
                    return $item;
                }

                $stock = PharmacieMedicament::find($item['stock_id']);
                $quantite = min((int) $request->quantite, $stock?->quantite ?? 0);

                return ['stock_id' => $item['stock_id'], 'quantite' => $quantite];
            })
            ->filter(fn ($item) => $item['quantite'] > 0)
            ->values()
            ->all();

        if (empty($panier['items'])) {
            $panier['pharmacie_id'] = null;
        }

        $request->session()->put('panier', $panier);

        return back()->with('succes', 'Panier mis à jour.');
    }

    public function supprimer(Request $request): RedirectResponse
    {
        $request->validate(['stock_id' => ['required', 'integer']]);

        $panier = $request->session()->get('panier', ['pharmacie_id' => null, 'items' => []]);
        $panier['items'] = array_values(array_filter($panier['items'], fn ($i) => $i['stock_id'] !== (int) $request->stock_id));

        if (empty($panier['items'])) {
            $panier['pharmacie_id'] = null;
        }

        $request->session()->put('panier', $panier);

        return back()->with('succes', 'Article retiré du panier.');
    }

    public function vider(Request $request): RedirectResponse
    {
        $request->session()->forget('panier');

        return back()->with('succes', 'Panier vidé.');
    }
}

<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Suivi des livraisons des commandes de la pharmacie. */
class LivraisonController extends Controller
{
    public function index(Request $request): View
    {
        $pharmacie = $request->user()->pharmacie;

        $commandes = Commande::query()
            ->where('pharmacie_id', $pharmacie->id)
            ->whereHas('livraison')
            ->with(['client.user', 'livraison.livreur.user'])
            ->latest()
            ->paginate(15);

        return view('pharmacie.livraisons', [
            'pharmacie' => $pharmacie,
            'commandes' => $commandes,
        ]);
    }
}

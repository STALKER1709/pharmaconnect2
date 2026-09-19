<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LivraisonController extends Controller
{
    /** Suivi des livraisons de la pharmacie. */
    public function index(Request $request): View
    {
        $pharmacie = $request->user()->pharmacie;

        $commandes = Commande::where('pharmacie_id', $pharmacie->id)
            ->whereHas('livraison')
            ->with(['livraison.livreur.user', 'client.user'])
            ->latest()
            ->paginate(15);

        return view('pharmacie.livraisons', compact('commandes'));
    }
}

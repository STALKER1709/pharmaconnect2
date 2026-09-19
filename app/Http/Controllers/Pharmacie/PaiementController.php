<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaiementController extends Controller
{
    /** Historique des paiements reçus (gérer ses paiements). */
    public function index(Request $request): View
    {
        $pharmacie = $request->user()->pharmacie;

        $paiements = \App\Models\Paiement::whereHas('commande', fn ($q) => $q->where('pharmacie_id', $pharmacie->id))
            ->with('commande.client.user')
            ->latest()
            ->paginate(15);

        $totalRecu = \App\Models\Paiement::whereHas('commande', fn ($q) => $q->where('pharmacie_id', $pharmacie->id))
            ->where('statut', 'reussi')
            ->sum('montant');

        $totalMois = \App\Models\Paiement::whereHas('commande', fn ($q) => $q->where('pharmacie_id', $pharmacie->id))
            ->where('statut', 'reussi')
            ->whereMonth('paye_at', today()->month)
            ->sum('montant');

        return view('pharmacie.paiements', [
            'paiements' => $paiements,
            'totalRecu' => $totalRecu,
            'totalMois' => $totalMois,
        ]);
    }
}

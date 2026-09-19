<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        $filtre = $request->query('statut');

        $commandes = Commande::query()
            ->with(['client.user', 'pharmacie.user', 'livreur.user', 'paiement'])
            ->when($filtre !== null && $filtre !== '', fn ($q) => $q->where('statut', $filtre))
            ->latest('date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.commandes', ['commandes' => $commandes, 'filtre' => $filtre]);
    }

    public function show(Commande $commande)
    {
        return view('admin.commande-detail', [
            'commande' => $commande->load(['client.user', 'pharmacie.user', 'livreur.user', 'lignes.medicament', 'paiement', 'livraison']),
        ]);
    }
}

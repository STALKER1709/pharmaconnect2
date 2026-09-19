<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Medicament;
use App\Models\Pharmacie;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $client = $request->user()->client()->firstOrCreate(['user_id' => $request->user()->id], ['ville' => 'Douala']);

        $commandesRecentes = Commande::where('client_id', $client->id)
            ->with(['pharmacie', 'livraison'])
            ->latest()
            ->take(5)
            ->get();

        return view('client.dashboard', [
            'client' => $client,
            'commandes' => $commandesRecentes,
            'nbCommandes' => Commande::where('client_id', $client->id)->count(),
            'suggestions' => Medicament::actif()->inRandomOrder()->take(4)->get(),
            'pharmaciesProches' => Pharmacie::where('statut', 'actif')->orderByDesc('note_moyenne')->take(3)->get(),
        ]);
    }
}

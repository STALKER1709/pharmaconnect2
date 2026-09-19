<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Livreur;
use App\Models\Pharmacie;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $caTotal = Commande::where('statut', 'livree')->sum('total');
        $caMois = Commande::where('statut', 'livree')->whereMonth('created_at', today()->month)->sum('total');

        // CA 7 jours pour Chart.js
        $caParJour = collect(range(6, 0))->map(fn ($i) => [
            'jour' => today()->subDays($i)->locale('fr')->translatedFormat('D d'),
            'total' => (int) Commande::whereIn('statut', ['confirmee', 'prete', 'assignee', 'en_livraison', 'livree'])
                ->whereDate('created_at', today()->subDays($i))->sum('total'),
        ]);

        return view('admin.dashboard', [
            'nbClients' => User::where('role', 'client')->count(),
            'nbPharmacies' => Pharmacie::where('statut', 'actif')->count(),
            'nbLivreurs' => Livreur::where('statut', 'actif')->count(),
            'enAttente' => User::where('statut', 'en_attente')->with(['pharmacie', 'livreur'])->orderBy('created_at')->get(),
            'nbCommandes' => Commande::count(),
            'caTotal' => $caTotal,
            'caMois' => $caMois,
            'caParJour' => $caParJour,
            'commandesRecentes' => Commande::with(['client.user', 'pharmacie'])->latest()->take(8)->get(),
        ]);
    }
}

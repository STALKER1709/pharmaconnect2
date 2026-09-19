<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\PharmacieMedicament;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $pharmacie = $request->user()->pharmacie;

        $commandes = Commande::where('pharmacie_id', $pharmacie->id)
            ->whereIn('statut', ['confirmee', 'prete', 'assignee', 'en_livraison', 'livree']);

        $caJour = (clone $commandes)->whereDay('created_at', today())->sum('total');
        $caMois = (clone $commandes)->whereMonth('created_at', today()->month)->sum('total');
        $nbEnAttente = Commande::where('pharmacie_id', $pharmacie->id)->where('statut', 'en_attente')->count();
        $nbLivrees = Commande::where('pharmacie_id', $pharmacie->id)->where('statut', 'livree')->count();

        // CA des 7 derniers jours (Chart.js)
        $caParJour = collect(range(6, 0))->map(function ($i) use ($pharmacie) {
            $jour = today()->subDays($i);

            return [
                'jour' => $jour->locale('fr')->translatedFormat('D d'),
                'total' => (int) Commande::where('pharmacie_id', $pharmacie->id)
                    ->whereDate('created_at', $jour)
                    ->whereIn('statut', ['confirmee', 'prete', 'assignee', 'en_livraison', 'livree'])
                    ->sum('total'),
            ];
        });

        // Top 5 médicaments vendus
        $topMedicaments = \App\Models\CommandeLigne::query()
            ->selectRaw('nom_medicament, SUM(quantite) AS total_vendus, SUM(sous_total) AS recette')
            ->whereIn('commande_id', Commande::where('pharmacie_id', $pharmacie->id)->select('id'))
            ->groupBy('nom_medicament')
            ->orderByDesc('total_vendus')
            ->take(5)
            ->get();

        // Alertes stock bas / péremption proche
        $stockBas = PharmacieMedicament::where('pharmacie_id', $pharmacie->id)
            ->whereRaw('quantite <= seuil_stock_bas')
            ->where('quantite', '>', 0)
            ->with('medicament')
            ->take(5)
            ->get();

        $peremption = PharmacieMedicament::where('pharmacie_id', $pharmacie->id)
            ->whereNotNull('date_peremption')
            ->whereBetween('date_peremption', [now(), now()->addMonths((int) config('app.mois_alerte_peremption', 3))])
            ->with('medicament')
            ->take(5)
            ->get();

        return view('pharmacie.dashboard', [
            'pharmacie' => $pharmacie,
            'caJour' => $caJour,
            'caMois' => $caMois,
            'nbEnAttente' => $nbEnAttente,
            'nbLivrees' => $nbLivrees,
            'caParJour' => $caParJour,
            'topMedicaments' => $topMedicaments,
            'stockBas' => $stockBas,
            'peremption' => $peremption,
        ]);
    }
}

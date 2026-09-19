<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeLigne;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatistiquesController extends Controller
{
    public function index(Request $request): View
    {
        $pharmacie = $request->user()->pharmacie;

        $base = Commande::where('pharmacie_id', $pharmacie->id)
            ->whereIn('statut', ['confirmee', 'prete', 'assignee', 'en_livraison', 'livree']);

        // CA par mois (12 derniers mois)
        $caParMois = collect(range(11, 0))->map(function ($i) use ($base) {
            $mois = today()->subMonths($i);

            return [
                'mois' => $mois->locale('fr')->translatedFormat('M y'),
                'total' => (int) (clone $base)->whereYear('created_at', $mois->year)->whereMonth('created_at', $mois->month)->sum('total'),
            ];
        });

        // Commandes par statut
        $parStatut = Commande::where('pharmacie_id', $pharmacie->id)
            ->selectRaw('statut, COUNT(*) AS nb')
            ->groupBy('statut')
            ->pluck('nb', 'statut');

        // Top 8 médicaments
        $topMedicaments = CommandeLigne::whereIn('commande_id', Commande::where('pharmacie_id', $pharmacie->id)->select('id'))
            ->selectRaw('nom_medicament, SUM(quantite) AS total_vendus, SUM(sous_total) AS recette')
            ->groupBy('nom_medicament')
            ->orderByDesc('total_vendus')
            ->take(8)
            ->get();

        // Note moyenne et avis
        $noteMoyenne = round((float) $pharmacie->avis()->avg('note'), 1);
        $nbAvis = $pharmacie->nb_avis;

        return view('pharmacie.statistiques', [
            'pharmacie' => $pharmacie,
            'caParMois' => $caParMois,
            'parStatut' => $parStatut,
            'topMedicaments' => $topMedicaments,
            'noteMoyenne' => $noteMoyenne,
            'nbAvis' => $nbAvis,
            'caTotal' => (clone $base)->sum('total'),
            'nbCommandes' => (clone $base)->count(),
            'panierMoyen' => (clone $base)->count() > 0 ? (int) round((clone $base)->sum('total') / (clone $base)->count()) : 0,
        ]);
    }
}

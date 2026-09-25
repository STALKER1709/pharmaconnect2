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

        $caJour = (clone $commandes)->whereDate('created_at', today())->sum('total');
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

        $valides = ['confirmee', 'prete', 'assignee', 'en_livraison', 'livree'];
        $base = fn () => Commande::where('pharmacie_id', $pharmacie->id);

        // Évolution du CA par rapport au mois précédent
        $caMoisPrecedent = (int) $base()->whereIn('statut', $valides)
            ->whereBetween('created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])
            ->sum('total');
        $evolutionCa = $caMoisPrecedent > 0 ? round(($caMois - $caMoisPrecedent) * 100 / $caMoisPrecedent, 1) : null;

        // CA cumulé par semaine du mois en cours (graphique en aire de la maquette)
        $caParSemaine = collect(range(1, 4))->map(function ($semaine) use ($base, $valides) {
            $debut = now()->startOfMonth()->addDays(($semaine - 1) * 7);
            $fin = $semaine === 4 ? now()->endOfMonth() : $debut->copy()->addDays(6)->endOfDay();

            return (int) $base()->whereIn('statut', $valides)->whereBetween('created_at', [$debut, $fin])->sum('total');
        });

        // Commandes par jour de la semaine en cours (lundi → dimanche)
        $commandesParJour = collect(range(0, 6))->map(fn ($i) => [
            'jour' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'][$i],
            'total' => $base()->whereDate('created_at', now()->startOfWeek()->addDays($i))->count(),
        ]);

        // Commandes à traiter (en attente de validation ou à préparer)
        $aTraiter = $base()->whereIn('statut', ['en_attente', 'confirmee'])
            ->with(['lignes', 'paiement', 'client.user'])
            ->orderByRaw("statut = 'en_attente' desc")
            ->oldest()
            ->take(3)
            ->get();

        // État du stock des médicaments les plus vendus
        $stocksParNom = PharmacieMedicament::where('pharmacie_id', $pharmacie->id)->with('medicament.categorie')->get()
            ->keyBy(fn ($s) => $s->medicament?->nom);

        return view('pharmacie.dashboard', [
            'evolutionCa' => $evolutionCa,
            'caParSemaine' => $caParSemaine,
            'commandesParJour' => $commandesParJour,
            'aTraiter' => $aTraiter,
            'nbATraiter' => $base()->whereIn('statut', ['en_attente', 'confirmee'])->count(),
            'nbCommandesMois' => $base()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'nbCommandesSemaine' => $base()->where('created_at', '>=', now()->startOfWeek())->count(),
            'nbStockBas' => PharmacieMedicament::where('pharmacie_id', $pharmacie->id)->whereRaw('quantite <= seuil_stock_bas')->count(),
            'stocksParNom' => $stocksParNom,
            'nbLivreursDisponibles' => \App\Models\Livreur::where('statut', 'actif')->where('disponibilite', 'disponible')->count(),
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

<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Enums\StatutCommande;
use App\Enums\StatutPaiement;
use App\Models\Commande;
use App\Models\Paiement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        $pharmacieId = Auth::user()->pharmacie->id;

        // CA des 14 derniers jours
        $caParJour = Commande::query()
            ->where('pharmacie_id', $pharmacieId)
            ->whereNotIn('statut', [StatutCommande::EnAttente, StatutCommande::Annulee])
            ->whereBetween('date', [now()->subDays(13)->startOfDay(), now()->endOfDay()])
            ->selectRaw("DATE(date) as jour, SUM(montant_total + frais_livraison) as total")
            ->groupBy('jour')
            ->orderBy('jour')
            ->get()
            ->keyBy('jour');

        $jours = collect();
        $caLabels = [];
        $caData = [];
        for ($i = 13; $i >= 0; $i--) {
            $jour = now()->subDays($i)->toDateString();
            $caLabels[] = now()->subDays($i)->format('d/m');
            $caData[] = (int) ($caParJour[$jour]->total ?? 0);
        }

        // Commandes par statut
        $parStatut = Commande::query()
            ->where('pharmacie_id', $pharmacieId)
            ->selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // Top 5 médicaments vendus
        $topMedicaments = DB::table('ligne_commandes')
            ->join('commandes', 'ligne_commandes.commande_id', '=', 'commandes.id')
            ->join('medicaments', 'ligne_commandes.medicament_id', '=', 'medicaments.id')
            ->where('commandes.pharmacie_id', $pharmacieId)
            ->whereNotIn('commandes.statut', [StatutCommande::EnAttente, StatutCommande::Annulee])
            ->selectRaw('medicaments.nom, SUM(ligne_commandes.quantite) as total_vendu, SUM(ligne_commandes.quantite * ligne_commandes.prix_unitaire) as ca')
            ->groupBy('medicaments.nom')
            ->orderByDesc('total_vendu')
            ->take(5)
            ->get();

        $stats = [
            'ca_total' => (int) Paiement::query()
                ->whereHas('commande', fn ($q) => $q->where('pharmacie_id', $pharmacieId))
                ->where('statut', StatutPaiement::Valide)
                ->sum('montant'),
            'commandes_total' => Commande::query()->where('pharmacie_id', $pharmacieId)->count(),
            'commandes_livrees' => Commande::query()->where('pharmacie_id', $pharmacieId)->where('statut', StatutCommande::Livree)->count(),
            'panier_moyen' => (int) (Commande::query()
                ->where('pharmacie_id', $pharmacieId)
                ->whereNotIn('statut', [StatutCommande::EnAttente, StatutCommande::Annulee])
                ->avg(DB::raw('montant_total + frais_livraison')) ?? 0),
        ];

        return view('pharmacie.statistiques', [
            'stats' => $stats,
            'caLabels' => $caLabels,
            'caData' => $caData,
            'parStatut' => $parStatut,
            'topMedicaments' => $topMedicaments,
        ]);
    }
}

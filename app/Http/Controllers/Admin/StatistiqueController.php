<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\Role;
use App\Enums\StatutCommande;
use App\Models\Commande;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        // CA des 14 derniers jours (paiements validés)
        $paiements = DB::table('paiements')
            ->where('statut', 'VALIDE')
            ->whereBetween('created_at', [now()->subDays(13)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as jour, SUM(montant) as total')
            ->groupBy('jour')
            ->get()
            ->keyBy('jour');

        $caLabels = [];
        $caData = [];
        for ($i = 13; $i >= 0; $i--) {
            $jour = now()->subDays($i)->toDateString();
            $caLabels[] = now()->subDays($i)->format('d/m');
            $caData[] = (int) ($paiements[$jour]->total ?? 0);
        }

        $parStatut = Commande::query()
            ->selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $parRole = User::query()
            ->selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $topPharmacies = DB::table('commandes')
            ->join('pharmacies', 'commandes.pharmacie_id', '=', 'pharmacies.id')
            ->whereNotIn('commandes.statut', [StatutCommande::EnAttente->value, StatutCommande::Annulee->value])
            ->selectRaw('pharmacies.nom_commercial, COUNT(*) as nb, SUM(commandes.montant_total) as ca')
            ->groupBy('pharmacies.nom_commercial')
            ->orderByDesc('ca')
            ->take(5)
            ->get();

        return view('admin.statistiques', [
            'caLabels' => $caLabels,
            'caData' => $caData,
            'parStatut' => $parStatut,
            'parRole' => $parRole,
            'topPharmacies' => $topPharmacies,
            'totalClients' => User::ofRole(Role::Client)->count(),
            'totalPharmacies' => User::ofRole(Role::Pharmacie)->count(),
            'totalLivreurs' => User::ofRole(Role::Livreur)->count(),
        ]);
    }
}

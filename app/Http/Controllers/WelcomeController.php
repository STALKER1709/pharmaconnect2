<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Medicament;
use App\Models\Pharmacie;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $recherche = trim((string) $request->query('q', ''));
        $categorieId = $request->query('categorie');

        $pharmacies = Pharmacie::query()
            ->where('statut', 'actif')
            ->with('user')
            ->when($recherche !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('nom', 'like', "%{$recherche}%")
                ->orWhere('quartier', 'like', "%{$recherche}%")
                ->orWhere('ville', 'like', "%{$recherche}%")))
            ->latest()
            ->take(6)
            ->get();

        $medicaments = Medicament::query()
            ->actif()
            ->with('categorie')
            ->when($recherche !== '', fn ($q) => $q->where('nom', 'like', "%{$recherche}%"))
            ->when($categorieId, fn ($q) => $q->where('categorie_id', $categorieId))
            ->latest()
            ->take(8)
            ->get();

        return view('accueil', [
            'recherche' => $recherche,
            'categories' => Categorie::orderBy('nom')->get(),
            'pharmacies' => $pharmacies,
            'medicaments' => $medicaments,
            'nbPharmacies' => Pharmacie::where('statut', 'actif')->count(),
            'nbMedicaments' => Medicament::actif()->count(),
            'nbCommandes' => \App\Models\Commande::count(),
        ]);
    }
}

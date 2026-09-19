<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Pharmacie;
use Illuminate\Http\Request;

class RechercheController extends Controller
{
    /**
     * Recherche publique : médicaments (avec pharmacies qui les détiennent)
     * et pharmacies. Accessible aux visiteurs.
     */
    public function index(Request $request)
    {
        $terme = trim((string) $request->query('q', ''));
        $filtreOrdonnance = $request->query('ordonnance');
        $prixMin = $request->query('prix_min');
        $prixMax = $request->query('prix_max');

        $medicaments = Medicament::query()
            ->disponible()
            ->dansPharmaciesDisponibles()
            ->with(['pharmacie.user'])
            ->recherche($terme)
            ->surOrdonnance($filtreOrdonnance !== null ? filter_var($filtreOrdonnance, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null)
            ->prixDans($prixMin !== null && $prixMin !== '' ? (float) $prixMin : null, $prixMax !== null && $prixMax !== '' ? (float) $prixMax : null)
            ->orderBy('nom')
            ->paginate(12)
            ->withQueryString();

        $pharmacies = Pharmacie::query()
            ->whereHas('user', fn ($q) => $q->actifs())
            ->with('user')
            ->when($terme !== '', fn ($q) => $q->where(fn ($qq) => $qq
                ->where('nom_commercial', 'like', "%{$terme}%")
                ->orWhere('adresse', 'like', "%{$terme}%")))
            ->take(6)
            ->get();

        return view('recherche', compact('medicaments', 'pharmacies', 'terme', 'filtreOrdonnance', 'prixMin', 'prixMax'));
    }
}

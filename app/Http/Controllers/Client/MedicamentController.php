<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Medicament;
use App\Models\Pharmacie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicamentController extends Controller
{
    /** Recherche de médicaments : nom, filtres, disponibilité, prix, distance. */
    public function index(Request $request)
    {
        $terme = trim((string) $request->query('q', ''));
        $ordonnance = $request->query('ordonnance');
        $prixMin = $request->query('prix_min');
        $prixMax = $request->query('prix_max');
        $pharmacieId = $request->query('pharmacie_id');
        $triDistance = $request->boolean('distance');
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        $query = Medicament::query()
            ->disponible()
            ->dansPharmaciesDisponibles()
            ->with(['pharmacie.user', 'avis'])
            ->recherche($terme)
            ->surOrdonnance($ordonnance !== null && $ordonnance !== '' ? filter_var($ordonnance, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null)
            ->prixDans($prixMin !== null && $prixMin !== '' ? (float) $prixMin : null, $prixMax !== null && $prixMax !== '' ? (float) $prixMax : null);

        if ($pharmacieId !== null && $pharmacieId !== '') {
            $query->where('pharmacie_id', (int) $pharmacieId);
        }

        // Tri par distance (Haversine SQL) si position du client fournie
        if ($triDistance && is_numeric($lat) && is_numeric($lng)) {
            $expression = expression_haversine_sql('pharmacies.latitude', 'pharmacies.longitude', (float) $lat, (float) $lng);
            $query->join('pharmacies', 'medicaments.pharmacie_id', '=', 'pharmacies.id')
                ->select('medicaments.*', DB::raw("{$expression} AS distance_km"))
                ->orderBy('distance_km');
        } else {
            $query->orderBy('nom');
        }

        $medicaments = $query->paginate(12)->withQueryString();

        $pharmacies = Pharmacie::query()
            ->whereHas('user', fn ($q) => $q->actifs())
            ->orderBy('nom_commercial')
            ->get();

        return view('client.medicaments', [
            'medicaments' => $medicaments,
            'pharmacies' => $pharmacies,
            'terme' => $terme,
            'ordonnance' => $ordonnance,
            'prixMin' => $prixMin,
            'prixMax' => $prixMax,
            'pharmacieId' => $pharmacieId,
            'triDistance' => $triDistance,
        ]);
    }

    /** Fiche détaillée + pharmacies qui ont le médicament en stock. */
    public function show(Medicament $medicament)
    {
        // Toutes les pharmacies actives proposant le même nom de médicament
        $equivalents = Medicament::query()
            ->disponible()
            ->dansPharmaciesDisponibles()
            ->with(['pharmacie.user', 'avis'])
            ->where('nom', $medicament->nom)
            ->orderBy('prix')
            ->get();

        return view('client.medicament-fiche', [
            'medicament' => $medicament->load(['pharmacie.user', 'avis.client.user']),
            'equivalents' => $equivalents,
            'noteMoyenne' => (float) $medicament->avis()->avg('note'),
            'nbAvis' => $medicament->avis()->count(),
        ]);
    }
}

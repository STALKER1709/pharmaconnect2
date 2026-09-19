<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Pharmacie;
use Illuminate\Http\Request;

class PharmacieController extends Controller
{
    /** Liste des pharmacies : distance depuis la position du client si fournie. */
    public function index(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $terme = trim((string) $request->query('q', ''));

        $query = Pharmacie::query()
            ->whereHas('user', fn ($q) => $q->actifs())
            ->with('user')
            ->withCount(['medicaments' => fn ($q) => $q->disponible()])
            ->when($terme !== '', fn ($q) => $q->where(fn ($qq) => $qq
                ->where('nom_commercial', 'like', "%{$terme}%")
                ->orWhere('adresse', 'like', "%{$terme}%")));

        if (is_numeric($lat) && is_numeric($lng)) {
            $expression = expression_haversine_sql('pharmacies.latitude', 'pharmacies.longitude', (float) $lat, (float) $lng);
            $query->select('pharmacies.*')
                ->selectRaw("{$expression} AS distance_km")
                ->orderBy('distance_km');
        } else {
            $query->orderBy('nom_commercial');
        }

        return view('client.pharmacies', [
            'pharmacies' => $query->paginate(9)->withQueryString(),
            'terme' => $terme,
            'lat' => $lat,
            'lng' => $lng,
        ]);
    }

    /** Fiche pharmacie : horaires, ouvert/fermé, carte, avis, médicaments. */
    public function show(Pharmacie $pharmacie)
    {
        $medicaments = $pharmacie->medicaments()
            ->disponible()
            ->orderBy('nom')
            ->take(12)
            ->get();

        $avis = $pharmacie->avis()
            ->with('client.user')
            ->latest()
            ->take(10)
            ->get();

        return view('client.pharmacie-fiche', [
            'pharmacie' => $pharmacie->load('user'),
            'medicaments' => $medicaments,
            'avis' => $avis,
            'noteMoyenne' => (float) $pharmacie->avis()->avg('note'),
            'nbAvis' => $pharmacie->avis()->count(),
            'ouverte' => $pharmacie->estOuverteSelonHoraires(),
        ]);
    }
}

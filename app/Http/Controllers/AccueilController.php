<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Pharmacie;

class AccueilController extends Controller
{
    public function index()
    {
        $stats = [
            'pharmacies' => Pharmacie::query()->whereHas('user', fn ($q) => $q->actifs())->count(),
            'medicaments' => Medicament::query()->disponible()->count(),
            'livreurs' => \App\Models\Livreur::count(),
        ];

        $medicamentsRecents = Medicament::query()
            ->disponible()
            ->with('pharmacie')
            ->latest()
            ->take(8)
            ->get();

        $pharmaciesOuvertes = Pharmacie::query()
            ->whereHas('user', fn ($q) => $q->actifs())
            ->with('user')
            ->take(3)
            ->get();

        return view('accueil', compact('stats', 'medicamentsRecents', 'pharmaciesOuvertes'));
    }
}

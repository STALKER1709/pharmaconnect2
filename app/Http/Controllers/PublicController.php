<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Medicament;
use App\Models\Pharmacie;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    /** Liste/recherche publique des pharmacies. */
    public function pharmacies(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $pharmacies = Pharmacie::query()
            ->where('statut', 'actif')
            ->with('user')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('nom', 'like', "%{$q}%")
                ->orWhere('quartier', 'like', "%{$q}%")
                ->orWhere('ville', 'like', "%{$q}%")))
            ->orderBy('note_moyenne', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('public.pharmacies', compact('pharmacies', 'q'));
    }

    /** Fiche publique d'une pharmacie : horaires, carte, avis, catalogue. */
    public function pharmacie(Pharmacie $pharmacie): View
    {
        abort_unless($pharmacie->statut === 'actif', 404);

        $pharmacie->load(['user', 'horaires', 'avis' => fn ($q) => $q->latest()->take(5), 'avis.client.user']);

        $medicaments = $pharmacie->stocks()
            ->where('quantite', '>', 0)
            ->with('medicament.categorie')
            ->paginate(12);

        return view('public.pharmacie', [
            'pharmacie' => $pharmacie,
            'stocks' => $medicaments,
            'jours' => \App\Models\Horaire::jours(),
            'noteMoyenne' => round((float) $pharmacie->avis()->avg('note'), 1),
        ]);
    }

    /** Recherche publique de médicaments + pharmacies qui les ont en stock. */
    public function medicaments(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $categorieId = $request->query('categorie');
        $ordonnance = $request->query('ordonnance');

        $medicaments = Medicament::query()
            ->actif()
            ->with('categorie')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('nom', 'like', "%{$q}%")
                ->orWhere('fabricant', 'like', "%{$q}%")
                ->orWhere('reference', 'like', "%{$q}%")))
            ->when($categorieId, fn ($query) => $query->where('categorie_id', $categorieId))
            ->when($ordonnance !== null && $ordonnance !== '', fn ($query) => $query->where('ordonnance_obligatoire', $ordonnance === '1'))
            ->orderBy('nom')
            ->paginate(12)
            ->withQueryString();

        return view('public.medicaments', [
            'medicaments' => $medicaments,
            'categories' => Categorie::orderBy('nom')->get(),
            'q' => $q,
            'categorieId' => $categorieId,
            'ordonnance' => $ordonnance,
        ]);
    }

    /** Fiche médicament + pharmacies qui l'ont en stock avec prix (FCFA). */
    public function medicament(Medicament $medicament): View
    {
        abort_unless($medicament->actif, 404);

        $stocks = $medicament->pharmacies()
            ->where('pharmacies.statut', 'actif')
            ->withPivot(['quantite', 'prix', 'date_peremption', 'seuil_stock_bas', 'pharmacie_id', 'medicament_id'])
            ->get()
            ->map(function ($pharmacie) {
                $pivot = $pharmacie->pivot;
                $pivot->est_en_stock = $pivot->quantite > 0
                    && ($pivot->date_peremption === null || \Illuminate\Support\Carbon::parse($pivot->date_peremption)->isFuture());

                return $pharmacie;
            })
            ->filter(fn ($p) => $p->pivot->est_en_stock)
            ->sortBy(fn ($p) => $p->pivot->prix)
            ->values();

        return view('public.medicament', [
            'medicament' => $medicament->load('categorie'),
            'stocks' => $stocks,
            'noteMoyenne' => $medicament->noteMoyenne(),
        ]);
    }
}

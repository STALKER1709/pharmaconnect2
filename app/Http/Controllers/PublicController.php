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

        $pharmacie->load(['user', 'horaires']);

        $stocks = $pharmacie->stocks()
            ->where('quantite', '>', 0)
            ->where(fn ($d) => $d->whereNull('date_peremption')->orWhereDate('date_peremption', '>', now()))
            ->whereHas('medicament', fn ($m) => $m->actif())
            ->with('medicament.categorie')
            ->get()
            ->sortBy(fn ($s) => $s->medicament->nom)
            ->values();

        $avis = $pharmacie->avis()->with('client.user')->latest()->get();
        $repartition = collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($n) => [
            $n => $avis->isEmpty() ? 0 : (int) round($avis->where('note', $n)->count() * 100 / $avis->count()),
        ]);

        return view('public.pharmacie', [
            'pharmacie' => $pharmacie,
            'stocks' => $stocks,
            'jours' => \App\Models\Horaire::jours(),
            'avis' => $avis->take(4),
            'nbAvis' => $avis->count(),
            'repartition' => $repartition,
            'noteMoyenne' => round((float) $avis->avg('note'), 1),
        ]);
    }

    /** Recherche publique de médicaments + pharmacies qui les ont en stock. */
    public function medicaments(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $ordonnance = $request->query('ordonnance');
        $quartier = trim((string) $request->query('quartier', ''));
        $forme = trim((string) $request->query('forme', ''));
        $enStock = $request->boolean('en_stock');
        $prixMax = (int) $request->query('prix_max', 0);
        $tri = (string) $request->query('tri', 'pertinence');

        // Catégories : sélection multiple (categories[]) ou unique (categorie) pour compatibilité
        $categoriesIds = collect((array) $request->query('categories', []))
            ->push($request->query('categorie'))
            ->filter()->map(fn ($id) => (int) $id)->unique()->values();

        $enStockActif = fn ($s) => $s->where('quantite', '>', 0)
            ->where(fn ($d) => $d->whereNull('date_peremption')->orWhereDate('date_peremption', '>', now()))
            ->whereHas('pharmacie', fn ($p) => $p->where('statut', 'actif'));

        $medicaments = Medicament::query()
            ->actif()
            ->with(['categorie', 'stocks.pharmacie'])
            ->withMin(['stocks as prix_min' => $enStockActif], 'prix')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('nom', 'like', "%{$q}%")
                ->orWhere('fabricant', 'like', "%{$q}%")
                ->orWhere('reference', 'like', "%{$q}%")))
            ->when($categoriesIds->isNotEmpty(), fn ($query) => $query->whereIn('categorie_id', $categoriesIds))
            ->when($ordonnance !== null && $ordonnance !== '', fn ($query) => $query->where('ordonnance_obligatoire', $ordonnance === '1'))
            ->when($forme !== '', fn ($query) => $query->where('forme', 'like', "{$forme}%"))
            ->when($enStock, fn ($query) => $query->whereHas('stocks', $enStockActif))
            ->when($prixMax > 0, fn ($query) => $query->whereHas('stocks', fn ($s) => $enStockActif($s)->where('prix', '<=', $prixMax)))
            ->when($quartier !== '', fn ($query) => $query->whereHas('stocks', fn ($s) => $enStockActif($s)
                ->whereHas('pharmacie', fn ($p) => $p->where('quartier', $quartier))))
            ->when($tri === 'prix_asc', fn ($query) => $query->orderByRaw('prix_min is null')->orderBy('prix_min'))
            ->when($tri === 'prix_desc', fn ($query) => $query->orderByDesc('prix_min'))
            ->orderBy('nom')
            ->paginate(9)
            ->withQueryString();

        $bornesPrix = \App\Models\PharmacieMedicament::query()
            ->where('quantite', '>', 0)
            ->selectRaw('min(prix) as min, max(prix) as max')
            ->first();

        return view('public.medicaments', [
            'medicaments' => $medicaments,
            'categories' => Categorie::withCount(['medicaments' => fn ($m) => $m->actif()])->orderBy('nom')->get(),
            'formes' => Medicament::actif()->whereNotNull('forme')->distinct()->orderBy('forme')->pluck('forme'),
            'nbPharmacies' => Pharmacie::where('statut', 'actif')->count(),
            'quartiers' => Pharmacie::where('statut', 'actif')->whereNotNull('quartier')->distinct()->orderBy('quartier')->pluck('quartier'),
            'prixMinGlobal' => (int) ($bornesPrix->min ?? 0),
            'prixMaxGlobal' => (int) ($bornesPrix->max ?? 0),
            'q' => $q,
            'categoriesIds' => $categoriesIds,
            'categorieId' => $categoriesIds->first(),
            'ordonnance' => $ordonnance,
            'forme' => $forme,
            'enStock' => $enStock,
            'prixMax' => $prixMax,
            'tri' => $tri,
            'quartier' => $quartier,
        ]);
    }

    /** Fiche médicament + pharmacies qui l'ont en stock avec prix (FCFA). */
    public function medicament(Medicament $medicament): View
    {
        abort_unless($medicament->actif, 404);

        // Lignes de stock des pharmacies actives, en stock et non périmées, du moins cher au plus cher
        $stocks = $medicament->stocks()
            ->with(['pharmacie.user', 'pharmacie.horaires'])
            ->whereHas('pharmacie', fn ($p) => $p->where('statut', 'actif'))
            ->where('quantite', '>', 0)
            ->where(fn ($d) => $d->whereNull('date_peremption')->orWhereDate('date_peremption', '>', now()))
            ->orderBy('prix')
            ->get();

        $avis = $medicament->avis()->with(['client.user', 'pharmacie'])->latest()->get();
        $repartition = collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($n) => [
            $n => $avis->isEmpty() ? 0 : (int) round($avis->where('note', $n)->count() * 100 / $avis->count()),
        ]);

        return view('public.medicament', [
            'medicament' => $medicament->load('categorie'),
            'stocks' => $stocks,
            'offre' => $stocks->first(),
            'avis' => $avis->take(3),
            'nbAvis' => $avis->count(),
            'repartition' => $repartition,
            'noteMoyenne' => $medicament->noteMoyenne(),
        ]);
    }
}

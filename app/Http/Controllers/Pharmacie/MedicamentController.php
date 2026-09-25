<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Medicament;
use App\Models\PharmacieMedicament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MedicamentController extends Controller
{
    /** Catalogue + stock de la pharmacie. */
    public function index(Request $request): View
    {
        $pharmacie = $request->user()->pharmacie;

        $stocks = $pharmacie->stocks()->with(['medicament.categorie'])
            ->when($request->query('q'), fn ($q, $v) => $q->whereHas('medicament', fn ($m) => $m->where('nom', 'like', "%{$v}%")))
            // Filtre « Stocks » de la barre latérale : stocks sous le seuil d'alerte
            ->when($request->query('stock') === 'bas', fn ($q) => $q->whereRaw('quantite <= seuil_stock_bas'))
            ->orderBy('quantite')
            ->paginate(15)
            ->withQueryString();

        return view('pharmacie.medicaments', [
            'pharmacie' => $pharmacie,
            'stocks' => $stocks,
            'categories' => Categorie::orderBy('nom')->get(),
            'q' => $request->query('q', ''),
            'filtreStock' => $request->query('stock'),
            'nbReferences' => $pharmacie->stocks()->count(),
            'nbStockBas' => $pharmacie->stocks()->whereRaw('quantite <= seuil_stock_bas')->count(),
            'valeurStock' => (int) $pharmacie->stocks()->selectRaw('SUM(quantite * prix) as v')->value('v'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pharmacie = $request->user()->pharmacie;

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'categorie_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'posologie' => ['nullable', 'string'],
            'ordonnance_obligatoire' => ['nullable', 'boolean'],
            'fabricant' => ['nullable', 'string', 'max:255'],
            'forme' => ['nullable', 'string', 'max:50'],
            'dosage_mg' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'quantite' => ['required', 'integer', 'min:0'],
            'prix' => ['required', 'integer', 'min:0'],
            'date_peremption' => ['nullable', 'date', 'after:today'],
            'seuil_stock_bas' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $medicament = DB::transaction(function () use ($request, $pharmacie, $validated) {
            $data = collect($validated)->except(['quantite', 'prix', 'date_peremption', 'seuil_stock_bas', 'photo'])->all();
            $data['slug'] = \Illuminate\Support\Str::slug($validated['nom']).'-'.strtolower(\Illuminate\Support\Str::random(5));
            $data['ordonnance_obligatoire'] = $request->boolean('ordonnance_obligatoire');
            $data['actif'] = true;

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('medicaments', 'public');
            }

            $medicament = Medicament::create($data);

            $pharmacie->stocks()->create([
                'medicament_id' => $medicament->id,
                'quantite' => $validated['quantite'],
                'prix' => $validated['prix'],
                'date_peremption' => $validated['date_peremption'] ?? null,
                'seuil_stock_bas' => $validated['seuil_stock_bas'] ?? 5,
            ]);

            return $medicament;
        });

        return back()->with('succes', "Médicament « {$medicament->nom} » ajouté au catalogue.");
    }

    public function update(Request $request, PharmacieMedicament $stock): RedirectResponse
    {
        $pharmacie = $request->user()->pharmacie;
        abort_unless($stock->pharmacie_id === $pharmacie->id, 403);

        $validated = $request->validate([
            'quantite' => ['required', 'integer', 'min:0'],
            'prix' => ['required', 'integer', 'min:0'],
            'date_peremption' => ['nullable', 'date'],
            'seuil_stock_bas' => ['nullable', 'integer', 'min:0', 'max:100'],
            'actif' => ['nullable', 'boolean'],
        ]);

        $stock->update([
            'quantite' => $validated['quantite'],
            'prix' => $validated['prix'],
            'date_peremption' => $validated['date_peremption'] ?? null,
            'seuil_stock_bas' => $validated['seuil_stock_bas'] ?? $stock->seuil_stock_bas,
        ]);

        $stock->medicament->update(['actif' => $request->boolean('actif', true)]);

        return back()->with('succes', 'Stock mis à jour.');
    }

    public function destroy(Request $request, PharmacieMedicament $stock): RedirectResponse
    {
        abort_unless($stock->pharmacie_id === $request->user()->pharmacie->id, 403);

        $stock->delete();

        return back()->with('succes', 'Médicament retiré du catalogue.');
    }
}

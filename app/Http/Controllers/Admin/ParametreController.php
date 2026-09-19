<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ParametreController extends Controller
{
    public function index(): View
    {
        return view('admin.parametres', [
            'categories' => Categorie::withCount('medicaments')->orderBy('nom')->get(),
        ]);
    }

    public function storeCategorie(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:120', 'unique:categories,nom'],
            'icone' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Categorie::create([
            'nom' => $validated['nom'],
            'slug' => Str::slug($validated['nom']),
            'icone' => $validated['icone'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('succes', 'Catégorie créée.');
    }

    public function destroyCategorie(Categorie $categorie): RedirectResponse
    {
        $categorie->medicaments()->update(['categorie_id' => null]);
        $categorie->delete();

        return back()->with('succes', 'Catégorie supprimée (médicaments conservés).');
    }
}

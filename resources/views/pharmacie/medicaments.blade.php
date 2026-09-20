@extends('layouts.app')

@section('titre', 'Mon catalogue')

@section('contenu')
<div class="space-y-6"
     x-data="{ formulaire: {{ $errors->any() ? 'true' : 'false' }} }">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">💊 Mon catalogue</h1>
        <button @click="formulaire = !formulaire" class="btn-primary">+ Ajouter un médicament</button>
    </div>

    {{-- Formulaire de création --}}
    <form x-show="formulaire" x-cloak action="{{ route('pharmacie.medicaments.store') }}" method="POST" enctype="multipart/form-data" class="card grid gap-4 p-5 sm:grid-cols-2">
        @csrf
        <div class="sm:col-span-2">
            <h2 class="font-bold">Nouveau médicament</h2>
        </div>
        <div>
            <label class="label">Nom *</label>
            <input name="nom" required class="input" value="{{ old('nom') }}">
        </div>
        <div>
            <label class="label">Catégorie</label>
            <select name="categorie_id" class="input">
                <option value="">—</option>
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->id }}" @selected(old('categorie_id') == $categorie->id)>{{ $categorie->nom }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Prix (FCFA) *</label>
            <input type="number" name="prix" required min="0" class="input" value="{{ old('prix') }}">
        </div>
        <div>
            <label class="label">Quantité en stock *</label>
            <input type="number" name="quantite" required min="0" class="input" value="{{ old('quantite', 10) }}">
        </div>
        <div>
            <label class="label">Forme</label>
            <input name="forme" class="input" placeholder="comprimé, sirop…" value="{{ old('forme') }}">
        </div>
        <div>
            <label class="label">Fabricant</label>
            <input name="fabricant" class="input" value="{{ old('fabricant') }}">
        </div>
        <div>
            <label class="label">Date de péremption</label>
            <input type="date" name="date_peremption" class="input" value="{{ old('date_peremption') }}">
        </div>
        <div>
            <label class="label">Seuil d'alerte stock bas</label>
            <input type="number" name="seuil_stock_bas" min="0" class="input" value="{{ old('seuil_stock_bas', 5) }}">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Description</label>
            <textarea name="description" rows="2" class="input">{{ old('description') }}</textarea>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="ordonnance_obligatoire" value="1" class="h-4 w-4 rounded text-menthe-600" @checked(old('ordonnance_obligatoire'))>
            Ordonnance obligatoire
        </label>
        <div class="sm:col-span-2">
            <button class="btn-primary">Enregistrer</button>
        </div>
    </form>

    {{-- Liste du stock --}}
    <div class="card overflow-x-auto">
        <table class="w-full min-w-[720px] text-sm">
            <thead class="bg-menthe-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Médicament</th>
                    <th class="px-4 py-3">Prix</th>
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Péremption</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-menthe-50">
                @forelse($stocks as $stock)
                    <tr class="hover:bg-menthe-50/40">
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $stock->medicament->nom }}</div>
                            <div class="text-xs text-slate-400">{{ $stock->medicament->forme }} {{ $stock->medicament->ordonnance_obligatoire ? '· ordonnance' : '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('pharmacie.stocks.update', $stock) }}" method="POST" class="flex items-center gap-1" x-data>
                                @csrf
                                @method('PUT')
                                <input type="number" name="prix" value="{{ $stock->prix }}" min="0" class="input w-24">
                        </td>
                        <td class="px-4 py-3">
                                <input type="number" name="quantite" value="{{ $stock->quantite }}" min="0" class="input w-20">
                        </td>
                        <td class="px-4 py-3">
                                <input type="date" name="date_peremption" value="{{ $stock->date_peremption?->format('Y-m-d') }}" class="input w-36">
                                <button class="btn-secondary mt-1 text-xs">💾 Enregistrer</button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($stock->stockBas())
                                <span class="badge bg-red-100 text-red-700">Stock bas</span>
                            @endif
                            @if($stock->peremptionProche())
                                <span class="badge bg-amber-100 text-amber-700">Péremption</span>
                            @endif
                            <form action="{{ route('pharmacie.stocks.destroy', $stock) }}" method="POST" class="mt-1" onsubmit="return confirm('Retirer du catalogue ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-danger text-xs">Retirer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Catalogue vide — ajoutez votre premier médicament.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $stocks->links() }}
</div>
@endsection

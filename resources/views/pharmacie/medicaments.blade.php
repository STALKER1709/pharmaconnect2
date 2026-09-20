@extends('layouts.app')

@section('titre', 'Mon catalogue')

@section('contenu')
<div x-data="{ formulaire: {{ $errors->any() ? 'true' : 'false' }} }">

    <div class="rangee-entre mb-6">
        <h1 class="titre-page">💊 Mon catalogue</h1>
        <button type="button" @click="formulaire = !formulaire" class="btn btn-primaire">+ Ajouter un médicament</button>
    </div>

    {{-- Formulaire de création --}}
    <form x-show="formulaire" x-cloak action="{{ route('pharmacie.medicaments.store') }}" method="POST" enctype="multipart/form-data"
          class="carte carte-corps mb-6" style="display:grid; gap:16px; grid-template-columns:1fr 1fr;">
        @csrf
        <h2 class="carte-titre" style="grid-column:1/-1; font-size:16px;">Nouveau médicament</h2>
        <div>
            <label class="champ-label">Nom *</label>
            <input name="nom" required class="champ" value="{{ old('nom') }}">
        </div>
        <div>
            <label class="champ-label">Catégorie</label>
            <select name="categorie_id" class="champ">
                <option value="">—</option>
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->id }}" @selected(old('categorie_id') == $categorie->id)>{{ $categorie->nom }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="champ-label">Prix (FCFA) *</label>
            <input type="number" name="prix" required min="0" class="champ" value="{{ old('prix') }}">
        </div>
        <div>
            <label class="champ-label">Quantité en stock *</label>
            <input type="number" name="quantite" required min="0" class="champ" value="{{ old('quantite', 10) }}">
        </div>
        <div>
            <label class="champ-label">Forme</label>
            <input name="forme" class="champ" placeholder="comprimé, sirop…" value="{{ old('forme') }}">
        </div>
        <div>
            <label class="champ-label">Fabricant</label>
            <input name="fabricant" class="champ" value="{{ old('fabricant') }}">
        </div>
        <div>
            <label class="champ-label">Date de péremption</label>
            <input type="date" name="date_peremption" class="champ" value="{{ old('date_peremption') }}">
        </div>
        <div>
            <label class="champ-label">Seuil d'alerte stock bas</label>
            <input type="number" name="seuil_stock_bas" min="0" class="champ" value="{{ old('seuil_stock_bas', 5) }}">
        </div>
        <div style="grid-column:1/-1;">
            <label class="champ-label">Description</label>
            <textarea name="description" rows="2" class="champ">{{ old('description') }}</textarea>
        </div>
        <label class="rangée texte-petit" style="gap:8px; grid-column:1/-1;">
            <input type="checkbox" name="ordonnance_obligatoire" value="1" class="case-a-cocher" @checked(old('ordonnance_obligatoire'))>
            Ordonnance obligatoire
        </label>
        <div style="grid-column:1/-1;">
            <button class="btn btn-primaire">Enregistrer</button>
        </div>
    </form>

    {{-- Liste du stock --}}
    <div class="carte tableau-scroll">
        <table class="tableau" style="min-width:760px;">
            <thead>
                <tr>
                    <th>Médicament</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Péremption</th>
                    <th class="texte-droit">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr>
                        <td>
                            <div style="font-weight:600; color:var(--encre);">{{ $stock->medicament->nom }}</div>
                            <div class="texte-petit texte-doux">{{ $stock->medicament->forme }} {{ $stock->medicament->ordonnance_obligatoire ? '· ordonnance' : '' }}</div>
                        </td>
                        <td colspan="3">
                            <form action="{{ route('pharmacie.stocks.update', $stock) }}" method="POST" class="rangée">
                                @csrf
                                @method('PUT')
                                <input type="number" name="prix" value="{{ $stock->prix }}" min="0" class="champ" style="width:110px;" aria-label="Prix">
                                <input type="number" name="quantite" value="{{ $stock->quantite }}" min="0" class="champ" style="width:80px;" aria-label="Quantité">
                                <input type="date" name="date_peremption" value="{{ $stock->date_peremption?->format('Y-m-d') }}" class="champ" style="width:150px;" aria-label="Péremption">
                                <button class="btn btn-secondaire btn-petit">💾 Enregistrer</button>
                            </form>
                        </td>
                        <td class="texte-droit">
                            @if($stock->stockBas())
                                <span class="badge badge-rouge">Stock bas</span>
                            @endif
                            @if($stock->peremptionProche())
                                <span class="badge badge-ambre">Péremption</span>
                            @endif
                            <form action="{{ route('pharmacie.stocks.destroy', $stock) }}" method="POST" class="mt-2" onsubmit="return confirm('Retirer du catalogue ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-petit">Retirer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding:32px; text-align:center;" class="texte-doux">Catalogue vide — ajoutez votre premier médicament.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $stocks->links() }}
</div>
@endsection

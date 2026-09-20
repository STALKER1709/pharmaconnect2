@extends('layouts.app')

@section('titre', 'Médicaments')

@section('contenu')
<div class="mb-4">
    <h1 class="titre-page">Rechercher un médicament</h1>
    <p class="sous-titre">Comparez les prix des pharmacies partenaires de Douala.</p>
</div>

<form action="{{ route('public.medicaments') }}" method="GET" class="carte carte-corps mb-6" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
    <div style="flex:1; min-width:220px;">
        <label class="champ-label">Nom, fabricant ou référence</label>
        <input type="search" name="q" value="{{ $q }}" class="champ" placeholder="Paracétamol, Sanofi…">
    </div>
    <div style="width:190px;">
        <label class="champ-label">Catégorie</label>
        <select name="categorie" class="champ">
            <option value="">Toutes</option>
            @foreach($categories as $categorie)
                <option value="{{ $categorie->id }}" @selected($categorieId == $categorie->id)>{{ $categorie->icone }} {{ $categorie->nom }}</option>
            @endforeach
        </select>
    </div>
    <div style="width:170px;">
        <label class="champ-label">Ordonnance</label>
        <select name="ordonnance" class="champ">
            <option value="">Tous</option>
            <option value="1" @selected($ordonnance === '1')>Avec ordonnance</option>
            <option value="0" @selected($ordonnance === '0')>Sans ordonnance</option>
        </select>
    </div>
    <button class="btn btn-primaire">Filtrer</button>
</form>

<div class="grille grille-4">
    @forelse($medicaments as $medicament)
        <a href="{{ route('public.medicament', $medicament) }}" class="carte carte-corps medic-carte">
            <div>
                <div class="medic-visuel">
                    <span class="medic-badge-haut badge badge-gris">{{ $medicament->categorie?->nom ?? 'Médicament' }}</span>
                    @if($medicament->ordonnance_obligatoire)
                        <span class="medic-badge-stock badge badge-ambre">Ordonnance</span>
                    @endif
                    💊
                </div>
                <h2 class="medic-nom">{{ $medicament->nom }}</h2>
                <p class="medic-cat">{{ $medicament->forme }} · {{ $medicament->fabricant }}</p>
            </div>
            <div class="medic-pied">
                <span class="badge badge-vert">Voir les pharmacies</span>
                <span class="lien-voir-tout">→</span>
            </div>
        </a>
    @empty
        <div class="carte vide" style="grid-column:1/-1;"><div class="vide-icone">🔎</div>Aucun médicament trouvé pour ces critères.</div>
    @endforelse
</div>

{{ $medicaments->links() }}
@endsection

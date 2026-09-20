@extends('layouts.app')

@section('titre', 'Pharmacies')

@section('contenu')
<div class="rangee-entre mb-6">
    <div>
        <h1 class="titre-page">Pharmacies partenaires</h1>
        <p class="sous-titre">Agréées, vérifiées, notées par la communauté.</p>
    </div>
    <form action="{{ route('public.pharmacies') }}" method="GET" class="rangée">
        <input type="search" name="q" value="{{ $q }}" placeholder="Nom ou quartier…" class="champ" style="width:260px;">
        <button class="btn btn-primaire">Rechercher</button>
    </form>
</div>

<div class="grille grille-3">
    @forelse($pharmacies as $pharmacie)
        <a href="{{ route('public.pharmacie', $pharmacie) }}" class="carte carte-corps pharmacie-carte">
            <div class="rangee-entre">
                <div>
                    <h2 class="pharmacie-nom">{{ $pharmacie->nom }}</h2>
                    <p class="pharmacie-meta">{{ $pharmacie->adresse }}, {{ $pharmacie->quartier }}</p>
                </div>
                <span class="badge {{ $pharmacie->estOuverte() ? 'badge-vert' : 'badge-gris' }}">{{ $pharmacie->statutOuverture() }}</span>
            </div>
            <div class="rangée texte-petit">
                <span>⭐ {{ number_format($pharmacie->note_moyenne, 1) }} ({{ $pharmacie->nb_avis }})</span>
                <span>🛵 {{ \App\Support\Fcfa::montant($pharmacie->frais_livraison) }}</span>
            </div>
            @if($pharmacie->description)
                <p class="texte-petit texte-doux">{{ $pharmacie->description }}</p>
            @endif
        </a>
    @empty
        <div class="carte vide" style="grid-column:1/-1;"><div class="vide-icone">🏥</div>Aucune pharmacie trouvée.</div>
    @endforelse
</div>

{{ $pharmacies->links() }}
@endsection

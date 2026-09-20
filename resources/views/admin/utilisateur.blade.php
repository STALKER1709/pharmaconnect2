@extends('layouts.app')

@section('titre', $user->name)

@section('contenu')
<div style="max-width:680px; margin:0 auto;">
    <div class="carte carte-corps mb-6">
        <div class="rangee-entre">
            <div>
                <h1 class="titre-page">{{ $user->name }}</h1>
                <p class="sous-titre">{{ $user->email }} · 📞 {{ $user->telephone ?? '—' }}</p>
            </div>
            <span class="badge {{ $user->statut === 'actif' ? 'badge-vert' : ($user->statut === 'en_attente' ? 'badge-ambre' : 'badge-rouge') }}">{{ ucfirst($user->statut) }}</span>
        </div>
    </div>

    @if($user->estPharmacie() && $user->pharmacie)
        <div class="carte carte-corps mb-6">
            <h2 class="carte-titre" style="font-size:16px;">🏥 Pharmacie</h2>
            <p class="mt-2 texte-petit"><strong style="color:var(--encre);">{{ $user->pharmacie->nom }}</strong></p>
            <p class="texte-petit texte-doux">{{ $user->pharmacie->adresse }}, {{ $user->pharmacie->quartier }} {{ $user->pharmacie->ville }}</p>
            <p class="mt-2 texte-petit">GPS : {{ $user->pharmacie->latitude }}, {{ $user->pharmacie->longitude }}</p>
            <p class="mt-2 texte-petit">Frais de livraison : {{ \App\Support\Fcfa::montant($user->pharmacie->frais_livraison) }}</p>
        </div>
    @endif

    @if($user->estLivreur() && $user->livreur)
        <div class="carte carte-corps mb-6">
            <h2 class="carte-titre" style="font-size:16px;">🛵 Livreur</h2>
            <p class="mt-2 texte-petit">Véhicule : {{ $user->livreur->vehiculeLabel() }} — {{ $user->livreur->immatriculation ?? '—' }}</p>
            <p class="mt-2 texte-petit">Disponibilité : {{ $user->livreur->disponibilite }}</p>
        </div>
    @endif

    <div class="rangée">
        @if($user->statut === 'en_attente')
            <form action="{{ route('admin.utilisateurs.valider', $user) }}" method="POST">
                @csrf <button class="btn btn-primaire">✓ Valider ce compte</button>
            </form>
        @endif
        <a href="{{ route('admin.utilisateurs') }}" class="btn btn-secondaire">← Retour</a>
    </div>
</div>
@endsection

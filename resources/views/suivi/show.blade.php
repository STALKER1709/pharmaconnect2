@extends('layouts.app')

@section('titre', 'Suivi '.$commande->numero)

@section('contenu')
<div x-data="PharmaConnect.suivi({
         commandeId: {{ $commande->id }},
         statut: '{{ $commande->statut->value }}',
         statutLabel: '{{ $commande->statut->label() }}',
         statutLivraison: '{{ $commande->livraison?->statut->value ?? '' }}',
         estLivreur: {{ auth()->user()->estLivreur() ? 'true' : 'false' }},
         latitude: {{ $commande->pharmacie->latitude ?? 4.0511 }},
         longitude: {{ $commande->pharmacie->longitude ?? 9.7679 }},
         pharmacie: @json($commande->pharmacie->latitude ? ['lat' => (float) $commande->pharmacie->latitude, 'lng' => (float) $commande->pharmacie->longitude, 'nom' => $commande->pharmacie->nom] : null),
         arrivee: @json($commande->latitude_livraison ? ['lat' => (float) $commande->latitude_livraison, 'lng' => (float) $commande->longitude_livraison] : null),
         csrf: '{{ csrf_token() }}',
         urls: {
             position: '{{ route('suivi.position', $commande) }}',
             partager: '{{ route('suivi.partager-position', $commande) }}',
             signalerPosition: '{{ auth()->user()->estLivreur() && $commande->livraison ? route('livreur.livraisons.position', $commande->livraison) : '' }}',
         },
     })"
     x-init="init()" @beforeunload.window="destroy()">

    <div class="rangee-entre mb-6">
        <div>
            <h1 class="titre-page">Suivi — {{ $commande->numero }}</h1>
            <p class="sous-titre">Position du livreur en temps réel (Reverb + OpenStreetMap).</p>
        </div>
        <div class="texte-droit">
            <span class="badge badge-bleu" style="font-size:14px; padding:6px 14px;" x-text="statutLabel"></span>
            <p class="texte-petit texte-doux mt-2">Commande : {{ $commande->statut->label() }}</p>
        </div>
    </div>

    <div class="carte mb-6" style="overflow:hidden;">
        <div id="carte-suivi" class="carte-carte"></div>
    </div>

    <div class="grille grille-3 mb-6">
        <div class="carte carte-corps">
            <div class="stat-libelle">🏥 Préparée par</div>
            <div style="font-weight:700; color:var(--encre); margin-top:4px;">{{ $commande->pharmacie->nom }}</div>
        </div>
        <div class="carte carte-corps">
            <div class="stat-libelle">🛵 Livreur</div>
            <div style="font-weight:700; color:var(--encre); margin-top:4px;">{{ $commande->livreur?->user?->name ?? 'En attente d\'assignation' }}</div>
        </div>
        <div class="carte carte-corps">
            <div class="stat-libelle">📍 Destination</div>
            <div style="font-weight:700; color:var(--encre); margin-top:4px;">{{ $commande->adresse_livraison }}</div>
        </div>
    </div>

    <div class="rangée">
        <button type="button" @click="partagerMaPosition()" class="btn btn-primaire">📍 Partager / affiner ma position</button>
        <a href="{{ route('commandes.show', $commande) }}" class="btn btn-secondaire">← Détails de la commande</a>
        @auth
            <a href="{{ route('messagerie.demarrer', $commande->pharmacie->user) }}" class="btn btn-secondaire">💬 Contacter la pharmacie</a>
        @endauth
    </div>
</div>
@endsection

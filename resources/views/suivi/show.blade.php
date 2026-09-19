@extends('layouts.app')

@section('titre', 'Suivi '.$commande->numero)

@section('contenu')
<div class="space-y-6"
     x-data="PharmaConnect.suivi({
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
     x-init="init()" x-effect="destroy" @beforeunload.window="destroy()">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black">Suivi — {{ $commande->numero }}</h1>
            <p class="text-sm text-slate-500">Position du livreur en temps réel (Reverb + OpenStreetMap).</p>
        </div>
        <div class="text-right">
            <span class="badge px-3 py-1.5 text-sm bg-cyan-100 text-cyan-800" x-text="statutLabel"></span>
            <p class="mt-1 text-xs text-slate-400">Commande : {{ $commande->statut->label() }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div id="carte-suivi" class="h-96 w-full"></div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card p-4 text-sm">
            <div class="text-slate-500">🏥 Préparée par</div>
            <div class="font-bold">{{ $commande->pharmacie->nom }}</div>
        </div>
        <div class="card p-4 text-sm">
            <div class="text-slate-500">🛵 Livreur</div>
            <div class="font-bold">{{ $commande->livreur?->user?->name ?? 'En attente d\'assignation' }}</div>
        </div>
        <div class="card p-4 text-sm">
            <div class="text-slate-500">📍 Destination</div>
            <div class="font-bold">{{ $commande->adresse_livraison }}</div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <button @click="partagerMaPosition()" class="btn-primary">📍 Partager / affiner ma position</button>
        <a href="{{ route('commandes.show', $commande) }}" class="btn-secondary">← Détails de la commande</a>
        @auth
            <a href="{{ route('messagerie.demarrer', $commande->pharmacie->user) }}" class="btn-secondary">💬 Contacter la pharmacie</a>
        @endauth
    </div>
</div>
@endsection

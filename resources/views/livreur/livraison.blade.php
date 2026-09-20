@extends('layouts.app')

@section('titre', 'Livraison '.$livraison->commande?->numero)

@section('contenu')
<div style="max-width:820px; margin:0 auto;"
     x-data="PharmaConnect.suivi({
         commandeId: {{ $livraison->commande?->id ?? 0 }},
         statut: '{{ $livraison->statut->value }}',
         statutLabel: '{{ $livraison->statut->label() }}',
         statutLivraison: '{{ $livraison->statut->value }}',
         estLivreur: true,
         latitude: {{ $livraison->commande?->pharmacie?->latitude ?? 4.0511 }},
         longitude: {{ $livraison->commande?->pharmacie?->longitude ?? 9.7679 }},
         pharmacie: @json($livraison->commande?->pharmacie ? ['lat' => (float) $livraison->commande->pharmacie->latitude, 'lng' => (float) $livraison->commande->pharmacie->longitude, 'nom' => $livraison->commande->pharmacie->nom] : null),
         arrivee: @json($livraison->latitude_arrivee ? ['lat' => (float) $livraison->latitude_arrivee, 'lng' => (float) $livraison->longitude_arrivee] : null),
         csrf: '{{ csrf_token() }}',
         urls: {
             position: '#',
             partager: '#',
             signalerPosition: '{{ route('livreur.livraisons.position', $livraison) }}',
         },
     })"
     x-init="init()" @beforeunload.window="destroy()">

    <div class="rangee-entre mb-6">
        <div>
            <h1 class="titre-page">Course {{ $livraison->commande?->numero }}</h1>
            <p class="sous-titre">🏥 {{ $livraison->commande?->pharmacie?->nom }} → 📍 {{ $livraison->commande?->adresse_livraison }}</p>
        </div>
        <span class="badge badge-bleu" style="font-size:14px; padding:6px 14px;" x-text="statutLabel"></span>
    </div>

    <div class="carte mb-6" style="overflow:hidden;">
        <div id="carte-suivi" class="carte-carte" style="height:320px;"></div>
    </div>

    <div class="carte carte-corps mb-6">
        <h2 class="carte-titre" style="font-size:16px;">Articles à livrer</h2>
        <ul style="list-style:none; margin:8px 0 0; padding:0; font-size:14px;">
            @foreach($livraison->commande?->lignes ?? [] as $ligne)
                <li class="rangee-entre" style="padding:6px 0;">
                    <span>{{ $ligne->nom_medicament }} × {{ $ligne->quantite }}</span>
                </li>
            @endforeach
        </ul>
        @if($livraison->commande?->notes)
            <div class="alerte alerte-ambre mt-4">📝 {{ $livraison->commande->notes }}</div>
        @endif
    </div>

    {{-- Flux d'actions --}}
    <div class="carte carte-corps mb-6" style="display:grid; gap:12px;">
        <h2 class="carte-titre" style="font-size:16px;">Actions</h2>

        @if($livraison->statut === \App\Enums\LivraisonStatut::Assignee)
            <form action="{{ route('livreur.livraisons.accepter', $livraison) }}" method="POST">
                @csrf
                <button class="btn btn-primaire" style="width:100%;">✓ Accepter cette course</button>
            </form>
        @elseif($livraison->statut === \App\Enums\LivraisonStatut::Acceptee)
            <form action="{{ route('livreur.livraisons.demarrer', $livraison) }}" method="POST">
                @csrf
                <button class="btn btn-primaire" style="width:100%;">🛵 Démarrer la livraison (partage GPS activé)</button>
            </form>
        @elseif($livraison->statut === \App\Enums\LivraisonStatut::EnRoute)
            <div class="alerte alerte-info">📡 Position partagée automatiquement toutes les 10 s.</div>
            <form action="{{ route('livreur.livraisons.arrivee', $livraison) }}" method="POST">
                @csrf
                <button class="btn btn-primaire" style="width:100%;">📍 Je suis arrivé sur place</button>
            </form>
        @elseif($livraison->statut === \App\Enums\LivraisonStatut::Arrivee)
            <form action="{{ route('livreur.livraisons.livrer', $livraison) }}" method="POST">
                @csrf
                <button class="btn btn-primaire" style="width:100%;">✅ Colis remis au client</button>
            </form>
        @elseif($livraison->statut === \App\Enums\LivraisonStatut::Livree)
            <p style="color:var(--vert-700); font-weight:600;">✅ Livrée — en attente de la confirmation du client.</p>
        @endif
    </div>

    @auth
        <div class="rangée">
            @if($livraison->commande?->pharmacie?->user)
                <a href="{{ route('messagerie.demarrer', $livraison->commande->pharmacie->user) }}" class="btn btn-secondaire btn-petit">💬 Pharmacien</a>
            @endif
            @if($livraison->commande?->client?->user)
                <a href="{{ route('messagerie.demarrer', $livraison->commande->client->user) }}" class="btn btn-secondaire btn-petit">💬 Client</a>
            @endif
        </div>
    @endauth
</div>
@endsection

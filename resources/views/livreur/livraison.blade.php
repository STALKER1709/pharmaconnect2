@extends('layouts.app')

@section('titre', 'Livraison '.$livraison->commande?->numero)

@section('contenu')
<div class="mx-auto max-w-3xl space-y-6"
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

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black">Course {{ $livraison->commande?->numero }}</h1>
            <p class="text-sm text-slate-500">🏥 {{ $livraison->commande?->pharmacie?->nom }} → 📍 {{ $livraison->commande?->adresse_livraison }}</p>
        </div>
        <span class="badge px-3 py-1.5 text-sm bg-cyan-100 text-cyan-800" x-text="statutLabel"></span>
    </div>

    <div class="card overflow-hidden">
        <div id="carte-suivi" class="h-80 w-full"></div>
    </div>

    <div class="card p-5">
        <h2 class="mb-2 font-bold">Articles à livrer</h2>
        <ul class="text-sm text-slate-600">
            @foreach($livraison->commande?->lignes ?? [] as $ligne)
                <li class="flex justify-between py-1">
                    <span>{{ $ligne->nom_medicament }} × {{ $ligne->quantite }}</span>
                </li>
            @endforeach
        </ul>
        @if($livraison->commande?->notes)
            <p class="mt-2 rounded-lg bg-amber-50 p-2 text-xs text-amber-800">📝 {{ $livraison->commande->notes }}</p>
        @endif
    </div>

    {{-- Flux d'actions --}}
    <div class="card space-y-3 p-5">
        <h2 class="font-bold">Actions</h2>

        @if($livraison->statut === \App\Enums\LivraisonStatut::Assignee)
            <form action="{{ route('livreur.livraisons.accepter', $livraison) }}" method="POST">
                @csrf
                <button class="btn-primary w-full">✓ Accepter cette course</button>
            </form>
        @elseif($livraison->statut === \App\Enums\LivraisonStatut::Acceptee)
            <form action="{{ route('livreur.livraisons.demarrer', $livraison) }}" method="POST">
                @csrf
                <button class="btn-primary w-full">🛵 Démarrer la livraison (partage GPS activé)</button>
            </form>
        @elseif($livraison->statut === \App\Enums\LivraisonStatut::EnRoute)
            <p class="rounded-lg bg-cyan-50 p-3 text-sm text-cyan-800">📡 Position partagée automatiquement toutes les 10 s.</p>
            <form action="{{ route('livreur.livraisons.arrivee', $livraison) }}" method="POST">
                @csrf
                <button class="btn-primary w-full">📍 Je suis arrivé sur place</button>
            </form>
        @elseif($livraison->statut === \App\Enums\LivraisonStatut::Arrivee)
            <form action="{{ route('livreur.livraisons.livrer', $livraison) }}" method="POST">
                @csrf
                <button class="btn-primary w-full">✅ Colis remis au client</button>
            </form>
        @elseif($livraison->statut === \App\Enums\LivraisonStatut::Livree)
            <p class="text-sm text-emerald-700">✅ Livrée — en attente de la confirmation du client.</p>
        @endif
    </div>

    @auth
        <div class="flex flex-wrap gap-2">
            @if($livraison->commande?->pharmacie?->user)
                <a href="{{ route('messagerie.demarrer', $livraison->commande->pharmacie->user) }}" class="btn-secondary text-sm">💬 Pharmacien</a>
            @endif
            @if($livraison->commande?->client?->user)
                <a href="{{ route('messagerie.demarrer', $livraison->commande->client->user) }}" class="btn-secondary text-sm">💬 Client</a>
            @endif
        </div>
    @endauth
</div>
@endsection

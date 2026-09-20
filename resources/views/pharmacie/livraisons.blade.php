@extends('layouts.app')

@section('titre', 'Livraisons')

@section('contenu')
<h1 class="titre-page mb-6">🛵 Livraisons</h1>

<div class="carte" style="overflow:hidden;">
    @forelse($commandes as $commande)
        <div class="rangee-entre" style="padding:16px 20px; border-bottom:1px solid var(--bord);">
            <div>
                <div style="font-weight:700; color:var(--encre);">{{ $commande->numero }} — {{ $commande->client?->user?->name }}</div>
                <div class="texte-petit texte-doux">
                    🛵 {{ $commande->livraison->livreur?->user?->name ?? 'Non assigné' }}
                </div>
            </div>
            <div class="rangée">
                <span class="badge badge-bleu">{{ $commande->livraison->statut->label() }}</span>
                @auth
                    <a href="{{ route('messagerie.demarrer', $commande->client->user) }}" class="btn btn-secondaire btn-petit">💬 Client</a>
                @endauth
            </div>
        </div>
    @empty
        <div class="vide"><div class="vide-icone">🛵</div>Aucune livraison.</div>
    @endforelse
</div>

{{ $commandes->links() }}
@endsection

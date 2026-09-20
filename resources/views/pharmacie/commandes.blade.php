@extends('layouts.app')

@section('titre', 'Commandes pharmacie')

@section('contenu')
<h1 class="titre-page mb-6">📋 Commandes reçues</h1>

<div class="carte" style="overflow:hidden;">
    @forelse($commandes as $commande)
        <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; padding:16px 20px; border-bottom:1px solid var(--bord);">
            <div>
                <a href="{{ route('pharmacie.commandes.show', $commande) }}" style="font-weight:700; color:var(--vert-700);">{{ $commande->numero }}</a>
                <div class="texte-petit texte-doux">
                    👤 {{ $commande->client?->user?->name }}
                    · {{ $commande->lignes->count() }} article(s)
                    · {{ $commande->created_at->format('d/m H:i') }}
                </div>
                @if($commande->paiement)
                    <div class="texte-petit texte-doux">
                        💰 {{ $commande->paiement->operateur->label() }} — {{ $commande->paiement->statut->label() }}
                    </div>
                @endif
            </div>

            <div class="rangée">
                <span class="prix">{{ $commande->totalFormatte() }}</span>
                <span class="badge {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>

                {{-- Actions selon le statut (machine à états) --}}
                @if($commande->statut === \App\Enums\CommandeStatut::EnAttente)
                    <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" class="rangée">
                        @csrf
                        <input type="hidden" name="statut" value="confirmee">
                        <button class="btn btn-primaire btn-petit">✓ Accepter</button>
                    </form>
                    <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST">
                        @csrf
                        <input type="hidden" name="statut" value="refusee">
                        <button class="btn btn-danger btn-petit">✗ Refuser</button>
                    </form>
                @elseif($commande->statut === \App\Enums\CommandeStatut::Confirmee)
                    <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST">
                        @csrf
                        <input type="hidden" name="statut" value="prete">
                        <button class="btn btn-primaire btn-petit">📦 Marquer prête</button>
                    </form>
                @elseif($commande->statut === \App\Enums\CommandeStatut::Prete)
                    <a href="{{ route('pharmacie.commandes.show', $commande) }}" class="btn btn-primaire btn-petit">🛵 Assigner un livreur</a>
                @endif
            </div>
        </div>
    @empty
        <div class="vide"><div class="vide-icone">📋</div>Aucune commande pour le moment.</div>
    @endforelse
</div>

{{ $commandes->links() }}
@endsection

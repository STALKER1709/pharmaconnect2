@extends('layouts.app')

@section('titre', 'Commandes pharmacie')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">📋 Commandes reçues</h1>

    <div class="card divide-y divide-menthe-50">
        @forelse($commandes as $commande)
            <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                <div>
                    <a href="{{ route('pharmacie.commandes.show', $commande) }}" class="font-bold hover:text-menthe-700">{{ $commande->numero }}</a>
                    <div class="text-sm text-slate-500">
                        👤 {{ $commande->client?->user?->name }}
                        · {{ $commande->lignes->count() }} article(s)
                        · {{ $commande->created_at->format('d/m H:i') }}
                    </div>
                    @if($commande->paiement)
                        <div class="text-xs text-slate-400">
                            💰 {{ $commande->paiement->operateur->label() }} — {{ $commande->paiement->statut->label() }}
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-black">{{ $commande->totalFormatte() }}</span>
                    <span class="badge {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>

                    {{-- Actions selon le statut (machine à états) --}}
                    @if($commande->statut === \App\Enums\CommandeStatut::EnAttente)
                        <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" class="flex gap-1">
                            @csrf
                            <input type="hidden" name="statut" value="confirmee">
                            <button class="btn-primary text-xs">✓ Accepter</button>
                        </form>
                        <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST">
                            @csrf
                            <input type="hidden" name="statut" value="refusee">
                            <button class="btn-danger text-xs">✗ Refuser</button>
                        </form>
                    @elseif($commande->statut === \App\Enums\CommandeStatut::Confirmee)
                        <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST">
                            @csrf
                            <input type="hidden" name="statut" value="prete">
                            <button class="btn-primary text-xs">📦 Marquer prête</button>
                        </form>
                    @elseif($commande->statut === \App\Enums\CommandeStatut::Prete)
                        <a href="{{ route('pharmacie.commandes.show', $commande) }}" class="btn-primary text-xs">🛵 Assigner un livreur</a>
                    @endif
                </div>
            </div>
        @empty
            <p class="p-8 text-center text-sm text-slate-400">Aucune commande pour le moment.</p>
        @endforelse
    </div>

    {{ $commandes->links() }}
</div>
@endsection

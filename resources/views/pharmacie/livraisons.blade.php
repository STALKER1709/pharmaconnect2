@extends('layouts.app')

@section('titre', 'Livraisons')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">🛵 Livraisons</h1>

    <div class="card divide-y divide-menthe-50">
        @forelse($commandes as $commande)
            <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                <div>
                    <div class="font-bold">{{ $commande->numero }} — {{ $commande->client?->user?->name }}</div>
                    <div class="text-sm text-slate-500">
                        🛵 {{ $commande->livraison->livreur?->user?->name ?? 'Non assigné' }}
                        · {{ $commande->livraison->statut->label() }}
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="badge bg-cyan-100 text-cyan-800">{{ $commande->livraison->statut->label() }}</span>
                    @auth
                        <a href="{{ route('messagerie.demarrer', $commande->client->user) }}" class="btn-secondary text-xs">💬 Client</a>
                    @endauth
                </div>
            </div>
        @empty
            <p class="p-8 text-center text-sm text-slate-400">Aucune livraison.</p>
        @endforelse
    </div>

    {{ $commandes->links() }}
</div>
@endsection

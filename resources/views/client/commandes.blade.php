@extends('layouts.app')

@section('titre', 'Mes commandes')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">Mes commandes</h1>

    <div class="card divide-y divide-menthe-50">
        @forelse($commandes as $commande)
            <a href="{{ route('commandes.show', $commande) }}" class="flex flex-wrap items-center justify-between gap-3 p-4 transition hover:bg-menthe-50/50">
                <div>
                    <div class="font-bold">{{ $commande->numero }}</div>
                    <div class="text-sm text-slate-500">{{ $commande->pharmacie?->nom }} · {{ $commande->created_at->format('d/m/Y à H:i') }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-black">{{ $commande->totalFormatte() }}</span>
                    <span class="badge {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>
                </div>
            </a>
        @empty
            <p class="p-6 text-sm text-slate-500">Aucune commande pour l'instant.</p>
        @endforelse
    </div>

    {{ $commandes->links() }}
</div>
@endsection

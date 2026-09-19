@extends('layouts.app')

@section('titre', 'Pharmacies')

@section('contenu')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black">Pharmacies partenaires</h1>
            <p class="text-sm text-slate-500">Agréées, vérifiées, notées par la communauté.</p>
        </div>
        <form action="{{ route('public.pharmacies') }}" method="GET" class="flex gap-2">
            <input type="search" name="q" value="{{ $q }}" placeholder="Nom ou quartier…" class="input w-64">
            <button class="btn-primary">Rechercher</button>
        </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($pharmacies as $pharmacie)
            <a href="{{ route('public.pharmacie', $pharmacie) }}" class="card group p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="font-bold group-hover:text-menthe-700">{{ $pharmacie->nom }}</h2>
                        <p class="text-sm text-slate-500">{{ $pharmacie->adresse }}, {{ $pharmacie->quartier }}</p>
                    </div>
                    <span class="badge {{ $pharmacie->estOuverte() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $pharmacie->statutOuverture() }}</span>
                </div>
                <div class="mt-3 flex items-center gap-3 text-sm text-slate-600">
                    <span>⭐ {{ number_format($pharmacie->note_moyenne, 1) }} ({{ $pharmacie->nb_avis }})</span>
                    <span>🛵 {{ \App\Support\Fcfa::montant($pharmacie->frais_livraison) }}</span>
                </div>
                <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $pharmacie->description }}</p>
            </a>
        @empty
            <p class="card col-span-full p-6 text-sm text-slate-500">Aucune pharmacie trouvée.</p>
        @endforelse
    </div>

    {{ $pharmacies->links() }}
</div>
@endsection

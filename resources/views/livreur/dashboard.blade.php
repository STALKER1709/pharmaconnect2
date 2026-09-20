@extends('layouts.app')

@section('titre', 'Espace livreur')

@section('contenu')
<div class="space-y-6"
     x-data="{ dispo: {{ $livreur->disponibilite === 'disponible' ? 'true' : 'false' }} }">

    <div class="card flex flex-wrap items-center justify-between gap-4 p-5">
        <div>
            <h1 class="text-2xl font-black">🛵 Espace livreur</h1>
            <p class="text-sm text-slate-500">{{ $livreur->vehiculeLabel() }} · ⭐ {{ number_format($livreur->note_moyenne, 1) }} ({{ $livreur->nb_avis }} avis)</p>
        </div>
        <form action="{{ route('livreur.disponibilite') }}" method="POST">
            @csrf
            <button class="btn px-5 py-2.5" :class="dispo ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50'">
                <span x-text="dispo ? '● En ligne — disponible' : '○ Hors ligne'"></span>
            </button>
        </form>
    </div>

    <dl class="grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Livraisons aujourd'hui</dt>
            <dd class="text-2xl font-black text-menthe-700">{{ $aujourdhui }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Total livrées</dt>
            <dd class="text-2xl font-black">{{ $totalLivrees }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">En cours</dt>
            <dd class="text-2xl font-black text-cyan-600">{{ $actives->count() }}</dd>
        </div>
    </dl>

    {{-- Mes courses actives --}}
    <section class="space-y-3">
        <h2 class="text-xl font-bold">Mes courses en cours</h2>
        @forelse($actives as $livraison)
            <a href="{{ route('livreur.livraison', $livraison) }}" class="card block p-4 transition hover:shadow-md">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="font-bold">{{ $livraison->commande?->numero }} — {{ $livraison->commande?->pharmacie?->nom }}</div>
                        <div class="text-sm text-slate-500">📍 {{ $livraison->commande?->adresse_livraison }}</div>
                    </div>
                    <span class="badge bg-cyan-100 text-cyan-800">{{ $livraison->statut->label() }}</span>
                </div>
            </a>
        @empty
            <p class="card p-6 text-sm text-slate-400">Aucune course active. Passez en ligne pour recevoir des livraisons.</p>
        @endforelse
    </section>

    {{-- Livraisons disponibles --}}
    <section class="space-y-3">
        <h2 class="text-xl font-bold">Livraisons disponibles</h2>
        @forelse($disponibles as $livraison)
            <div class="card flex flex-wrap items-center justify-between gap-3 p-4">
                <div>
                    <div class="font-bold">{{ $livraison->commande?->numero }} — {{ $livraison->commande?->pharmacie?->nom }}</div>
                    <div class="text-sm text-slate-500">📍 {{ $livraison->commande?->adresse_livraison }} · ⏱️ {{ $livraison->duree_estimee_min ?? '~35' }} min</div>
                </div>
                <form action="{{ route('livreur.livraisons.accepter', $livraison) }}" method="POST">
                    @csrf
                    <button class="btn-primary text-sm">Accepter la course</button>
                </form>
            </div>
        @empty
            <p class="card p-6 text-sm text-slate-400">Aucune livraison disponible pour le moment.</p>
        @endforelse
    </section>

    {{-- Historique --}}
    <section class="space-y-3">
        <h2 class="text-xl font-bold">Historique</h2>
        <div class="card divide-y divide-menthe-50">
            @forelse($terminees as $livraison)
                <div class="flex items-center justify-between p-3 text-sm">
                    <span>{{ $livraison->commande?->numero }} · {{ $livraison->commande?->pharmacie?->nom }}</span>
                    <span class="flex items-center gap-2">
                        <span class="text-xs text-slate-400">{{ $livraison->livree_at?->format('d/m H:i') }}</span>
                        <span class="badge {{ $livraison->statut->value === 'livree' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-700' }}">{{ $livraison->statut->label() }}</span>
                    </span>
                </div>
            @empty
                <p class="p-4 text-sm text-slate-400">Pas encore d'historique.</p>
            @endforelse
        </div>
        {{ $terminees->links() }}
    </section>
</div>
@endsection

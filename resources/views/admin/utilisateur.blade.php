@extends('layouts.app')

@section('titre', $user->name)

@section('contenu')
<div class="mx-auto max-w-2xl space-y-6">
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black">{{ $user->name }}</h1>
                <p class="text-sm text-slate-500">{{ $user->email }} · 📞 {{ $user->telephone ?? '—' }}</p>
            </div>
            <span class="badge {{ $user->statut === 'actif' ? 'bg-emerald-100 text-emerald-800' : ($user->statut === 'en_attente' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-700') }}">{{ ucfirst($user->statut) }}</span>
        </div>
    </div>

    @if($user->estPharmacie() && $user->pharmacie)
        <div class="card p-5 text-sm">
            <h2 class="mb-2 font-bold">🏥 Pharmacie</h2>
            <p><strong>{{ $user->pharmacie->nom }}</strong></p>
            <p class="text-slate-500">{{ $user->pharmacie->adresse }}, {{ $user->pharmacie->quartier }} {{ $user->pharmacie->ville }}</p>
            <p class="mt-1">GPS : {{ $user->pharmacie->latitude }}, {{ $user->pharmacie->longitude }}</p>
            <p class="mt-1">Frais de livraison : {{ \App\Support\Fcfa::montant($user->pharmacie->frais_livraison) }}</p>
        </div>
    @endif

    @if($user->estLivreur() && $user->livreur)
        <div class="card p-5 text-sm">
            <h2 class="mb-2 font-bold">🛵 Livreur</h2>
            <p>Véhicule : {{ $user->livreur->vehiculeLabel() }} — {{ $user->livreur->immatriculation ?? '—' }}</p>
            <p class="mt-1">Disponibilité : {{ $user->livreur->disponibilite }}</p>
        </div>
    @endif

    <div class="flex flex-wrap gap-2">
        @if($user->statut === 'en_attente')
            <form action="{{ route('admin.utilisateurs.valider', $user) }}" method="POST">
                @csrf <button class="btn-primary">✓ Valider ce compte</button>
            </form>
        @endif
        <a href="{{ route('admin.utilisateurs') }}" class="btn-secondary">← Retour</a>
    </div>
</div>
@endsection

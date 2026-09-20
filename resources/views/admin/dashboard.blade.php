@extends('layouts.app')

@section('titre', 'Administration')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">👑 Administration PharmaConnect</h1>

    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Clients</dt>
            <dd class="text-2xl font-black">{{ $nbClients }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Pharmacies actives</dt>
            <dd class="text-2xl font-black text-menthe-700">{{ $nbPharmacies }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Livreurs actifs</dt>
            <dd class="text-2xl font-black">{{ $nbLivreurs }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Commandes</dt>
            <dd class="text-2xl font-black">{{ $nbCommandes }}</dd>
        </div>
    </dl>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <h2 class="mb-3 font-bold">CA — 7 derniers jours</h2>
            <div class="h-56"><canvas id="graph-ca"></canvas></div>
        </div>
        <div class="card p-5">
            <h2 class="mb-3 font-bold">Recettes</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span>CA cumulé (livré)</span><span class="font-bold">{{ \App\Support\Fcfa::montant($caTotal) }}</span></div>
                <div class="flex justify-between"><span>CA ce mois</span><span class="font-bold">{{ \App\Support\Fcfa::montant($caMois) }}</span></div>
            </div>
        </div>
    </div>

    {{-- Comptes en attente de validation --}}
    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold">⏳ Comptes en attente ({{ $enAttente->count() }})</h2>
            <a href="{{ route('admin.utilisateurs') }}" class="text-sm font-semibold text-menthe-700 hover:underline">Gérer →</a>
        </div>
        <div class="card divide-y divide-menthe-50">
            @forelse($enAttente as $user)
                <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                    <div>
                        <div class="font-bold">{{ $user->name }}</div>
                        <div class="text-sm text-slate-500">
                            {{ $user->role === 'pharmacie' ? '🏥 '.$user->pharmacie?->nom : '🛵 Livreur '.$user->livreur?->vehiculeLabel() }}
                            · {{ $user->email }}
                        </div>
                        <div class="text-xs text-slate-400">Inscrit le {{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.utilisateurs.valider', $user) }}" method="POST">
                            @csrf
                            <button class="btn-primary text-xs">✓ Valider</button>
                        </form>
                        <a href="{{ route('admin.utilisateurs.show', $user) }}" class="btn-secondary text-xs">Détails</a>
                    </div>
                </div>
            @empty
                <p class="p-6 text-sm text-slate-400">Aucun compte en attente — tout est à jour ✓</p>
            @endforelse
        </div>
    </section>

    <section class="space-y-3">
        <h2 class="text-xl font-bold">Dernières commandes</h2>
        <div class="card divide-y divide-menthe-50">
            @foreach($commandesRecentes as $commande)
                <div class="flex items-center justify-between p-3 text-sm">
                    <span>{{ $commande->numero }} · {{ $commande->client?->user?->name }} → {{ $commande->pharmacie?->nom }}</span>
                    <span class="flex items-center gap-2">
                        <span class="font-semibold">{{ $commande->totalFormatte() }}</span>
                        <span class="badge {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>
                    </span>
                </div>
            @endforeach
        </div>
    </section>
</div>

@push('scripts')
<script type="module">
    PharmaConnect.graphiqueCA(document.getElementById('graph-ca'), @json($caParJour->pluck('jour')), @json($caParJour->pluck('total')));
</script>
@endpush
@endsection

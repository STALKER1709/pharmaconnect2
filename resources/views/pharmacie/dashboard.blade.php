@extends('layouts.app')

@section('titre', 'Espace pharmacie')

@section('contenu')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black">🏥 {{ $pharmacie->nom }}</h1>
            <p class="text-sm text-slate-500">{{ $pharmacie->quartier }}, {{ $pharmacie->ville }} · ⭐ {{ number_format($pharmacie->note_moyenne, 1) }}</p>
        </div>
        <div class="flex gap-2">
            <span class="badge {{ $pharmacie->estOuverte() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $pharmacie->statutOuverture() }}</span>
            <a href="{{ route('pharmacie.horaires') }}" class="btn-secondary text-sm">Gérer horaires</a>
        </div>
    </div>

    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <dt class="text-sm text-slate-500">CA aujourd'hui</dt>
            <dd class="text-2xl font-black text-menthe-700">{{ \App\Support\Fcfa::montant($caJour) }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">CA ce mois</dt>
            <dd class="text-2xl font-black">{{ \App\Support\Fcfa::montant($caMois) }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Commandes en attente</dt>
            <dd class="text-2xl font-black text-amber-600">{{ $nbEnAttente }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Commandes livrées</dt>
            <dd class="text-2xl font-black">{{ $nbLivrees }}</dd>
        </div>
    </dl>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="mb-3 font-bold">Chiffre d'affaires — 7 derniers jours</h2>
            <div class="h-56">
                <canvas id="graph-ca"></canvas>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card p-5">
                <h2 class="mb-2 font-bold">⚠️ Stock bas</h2>
                @forelse($stockBas as $stock)
                    <div class="flex justify-between border-b border-menthe-50 py-1.5 text-sm">
                        <span>{{ $stock->medicament->nom }}</span>
                        <span class="font-bold text-red-600">{{ $stock->quantite }} restant(s)</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Aucune alerte 🎉</p>
                @endforelse
            </div>

            <div class="card p-5">
                <h2 class="mb-2 font-bold">⏳ Péremption proche</h2>
                @forelse($peremption as $stock)
                    <div class="flex justify-between border-b border-menthe-50 py-1.5 text-sm">
                        <span>{{ $stock->medicament->nom }}</span>
                        <span class="text-amber-600">{{ $stock->date_peremption->format('m/Y') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Rien à signaler ✓</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card p-5">
        <h2 class="mb-3 font-bold">Top médicaments vendus</h2>
        @forelse($topMedicaments as $ligne)
            <div class="flex justify-between border-b border-menthe-50 py-1.5 text-sm">
                <span>{{ $ligne->nom_medicament }}</span>
                <span>{{ $ligne->total_vendus }} vendus · {{ \App\Support\Fcfa::montant($ligne->recette) }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-400">Pas encore de ventes.</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script type="module">
    PharmaConnect.graphiqueCA(
        document.getElementById('graph-ca'),
        @json($caParJour->pluck('jour')),
        @json($caParJour->pluck('total'))
    );
</script>
@endpush
@endsection

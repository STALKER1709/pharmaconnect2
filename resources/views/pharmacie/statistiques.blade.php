@extends('layouts.app')

@section('titre', 'Statistiques')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">📊 Statistiques</h1>

    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <dt class="text-sm text-slate-500">CA total</dt>
            <dd class="text-xl font-black text-menthe-700">{{ \App\Support\Fcfa::montant($caTotal) }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Commandes</dt>
            <dd class="text-xl font-black">{{ $nbCommandes }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Panier moyen</dt>
            <dd class="text-xl font-black">{{ \App\Support\Fcfa::montant($panierMoyen) }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Satisfaction</dt>
            <dd class="text-xl font-black">⭐ {{ $noteMoyenne }}/5 <span class="text-sm font-normal text-slate-400">({{ $nbAvis }} avis)</span></dd>
        </div>
    </dl>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="mb-3 font-bold">CA des 12 derniers mois</h2>
            <div class="h-64"><canvas id="graph-ca-mois"></canvas></div>
        </div>
        <div class="card p-5">
            <h2 class="mb-3 font-bold">Commandes par statut</h2>
            <div class="h-64"><canvas id="graph-statuts"></canvas></div>
        </div>
    </div>

    <div class="card p-5">
        <h2 class="mb-3 font-bold">Top médicaments vendus</h2>
        <div class="h-64"><canvas id="graph-top"></canvas></div>
    </div>
</div>

@push('scripts')
<script type="module">
    PharmaConnect.graphiqueCA(document.getElementById('graph-ca-mois'), @json($caParMois->pluck('mois')), @json($caParMois->pluck('total')));
    PharmaConnect.graphiqueStatuts(document.getElementById('graph-statuts'), @json($parStatut->keys()->map(fn ($s) => \App\Enums\CommandeStatut::from($s)->label())), @json($parStatut->values()));
    PharmaConnect.graphiqueBarres(document.getElementById('graph-top'), @json($topMedicaments->pluck('nom_medicament')), @json($topMedicaments->pluck('total_vendus')));
</script>
@endpush
@endsection

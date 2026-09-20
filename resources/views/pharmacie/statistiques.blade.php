@extends('layouts.app')

@section('titre', 'Statistiques')

@section('contenu')
<h1 class="titre-page mb-6">📊 Statistiques</h1>

<div class="grille grille-4 mb-6">
    <div class="carte carte-corps">
        <div class="stat-libelle">CA total</div>
        <div class="stat-valeur" style="color:var(--vert-600);">{{ \App\Support\Fcfa::montant($caTotal) }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Commandes</div>
        <div class="stat-valeur">{{ $nbCommandes }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Panier moyen</div>
        <div class="stat-valeur">{{ \App\Support\Fcfa::montant($panierMoyen) }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Satisfaction</div>
        <div class="stat-valeur">⭐ {{ $noteMoyenne }}/5</div>
        <div class="texte-petit texte-doux">({{ $nbAvis }} avis)</div>
    </div>
</div>

<div class="grille grille-2 mb-6">
    <div class="carte carte-corps">
        <h2 class="carte-titre" style="font-size:16px;">CA des 12 derniers mois</h2>
        <div style="height:256px; margin-top:12px;"><canvas id="graph-ca-mois"></canvas></div>
    </div>
    <div class="carte carte-corps">
        <h2 class="carte-titre" style="font-size:16px;">Commandes par statut</h2>
        <div style="height:256px; margin-top:12px;"><canvas id="graph-statuts"></canvas></div>
    </div>
</div>

<div class="carte carte-corps">
    <h2 class="carte-titre" style="font-size:16px;">Top médicaments vendus</h2>
    <div style="height:256px; margin-top:12px;"><canvas id="graph-top"></canvas></div>
</div>
@endsection

@push('scripts')
<script type="module">
    PharmaConnect.graphiqueCA(document.getElementById('graph-ca-mois'), @json($caParMois->pluck('mois')), @json($caParMois->pluck('total')));
    PharmaConnect.graphiqueStatuts(document.getElementById('graph-statuts'), @json($parStatut->keys()->map(fn ($s) => \App\Enums\CommandeStatut::from($s)->label())), @json($parStatut->values()));
    PharmaConnect.graphiqueBarres(document.getElementById('graph-top'), @json($topMedicaments->pluck('nom_medicament')), @json($topMedicaments->pluck('total_vendus')));
</script>
@endpush

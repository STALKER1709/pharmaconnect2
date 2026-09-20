@extends('layouts.app')

@section('titre', 'Espace pharmacie')

@section('contenu')
<div class="rangee-entre mb-6">
    <div>
        <h1 class="titre-page">🏥 {{ $pharmacie->nom }}</h1>
        <p class="sous-titre">{{ $pharmacie->quartier }}, {{ $pharmacie->ville }} · ⭐ {{ number_format($pharmacie->note_moyenne, 1) }}</p>
    </div>
    <div class="rangée">
        <span class="badge {{ $pharmacie->estOuverte() ? 'badge-vert' : 'badge-gris' }}">{{ $pharmacie->statutOuverture() }}</span>
        <a href="{{ route('pharmacie.horaires') }}" class="btn btn-secondaire btn-petit">Gérer horaires</a>
    </div>
</div>

<div class="grille grille-4 mb-6">
    <div class="carte carte-corps">
        <div class="stat-libelle">CA aujourd'hui</div>
        <div class="stat-valeur" style="color:var(--vert-600);">{{ \App\Support\Fcfa::montant($caJour) }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">CA ce mois</div>
        <div class="stat-valeur">{{ \App\Support\Fcfa::montant($caMois) }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Commandes en attente</div>
        <div class="stat-valeur" style="color:var(--ambre-700);">{{ $nbEnAttente }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Commandes livrées</div>
        <div class="stat-valeur">{{ $nbLivrees }}</div>
    </div>
</div>

<div class="grille" style="grid-template-columns:1fr; gap:24px;">
    <div class="carte carte-corps">
        <h2 class="carte-titre" style="font-size:16px;">Chiffre d'affaires — 7 derniers jours</h2>
        <div style="height:224px; margin-top:12px;">
            <canvas id="graph-ca"></canvas>
        </div>
    </div>

    <div class="grille grille-2">
        <div class="carte carte-corps">
            <h2 class="carte-titre" style="font-size:16px;">⚠️ Stock bas</h2>
            <div class="mt-4" style="display:grid; gap:6px; font-size:14px;">
                @forelse($stockBas as $stock)
                    <div class="rangee-entre" style="border-bottom:1px solid #f1f5f9; padding:6px 0;">
                        <span>{{ $stock->medicament->nom }}</span>
                        <span style="font-weight:700; color:var(--rouge-600);">{{ $stock->quantite }} restant(s)</span>
                    </div>
                @empty
                    <p class="texte-doux">Aucune alerte 🎉</p>
                @endforelse
            </div>
        </div>

        <div class="carte carte-corps">
            <h2 class="carte-titre" style="font-size:16px;">⏳ Péremption proche</h2>
            <div class="mt-4" style="display:grid; gap:6px; font-size:14px;">
                @forelse($peremption as $stock)
                    <div class="rangee-entre" style="border-bottom:1px solid #f1f5f9; padding:6px 0;">
                        <span>{{ $stock->medicament->nom }}</span>
                        <span style="color:var(--ambre-700);">{{ $stock->date_peremption->format('m/Y') }}</span>
                    </div>
                @empty
                    <p class="texte-doux">Rien à signaler ✓</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="carte carte-corps">
        <h2 class="carte-titre" style="font-size:16px;">Top médicaments vendus</h2>
        <div class="mt-4" style="display:grid; gap:6px; font-size:14px;">
            @forelse($topMedicaments as $ligne)
                <div class="rangee-entre" style="border-bottom:1px solid #f1f5f9; padding:6px 0;">
                    <span>{{ $ligne->nom_medicament }}</span>
                    <span>{{ $ligne->total_vendus }} vendus · {{ \App\Support\Fcfa::montant($ligne->recette) }}</span>
                </div>
            @empty
                <p class="texte-doux">Pas encore de ventes.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    PharmaConnect.graphiqueCA(
        document.getElementById('graph-ca'),
        @json($caParJour->pluck('jour')),
        @json($caParJour->pluck('total'))
    );
</script>
@endpush

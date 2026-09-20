@extends('layouts.app')

@section('titre', 'Administration')

@section('contenu')
<h1 class="titre-page mb-6">👑 Administration PharmaConnect</h1>

<div class="grille grille-4 mb-6">
    <div class="carte carte-corps">
        <div class="stat-libelle">Clients</div>
        <div class="stat-valeur">{{ $nbClients }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Pharmacies actives</div>
        <div class="stat-valeur" style="color:var(--vert-600);">{{ $nbPharmacies }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Livreurs actifs</div>
        <div class="stat-valeur">{{ $nbLivreurs }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Commandes</div>
        <div class="stat-valeur">{{ $nbCommandes }}</div>
    </div>
</div>

<div class="grille grille-2 mb-6">
    <div class="carte carte-corps">
        <h2 class="carte-titre" style="font-size:16px;">CA — 7 derniers jours</h2>
        <div style="height:224px; margin-top:12px;"><canvas id="graph-ca"></canvas></div>
    </div>
    <div class="carte carte-corps">
        <h2 class="carte-titre" style="font-size:16px;">Recettes</h2>
        <div class="mt-4" style="display:grid; gap:8px; font-size:14px;">
            <div class="rangee-entre"><span>CA cumulé (livré)</span><span style="font-weight:700;">{{ \App\Support\Fcfa::montant($caTotal) }}</span></div>
            <div class="rangee-entre"><span>CA ce mois</span><span style="font-weight:700;">{{ \App\Support\Fcfa::montant($caMois) }}</span></div>
        </div>
    </div>
</div>

{{-- Comptes en attente de validation --}}
<section class="mb-6">
    <div class="rangee-entre mb-4">
        <h2 class="titre-section">⏳ Comptes en attente ({{ $enAttente->count() }})</h2>
        <a href="{{ route('admin.utilisateurs') }}" class="lien-voir-tout">Gérer →</a>
    </div>
    <div class="carte" style="overflow:hidden;">
        @forelse($enAttente as $user)
            <div class="rangee-entre" style="padding:16px 20px; border-bottom:1px solid var(--bord);">
                <div>
                    <div style="font-weight:700; color:var(--encre);">{{ $user->name }}</div>
                    <div class="texte-petit texte-doux">
                        {{ $user->role === 'pharmacie' ? '🏥 '.$user->pharmacie?->nom : '🛵 Livreur '.$user->livreur?->vehiculeLabel() }}
                        · {{ $user->email }}
                    </div>
                    <div class="texte-petit texte-doux">Inscrit le {{ $user->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="rangée">
                    <form action="{{ route('admin.utilisateurs.valider', $user) }}" method="POST">
                        @csrf
                        <button class="btn btn-primaire btn-petit">✓ Valider</button>
                    </form>
                    <a href="{{ route('admin.utilisateurs.show', $user) }}" class="btn btn-secondaire btn-petit">Détails</a>
                </div>
            </div>
        @empty
            <div class="vide">Aucun compte en attente — tout est à jour ✓</div>
        @endforelse
    </div>
</section>

<section>
    <h2 class="titre-section mb-4">Dernières commandes</h2>
    <div class="carte" style="overflow:hidden;">
        @foreach($commandesRecentes as $commande)
            <div class="rangee-entre texte-petit" style="padding:12px 20px; border-bottom:1px solid var(--bord);">
                <span>{{ $commande->numero }} · {{ $commande->client?->user?->name }} → {{ $commande->pharmacie?->nom }}</span>
                <span class="rangée">
                    <span style="font-weight:600;">{{ $commande->totalFormatte() }}</span>
                    <span class="badge {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>
                </span>
            </div>
        @endforeach
    </div>
</section>
@endsection

@push('scripts')
<script type="module">
    PharmaConnect.graphiqueCA(document.getElementById('graph-ca'), @json($caParJour->pluck('jour')), @json($caParJour->pluck('total')));
</script>
@endpush

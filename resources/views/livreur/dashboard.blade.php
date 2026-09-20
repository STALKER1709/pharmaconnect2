@extends('layouts.app')

@section('titre', 'Espace livreur')

@section('contenu')
<div x-data="{ dispo: {{ $livreur->disponibilite === 'disponible' ? 'true' : 'false' }} }">

    <div class="carte carte-corps mb-6" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:16px;">
        <div>
            <h1 class="titre-page">🛵 Espace livreur</h1>
            <p class="sous-titre">{{ $livreur->vehiculeLabel() }} · ⭐ {{ number_format($livreur->note_moyenne, 1) }} ({{ $livreur->nb_avis }} avis)</p>
        </div>
        <form action="{{ route('livreur.disponibilite') }}" method="POST">
            @csrf
            <button class="btn" :class="dispo ? 'btn-primaire' : 'btn-secondaire'">
                <span x-text="dispo ? '● En ligne — disponible' : '○ Hors ligne'"></span>
            </button>
        </form>
    </div>

    <div class="grille grille-3 mb-6">
        <div class="carte carte-corps">
            <div class="stat-libelle">Livraisons aujourd'hui</div>
            <div class="stat-valeur" style="color:var(--vert-600);">{{ $aujourdhui }}</div>
        </div>
        <div class="carte carte-corps">
            <div class="stat-libelle">Total livrées</div>
            <div class="stat-valeur">{{ $totalLivrees }}</div>
        </div>
        <div class="carte carte-corps">
            <div class="stat-libelle">En cours</div>
            <div class="stat-valeur" style="color:var(--bleu-700);">{{ $actives->count() }}</div>
        </div>
    </div>

    {{-- Mes courses actives --}}
    <section class="mb-6">
        <h2 class="titre-section mb-4">Mes courses en cours</h2>
        <div style="display:grid; gap:12px;">
            @forelse($actives as $livraison)
                <a href="{{ route('livreur.livraison', $livraison) }}" class="carte carte-corps pharmacie-carte">
                    <div class="rangee-entre">
                        <div>
                            <div style="font-weight:700; color:var(--encre);">{{ $livraison->commande?->numero }} — {{ $livraison->commande?->pharmacie?->nom }}</div>
                            <div class="texte-petit texte-doux">📍 {{ $livraison->commande?->adresse_livraison }}</div>
                        </div>
                        <span class="badge badge-bleu">{{ $livraison->statut->label() }}</span>
                    </div>
                </a>
            @empty
                <div class="carte vide">Aucune course active. Passez en ligne pour recevoir des livraisons.</div>
            @endforelse
        </div>
    </section>

    {{-- Livraisons disponibles --}}
    <section class="mb-6">
        <h2 class="titre-section mb-4">Livraisons disponibles</h2>
        <div style="display:grid; gap:12px;">
            @forelse($disponibles as $livraison)
                <div class="carte carte-corps rangee-entre">
                    <div>
                        <div style="font-weight:700; color:var(--encre);">{{ $livraison->commande?->numero }} — {{ $livraison->commande?->pharmacie?->nom }}</div>
                        <div class="texte-petit texte-doux">📍 {{ $livraison->commande?->adresse_livraison }} · ⏱️ {{ $livraison->duree_estimee_min ?? '~35' }} min</div>
                    </div>
                    <form action="{{ route('livreur.livraisons.accepter', $livraison) }}" method="POST">
                        @csrf
                        <button class="btn btn-primaire btn-petit">Accepter la course</button>
                    </form>
                </div>
            @empty
                <div class="carte vide">Aucune livraison disponible pour le moment.</div>
            @endforelse
        </div>
    </section>

    {{-- Historique --}}
    <section>
        <h2 class="titre-section mb-4">Historique</h2>
        <div class="carte" style="overflow:hidden;">
            @forelse($terminees as $livraison)
                <div class="rangee-entre texte-petit" style="padding:12px 20px; border-bottom:1px solid var(--bord);">
                    <span>{{ $livraison->commande?->numero }} · {{ $livraison->commande?->pharmacie?->nom }}</span>
                    <span class="rangée">
                        <span class="texte-doux">{{ $livraison->livree_at?->format('d/m H:i') }}</span>
                        <span class="badge {{ $livraison->statut->value === 'livree' ? 'badge-vert' : 'badge-rouge' }}">{{ $livraison->statut->label() }}</span>
                    </span>
                </div>
            @empty
                <div class="vide">Pas encore d'historique.</div>
            @endforelse
        </div>
        {{ $terminees->links() }}
    </section>
</div>
@endsection

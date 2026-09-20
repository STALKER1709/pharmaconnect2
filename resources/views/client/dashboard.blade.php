@extends('layouts.app')

@section('titre', 'Mon espace')

@section('contenu')
<h1 class="titre-page mb-6">Bonjour, {{ auth()->user()->name }} 👋</h1>

<div class="grille grille-3 mb-6">
    <div class="carte carte-corps">
        <div class="stat-libelle">Commandes</div>
        <div class="stat-valeur">{{ $nbCommandes }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Pharmacies vedettes</div>
        <div class="mt-2" style="display:grid; gap:4px; font-size:14px;">
            @forelse($pharmaciesProches as $p)
                <a href="{{ route('public.pharmacie', $p) }}" style="color:var(--vert-700); font-weight:600;">🏥 {{ $p->nom }}</a>
            @empty
                <span class="texte-doux">—</span>
            @endforelse
        </div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Actions rapides</div>
        <div class="mt-2" style="display:grid; gap:8px;">
            <a href="{{ route('public.medicaments') }}" class="btn btn-primaire btn-petit">🔍 Rechercher un médicament</a>
            <a href="{{ route('messagerie.index') }}" class="btn btn-secondaire btn-petit">💬 Messagerie</a>
            <a href="{{ route('chatbot.index') }}" class="btn btn-secondaire btn-petit">🤖 Assistant santé</a>
        </div>
    </div>
</div>

<section>
    <div class="rangee-entre mb-4">
        <h2 class="titre-section">Dernières commandes</h2>
        <a href="{{ route('commandes.index') }}" class="lien-voir-tout">Tout voir →</a>
    </div>

    <div class="carte" style="overflow:hidden;">
        @forelse($commandes as $commande)
            <a href="{{ route('commandes.show', $commande) }}" class="rangee-entre" style="display:flex; flex-wrap:wrap; padding:16px 20px; border-bottom:1px solid var(--bord);"
               onmouseover="this.style.background='var(--vert-50)'" onmouseout="this.style.background=''">
                <div>
                    <div style="font-weight:700; color:var(--encre);">{{ $commande->numero }} — {{ $commande->pharmacie?->nom }}</div>
                    <div class="texte-petit texte-doux">{{ $commande->lignes->count() }} article(s) · {{ $commande->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="rangée">
                    <span class="prix">{{ $commande->totalFormatte() }}</span>
                    <span class="badge {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>
                </div>
            </a>
        @empty
            <div class="vide"><div class="vide-icone">📦</div>Aucune commande. <a href="{{ route('public.medicaments') }}" style="color:var(--vert-700); font-weight:600;">Trouvez votre médicament →</a></div>
        @endforelse
    </div>
</section>
@endsection

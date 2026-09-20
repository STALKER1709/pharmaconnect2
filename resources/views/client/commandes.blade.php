@extends('layouts.app')

@section('titre', 'Mes commandes')

@section('contenu')
<h1 class="titre-page mb-6">Mes commandes</h1>

<div class="carte" style="overflow:hidden;">
    @forelse($commandes as $commande)
        <a href="{{ route('commandes.show', $commande) }}" class="rangee-entre" style="display:flex; flex-wrap:wrap; padding:16px 20px; border-bottom:1px solid var(--bord); transition:background-color .15s;"
           onmouseover="this.style.background='var(--vert-50)'" onmouseout="this.style.background=''">
            <div>
                <div style="font-weight:700; color:var(--encre);">{{ $commande->numero }}</div>
                <div class="texte-petit texte-doux">{{ $commande->pharmacie?->nom }} · {{ $commande->created_at->format('d/m/Y à H:i') }}</div>
            </div>
            <div class="rangée">
                <span class="prix">{{ $commande->totalFormatte() }}</span>
                <span class="badge {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>
            </div>
        </a>
    @empty
        <div class="vide"><div class="vide-icone">📦</div>Aucune commande pour l'instant.</div>
    @endforelse
</div>

{{ $commandes->links() }}
@endsection

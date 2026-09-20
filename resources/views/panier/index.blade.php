@extends('layouts.app')

@section('titre', 'Mon panier')

@section('contenu')
<h1 class="titre-page mb-6">🛒 Mon panier</h1>

@if($lignes->isEmpty())
    <div class="carte vide">
        <div class="vide-icone">🛒</div>
        <p>Votre panier est vide.</p>
        <a href="{{ route('public.medicaments') }}" class="btn btn-primaire mt-4">Rechercher un médicament</a>
    </div>
@else
    <div class="grille grille-contenu-aside">
        <div style="display:grid; gap:12px;">
            <div class="alerte alerte-info">
                🏥 Pharmacie : <span style="font-weight:600;">{{ $pharmacie?->nom }}</span>
                — les commandes sont préparées par une seule pharmacie.
            </div>

            @foreach($lignes as $ligne)
                <div class="carte carte-corps" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
                    <div>
                        <a href="{{ route('public.medicament', $ligne['stock']->medicament) }}" class="medic-nom" style="color:var(--vert-700);">{{ $ligne['stock']->medicament->nom }}</a>
                        <p class="texte-petit texte-doux">{{ \App\Support\Fcfa::montant($ligne['stock']->prix) }} l'unité</p>
                    </div>

                    <form action="{{ route('panier.modifier') }}" method="POST" class="rangée">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="stock_id" value="{{ $ligne['stock']->id }}">
                        <input type="number" name="quantite" value="{{ $ligne['quantite'] }}" min="1" max="{{ $ligne['stock']->quantite }}"
                               class="champ" style="width:80px; text-align:center;">
                        <button class="btn btn-secondaire btn-petit">Mettre à jour</button>
                    </form>

                    <div class="rangée">
                        <span class="prix">{{ \App\Support\Fcfa::montant($ligne['sous_total']) }}</span>
                        <form action="{{ route('panier.supprimer') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="stock_id" value="{{ $ligne['stock']->id }}">
                            <button class="btn btn-danger btn-petit">Retirer</button>
                        </form>
                    </div>
                </div>
            @endforeach

            <form action="{{ route('panier.vider') }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-petit">Vider le panier</button>
            </form>
        </div>

        <aside class="carte carte-corps" style="align-self:start;">
            <h2 class="carte-titre">Récapitulatif</h2>
            <div class="rangee-entre mt-4 texte-petit"><span>Sous-total</span><span>{{ \App\Support\Fcfa::montant($sousTotal) }}</span></div>
            <div class="rangee-entre mt-2 texte-petit"><span>Livraison</span><span>{{ \App\Support\Fcfa::montant($fraisLivraison) }}</span></div>
            <hr class="separateur">
            <div class="rangee-entre"><span style="font-weight:700;">Total</span><span class="prix" style="font-size:22px;">{{ \App\Support\Fcfa::montant($total) }}</span></div>
            <a href="{{ route('commande.create') }}" class="btn btn-primaire mt-4" style="width:100%;">Passer la commande →</a>
        </aside>
    </div>
@endif
@endsection

@extends('layouts.app')

@section('titre', 'Commande '.$commande->numero)

@section('contenu')
<div class="rangee-entre mb-6">
    <div>
        <h1 class="titre-page">Commande {{ $commande->numero }}</h1>
        <p class="sous-titre">
            🏥 <a href="{{ route('public.pharmacie', $commande->pharmacie) }}" style="color:var(--vert-700); font-weight:600;">{{ $commande->pharmacie->nom }}</a>
            · {{ $commande->created_at->format('d/m/Y à H:i') }}
            @if($distanceKm !== null) · 📍 {{ $distanceKm }} km de la pharmacie @endif
        </p>
    </div>
    <span class="badge {{ $commande->statut->couleur() }}" style="font-size:14px; padding:6px 14px;">{{ $commande->statut->label() }}</span>
</div>

<div class="grille grille-contenu-aside">
    <div style="display:grid; gap:24px;">
        {{-- Articles --}}
        <div class="carte carte-corps">
            <h2 class="carte-titre" style="font-size:16px;">Articles</h2>
            <ul style="list-style:none; margin:8px 0 0; padding:0;">
                @foreach($commande->lignes as $ligne)
                    <li class="rangee-entre" style="padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:14px;">
                        <span>{{ $ligne->nom_medicament }} × {{ $ligne->quantite }}</span>
                        <span style="font-weight:600;">{{ \App\Support\Fcfa::montant($ligne->sous_total) }}</span>
                    </li>
                @endforeach
                <li class="rangee-entre" style="padding:8px 0; font-size:14px; color:var(--texte-doux);">
                    <span>Livraison</span><span>{{ \App\Support\Fcfa::montant($commande->frais_livraison) }}</span>
                </li>
                <li class="rangee-entre" style="padding-top:10px;">
                    <span style="font-weight:700;">Total</span><span class="prix" style="font-size:20px;">{{ $commande->totalFormatte() }}</span>
                </li>
            </ul>
        </div>

        {{-- Paiement --}}
        @if($commande->paiement)
            <div class="carte carte-corps">
                <h2 class="carte-titre" style="font-size:16px;">Paiement</h2>
                <div class="rangee-entre mt-4">
                    <div class="rangée">
                        <span class="badge {{ $commande->paiement->statut->value === 'reussi' ? 'badge-vert' : 'badge-ambre' }}">{{ $commande->paiement->statut->label() }}</span>
                        <span class="texte-petit">{{ $commande->paiement->operateur->label() }} · Réf. {{ $commande->paiement->reference }}</span>
                    </div>
                    <span class="prix">{{ $commande->paiement->montantFormate() }}</span>
                </div>
            </div>
        @endif

        {{-- Avis --}}
        @if($commande->statut === \App\Enums\CommandeStatut::Livree)
            <div class="carte carte-corps">
                <h2 class="carte-titre" style="font-size:16px;">Votre avis</h2>
                <form action="{{ route('commandes.avis', $commande) }}" method="POST" class="mt-4"
                      style="display:grid; gap:12px;"
                      x-data="{ type: 'pharmacie', note: 5 }">
                    @csrf
                    <div class="rangée">
                        @foreach(['pharmacie' => '🏥 Pharmacie', 'medicament' => '💊 Médicament', 'livreur' => '🛵 Livreur'] as $v => $l)
                            <label class="role-option" style="max-width:150px;">
                                <input type="radio" name="type" value="{{ $v }}" x-model="type" @checked($loop->first)>
                                <span>{{ $l }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div x-show="type === 'medicament'" x-cloak>
                        <select name="medicament_id" class="champ">
                            @foreach($commande->lignes as $ligne)
                                <option value="{{ $ligne->medicament_id }}">{{ $ligne->nom_medicament }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rangée">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="etoile">
                                <input type="radio" name="note" value="{{ $i }}" @checked($i === 5)>
                                <span>★</span>
                            </label>
                        @endfor
                        <span class="texte-petit texte-doux" style="margin-left:8px;">Note de 1 à 5</span>
                    </div>

                    <textarea name="commentaire" rows="2" class="champ" placeholder="Partagez votre expérience (optionnel)…"></textarea>
                    <button class="btn btn-primaire" style="justify-self:start;">Envoyer mon avis ⭐</button>
                </form>
            </div>
        @endif
    </div>

    {{-- Actions --}}
    <aside style="display:grid; gap:16px; align-content:start;">
        @if($commande->livraison && in_array($commande->statut->value, ['confirmee', 'prete', 'assignee', 'en_livraison']))
            <a href="{{ route('suivi.show', $commande) }}" class="btn btn-primaire" style="width:100%;">🗺️ Suivre ma livraison en direct</a>
        @endif

        @if($commande->peutEtreAnnuleeParClient())
            <form action="{{ route('commandes.annuler', $commande) }}" method="POST" onsubmit="return confirm('Annuler cette commande ?')">
                @csrf
                <button class="btn btn-danger" style="width:100%;">Annuler la commande</button>
            </form>
        @endif

        @if($commande->statut === \App\Enums\CommandeStatut::EnLivraison)
            <form action="{{ route('commandes.confirmer-reception', $commande) }}" method="POST">
                @csrf
                <button class="btn btn-primaire" style="width:100%;">✅ Confirmer la réception</button>
            </form>
        @endif

        <div class="carte carte-corps">
            <h3 class="carte-titre" style="font-size:15px;">Contacts</h3>
            <div class="mt-2" style="display:grid;">
                @auth
                    <a href="{{ route('messagerie.demarrer', $commande->pharmacie->user) }}" class="menu-user-lien">💬 Écrire à la pharmacie</a>
                @endauth
                @if($commande->livreur)
                    @auth
                        <a href="{{ route('messagerie.demarrer', $commande->livreur->user) }}" class="menu-user-lien">💬 Écrire au livreur</a>
                    @endauth
                @endif
                <a href="tel:{{ $commande->pharmacie->user->telephone }}" class="menu-user-lien">📞 Appeler la pharmacie</a>
            </div>
        </div>

        <div class="carte carte-corps">
            <h3 class="carte-titre" style="font-size:15px;">Livraison</h3>
            <p class="mt-2 texte-petit">{{ $commande->adresse_livraison }}, {{ $commande->ville_livraison }}</p>
            @if($commande->notes) <p class="mt-2 texte-petit texte-doux">📝 {{ $commande->notes }}</p> @endif
            @if($commande->livreur)
                <p class="mt-2 texte-petit">🛵 Livreur : {{ $commande->livreur->user->name }} ({{ $commande->livreur->vehiculeLabel() }})</p>
            @endif
        </div>
    </aside>
</div>
@endsection

@extends('layouts.app')

@section('titre', 'Commande '.$commande->numero)

@section('contenu')
<div style="max-width:880px; margin:0 auto;">
    <div class="rangee-entre mb-6">
        <div>
            <h1 class="titre-page">Commande {{ $commande->numero }}</h1>
            <p class="sous-titre">👤 {{ $commande->client?->user?->name }} · {{ $commande->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <span class="badge {{ $commande->statut->couleur() }}" style="font-size:14px; padding:6px 14px;">{{ $commande->statut->label() }}</span>
    </div>

    <div class="carte carte-corps mb-6">
        <h2 class="carte-titre" style="font-size:16px;">Articles</h2>
        <ul style="list-style:none; margin:8px 0 0; padding:0; font-size:14px;">
            @foreach($commande->lignes as $ligne)
                <li class="rangee-entre" style="padding:8px 0; border-bottom:1px solid #f1f5f9;">
                    <span>{{ $ligne->nom_medicament }} × {{ $ligne->quantite }}</span>
                    <span style="font-weight:600;">{{ \App\Support\Fcfa::montant($ligne->sous_total) }}</span>
                </li>
            @endforeach
            <li class="rangee-entre" style="padding:8px 0; color:var(--texte-doux);"><span>Livraison</span><span>{{ \App\Support\Fcfa::montant($commande->frais_livraison) }}</span></li>
            <li class="rangee-entre" style="padding-top:10px;"><span style="font-weight:700;">Total</span><span class="prix" style="font-size:20px;">{{ $commande->totalFormatte() }}</span></li>
        </ul>
    </div>

    @if($commande->paiement)
        <div class="carte carte-corps mb-6">
            <h2 class="carte-titre" style="font-size:16px;">Paiement</h2>
            <div class="rangée mt-2">
                {{ $commande->paiement->operateur->label() }} · {{ $commande->paiement->montantFormate() }}
                <span class="badge {{ $commande->paiement->statut->value === 'reussi' ? 'badge-vert' : 'badge-ambre' }}">{{ $commande->paiement->statut->label() }}</span>
            </div>
            <div class="texte-petit texte-doux mt-2">Réf. {{ $commande->paiement->reference }} · Payeur : {{ $commande->paiement->numero_payeur }}</div>
        </div>
    @endif

    <div class="carte carte-corps mb-6">
        <h2 class="carte-titre" style="font-size:16px;">🛵 Assigner un livreur</h2>

        @if($commande->statut !== \App\Enums\CommandeStatut::Prete)
            <p class="mt-2 texte-petit texte-doux">
                La commande doit d'abord être <strong>confirmée</strong> puis <strong>prête</strong> avant l'assignation.
            </p>
            @if($commande->statut === \App\Enums\CommandeStatut::EnAttente)
                <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" class="mt-4">
                    @csrf <input type="hidden" name="statut" value="confirmee">
                    <button class="btn btn-primaire">✓ Accepter la commande</button>
                </form>
            @elseif($commande->statut === \App\Enums\CommandeStatut::Confirmee)
                <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" class="mt-4">
                    @csrf <input type="hidden" name="statut" value="prete">
                    <button class="btn btn-primaire">📦 Marquer prête</button>
                </form>
            @elseif($commande->statut === \App\Enums\CommandeStatut::Assignee)
                <p class="mt-2" style="color:var(--vert-700); font-weight:600;">✅ Livreur assigné : {{ $commande->livreur?->user?->name }}</p>
            @endif
        @else
            @if($livreursDisponibles->isEmpty())
                <p class="mt-2" style="color:var(--ambre-700);">Aucun livreur disponible actuellement. Réessayez plus tard.</p>
            @else
                <div class="mt-4" style="display:grid; gap:8px;">
                    @foreach($livreursDisponibles as $livreur)
                        <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST"
                              class="rangee-entre" style="border:1px solid var(--bord-vert); border-radius:12px; padding:10px 16px;">
                            @csrf
                            <input type="hidden" name="statut" value="assignee">
                            <input type="hidden" name="livreur_id" value="{{ $livreur->id }}">
                            <span class="texte-petit">
                                <strong style="color:var(--encre);">{{ $livreur->user->name }}</strong>
                                <span class="texte-doux">· {{ $livreur->vehiculeLabel() }} {{ $livreur->immatriculation }}</span>
                                <span class="texte-doux">· ⭐ {{ number_format($livreur->note_moyenne, 1) }}</span>
                            </span>
                            <button class="btn btn-primaire btn-petit">Assigner</button>
                        </form>
                    @endforeach
                </div>
            @endif
        @endif

        @if($commande->statut === \App\Enums\CommandeStatut::EnAttente)
            <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" class="mt-4" style="border-top:1px solid var(--bord); padding-top:16px;">
                @csrf <input type="hidden" name="statut" value="refusee">
                <button class="btn btn-danger btn-petit">✗ Refuser cette commande</button>
            </form>
        @endif
    </div>

    <div class="carte carte-corps mb-6">
        <h2 class="carte-titre" style="font-size:16px;">📍 Livraison</h2>
        <p class="mt-2 texte-petit">{{ $commande->adresse_livraison }}, {{ $commande->ville_livraison }}</p>
        @if($commande->notes) <p class="mt-2 texte-petit texte-doux">📝 {{ $commande->notes }}</p> @endif
        @auth
            <a href="{{ route('messagerie.demarrer', $commande->client->user) }}" class="btn btn-secondaire btn-petit mt-4">💬 Contacter le client</a>
        @endauth
    </div>

    <a href="{{ route('pharmacie.commandes') }}" class="btn btn-secondaire btn-petit">← Toutes les commandes</a>
</div>
@endsection

@extends('layouts.app')

@section('titre', 'Commande '.$commande->numero)

@section('contenu')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black">Commande {{ $commande->numero }}</h1>
            <p class="text-sm text-slate-500">👤 {{ $commande->client?->user?->name }} · {{ $commande->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <span class="badge px-3 py-1.5 text-sm {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>
    </div>

    <div class="card p-5">
        <h2 class="mb-3 font-bold">Articles</h2>
        <ul class="divide-y divide-menthe-50 text-sm">
            @foreach($commande->lignes as $ligne)
                <li class="flex justify-between py-2">
                    <span>{{ $ligne->nom_medicament }} × {{ $ligne->quantite }}</span>
                    <span class="font-semibold">{{ \App\Support\Fcfa::montant($ligne->sous_total) }}</span>
                </li>
            @endforeach
            <li class="flex justify-between py-2 text-slate-500"><span>Livraison</span><span>{{ \App\Support\Fcfa::montant($commande->frais_livraison) }}</span></li>
            <li class="flex justify-between pt-2 text-lg font-black"><span>Total</span><span>{{ $commande->totalFormatte() }}</span></li>
        </ul>
    </div>

    @if($commande->paiement)
        <div class="card p-5 text-sm">
            <h2 class="mb-2 font-bold">Paiement</h2>
            {{ $commande->paiement->operateur->label() }} · {{ $commande->paiement->montantFormate() }}
            <span class="badge ml-2 {{ $commande->paiement->statut->value === 'reussi' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $commande->paiement->statut->label() }}</span>
            <div class="mt-1 text-xs text-slate-400">Réf. {{ $commande->paiement->reference }} · Payeur : {{ $commande->paiement->numero_payeur }}</div>
        </div>
    @endif

    <div class="card p-5">
        <h2 class="mb-3 font-bold">🛵 Assigner un livreur</h2>

        @if($commande->statut !== \App\Enums\CommandeStatut::Prete)
            <p class="text-sm text-slate-500">
                La commande doit d'abord être <strong>confirmée</strong> puis <strong>prête</strong> avant l'assignation.
            </p>
            @if($commande->statut === \App\Enums\CommandeStatut::EnAttente)
                <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" class="mt-3">
                    @csrf <input type="hidden" name="statut" value="confirmee">
                    <button class="btn-primary">✓ Accepter la commande</button>
                </form>
            @elseif($commande->statut === \App\Enums\CommandeStatut::Confirmee)
                <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" class="mt-3">
                    @csrf <input type="hidden" name="statut" value="prete">
                    <button class="btn-primary">📦 Marquer prête</button>
                </form>
            @elseif($commande->statut === \App\Enums\CommandeStatut::Assignee)
                <p class="text-sm text-emerald-700">✅ Livreur assigné : {{ $commande->livreur?->user?->name }}</p>
            @endif
        @else
            @if($livreursDisponibles->isEmpty())
                <p class="text-sm text-amber-600">Aucun livreur disponible actuellement. Réessayez plus tard.</p>
            @else
                <div class="space-y-2">
                    @foreach($livreursDisponibles as $livreur)
                        <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST"
                              class="flex items-center justify-between rounded-xl border border-menthe-100 px-4 py-2.5">
                            @csrf
                            <input type="hidden" name="statut" value="assignee">
                            <input type="hidden" name="livreur_id" value="{{ $livreur->id }}">
                            <span class="text-sm">
                                <strong>{{ $livreur->user->name }}</strong>
                                <span class="text-slate-400">· {{ $livreur->vehiculeLabel() }} {{ $livreur->immatriculation }}</span>
                                <span class="text-slate-400">· ⭐ {{ number_format($livreur->note_moyenne, 1) }}</span>
                            </span>
                            <button class="btn-primary text-xs">Assigner</button>
                        </form>
                    @endforeach
                </div>
            @endif
        @endif

        @if($commande->statut === \App\Enums\CommandeStatut::EnAttente)
            <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" class="mt-4 border-t border-menthe-50 pt-4">
                @csrf <input type="hidden" name="statut" value="refusee">
                <button class="btn-danger text-sm">✗ Refuser cette commande</button>
            </form>
        @endif
    </div>

    <div class="card p-5 text-sm">
        <h2 class="mb-2 font-bold">📍 Livraison</h2>
        <p>{{ $commande->adresse_livraison }}, {{ $commande->ville_livraison }}</p>
        @if($commande->notes) <p class="mt-1 text-xs text-slate-400">📝 {{ $commande->notes }}</p> @endif
        @auth
            <a href="{{ route('messagerie.demarrer', $commande->client->user) }}" class="btn-secondary mt-3 text-sm">💬 Contacter le client</a>
        @endauth
    </div>

    <a href="{{ route('pharmacie.commandes') }}" class="btn-secondary text-sm">← Toutes les commandes</a>
</div>
@endsection

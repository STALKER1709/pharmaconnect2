@extends('layouts.app')

@section('titre', 'Commande '.$commande->numero)

@section('contenu')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black">Commande {{ $commande->numero }}</h1>
            <p class="text-sm text-slate-500">
                🏥 <a href="{{ route('public.pharmacie', $commande->pharmacie) }}" class="hover:underline text-menthe-700 font-medium">{{ $commande->pharmacie->nom }}</a>
                · {{ $commande->created_at->format('d/m/Y à H:i') }}
                @if($distanceKm !== null) · 📍 {{ $distanceKm }} km de la pharmacie @endif
            </p>
        </div>
        <span class="badge px-3 py-1.5 text-sm {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Articles --}}
            <div class="card p-5">
                <h2 class="mb-3 font-bold">Articles</h2>
                <ul class="divide-y divide-menthe-50">
                    @foreach($commande->lignes as $ligne)
                        <li class="flex justify-between py-2 text-sm">
                            <span>{{ $ligne->nom_medicament }} × {{ $ligne->quantite }}</span>
                            <span class="font-semibold">{{ \App\Support\Fcfa::montant($ligne->sous_total) }}</span>
                        </li>
                    @endforeach
                    <li class="flex justify-between py-2 text-sm text-slate-500">
                        <span>Livraison</span><span>{{ \App\Support\Fcfa::montant($commande->frais_livraison) }}</span>
                    </li>
                    <li class="flex justify-between pt-2 text-lg font-black">
                        <span>Total</span><span>{{ $commande->totalFormatte() }}</span>
                    </li>
                </ul>
            </div>

            {{-- Paiement --}}
            @if($commande->paiement)
                <div class="card p-5">
                    <h2 class="mb-3 font-bold">Paiement</h2>
                    <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                        <div>
                            <span class="badge {{ $commande->paiement->statut->value === 'reussi' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $commande->paiement->statut->label() }}</span>
                            <span class="ml-2">{{ $commande->paiement->operateur->label() }} · Réf. {{ $commande->paiement->reference }}</span>
                        </div>
                        <span class="font-black">{{ $commande->paiement->montantFormate() }}</span>
                    </div>
                </div>
            @endif

            {{-- Avis --}}
            @if($commande->statut === \App\Enums\CommandeStatut::Livree)
                <div class="card p-5">
                    <h2 class="font-bold">Votre avis</h2>
                    <form action="{{ route('commandes.avis', $commande) }}" method="POST" class="mt-3 space-y-3"
                          x-data="{ type: 'pharmacie', note: 5 }">
                        @csrf
                        <div class="flex flex-wrap gap-2">
                            @foreach(['pharmacie' => '🏥 Pharmacie', 'medicament' => '💊 Médicament', 'livreur' => '🛵 Livreur'] as $v => $l)
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="{{ $v }}" class="peer sr-only" x-model="type" @checked($loop->first)>
                                    <span class="inline-block rounded-lg border px-3 py-1.5 text-sm peer-checked:border-menthe-500 peer-checked:bg-menthe-50">{{ $l }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div x-show="type === 'medicament'" x-cloak>
                            <select name="medicament_id" class="input">
                                @foreach($commande->lignes as $ligne)
                                    <option value="{{ $ligne->medicament_id }}">{{ $ligne->nom_medicament }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-1" x-data>
                            @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer text-2xl leading-none">
                                    <input type="radio" name="note" value="{{ $i }}" class="sr-only" @checked($i === 5)>
                                    <span class="text-amber-400 peer-checked:opacity-100 opacity-40 hover:opacity-75">★</span>
                                </label>
                            @endfor
                            <span class="ml-2 text-sm text-slate-500">Note de 1 à 5</span>
                        </div>

                        <textarea name="commentaire" rows="2" class="input" placeholder="Partagez votre expérience (optionnel)…"></textarea>
                        <button class="btn-primary">Envoyer mon avis ⭐</button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <aside class="space-y-4">
            @if($commande->livraison && in_array($commande->statut->value, ['confirmee', 'prete', 'assignee', 'en_livraison']))
                <a href="{{ route('suivi.show', $commande) }}" class="btn-primary w-full">🗺️ Suivre ma livraison en direct</a>
            @endif

            @if($commande->peutEtreAnnuleeParClient())
                <form action="{{ route('commandes.annuler', $commande) }}" method="POST" onsubmit="return confirm('Annuler cette commande ?')">
                    @csrf
                    <button class="btn-danger w-full">Annuler la commande</button>
                </form>
            @endif

            @if($commande->statut === \App\Enums\CommandeStatut::EnLivraison)
                <form action="{{ route('commandes.confirmer-reception', $commande) }}" method="POST">
                    @csrf
                    <button class="btn-primary w-full">✅ Confirmer la réception</button>
                </form>
            @endif

            <div class="card p-5 text-sm">
                <h3 class="mb-2 font-bold">Contacts</h3>
                @auth
                    <a href="{{ route('messagerie.demarrer', $commande->pharmacie->user) }}" class="block rounded-lg px-2 py-1.5 hover:bg-menthe-50">💬 Écrire à la pharmacie</a>
                @endauth
                @if($commande->livreur)
                    @auth
                        <a href="{{ route('messagerie.demarrer', $commande->livreur->user) }}" class="block rounded-lg px-2 py-1.5 hover:bg-menthe-50">💬 Écrire au livreur</a>
                    @endauth
                @endif
                <a href="tel:{{ $commande->pharmacie->user->telephone }}" class="block rounded-lg px-2 py-1.5 hover:bg-menthe-50">📞 Appeler la pharmacie</a>
            </div>

            <div class="card p-5 text-sm">
                <h3 class="mb-2 font-bold">Livraison</h3>
                <p class="text-slate-600">{{ $commande->adresse_livraison }}, {{ $commande->ville_livraison }}</p>
                @if($commande->notes) <p class="mt-1 text-xs text-slate-400">📝 {{ $commande->notes }}</p> @endif
                @if($commande->livreur)
                    <p class="mt-2">🛵 Livreur : {{ $commande->livreur->user->name }} ({{ $commande->livreur->vehiculeLabel() }})</p>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection

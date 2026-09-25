@extends('layouts.app')

@section('titre', 'Commande '.$commande->numero)

@php
    use App\Enums\CommandeStatut;

    $enCours = in_array($commande->statut->value, ['confirmee', 'prete', 'assignee', 'en_livraison'], true);
    $paiement = $commande->paiement;
    $carte = 'bg-surface-container-lowest rounded-2xl p-space-md sm:p-space-lg shadow-sm';
@endphp

@section('contenu')
<div class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop pt-space-md pb-space-xl">
    <nav aria-label="Fil d'Ariane" class="flex items-center gap-2 mb-space-md font-body-sm text-body-sm text-on-surface-variant">
        <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('accueil') }}"><span class="material-symbols-outlined text-[16px]">home</span><span>Accueil</span></a>
        <span class="text-outline-variant">/</span>
        <a class="hover:text-primary transition-colors" href="{{ route('commandes.index') }}">Mes commandes</a>
        <span class="text-outline-variant">/</span>
        <span class="text-on-surface font-label-md text-label-md">#{{ $commande->numero }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md pb-space-lg mb-space-lg border-b border-surface-container">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container-low text-primary font-label-sm text-label-sm mb-space-xs">
                <span class="material-symbols-outlined text-[15px]">verified_user</span>
                <span>Commande Sécurisée &amp; Traçabilité Certifiée</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Commande #{{ $commande->numero }}</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-primary">local_pharmacy</span><a href="{{ route('public.pharmacie', $commande->pharmacie) }}" class="hover:text-primary">{{ $commande->pharmacie->nom }}</a></span>
                <span>·</span>
                <span>{{ $commande->created_at->format('d/m/Y à H\hi') }}</span>
                @if ($distanceKm !== null)
                    <span>·</span><span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-primary">distance</span>{{ $distanceKm }} km de l'officine</span>
                @endif
            </p>
        </div>
        <x-badge-statut :statut="$commande->statut" class="!text-label-lg !px-4 !py-1.5 self-start lg:self-auto"/>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-start">
        <div class="lg:col-span-7 flex flex-col gap-space-lg">
            <!-- Articles -->
            <section class="{{ $carte }}">
                <div class="flex items-center justify-between gap-space-sm pb-space-md border-b border-surface-container-low">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[22px]">inventory_2</span>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Contenu du sac scellé</h2>
                    </div>
                    <span class="font-label-md text-label-md text-on-surface-variant bg-surface-container px-2.5 py-1 rounded-lg">{{ $commande->lignes->sum('quantite') }} {{ \Illuminate\Support\Str::plural('article', $commande->lignes->sum('quantite')) }}</span>
                </div>
                <div class="divide-y divide-surface-container-low">
                    @foreach ($commande->lignes as $ligne)
                        <div class="py-space-md flex items-center justify-between gap-space-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-primary flex-shrink-0"><span class="material-symbols-outlined text-[24px]">medication</span></div>
                                <div class="min-w-0">
                                    <h3 class="font-label-lg text-label-lg text-on-surface truncate">
                                        @if ($ligne->medicament)<a href="{{ route('public.medicament', $ligne->medicament) }}" class="hover:text-primary">{{ $ligne->nom_medicament }}</a>@else{{ $ligne->nom_medicament }}@endif
                                    </h3>
                                    <p class="font-label-sm text-label-sm text-primary mt-0.5">{{ format_fcfa($ligne->prix_unitaire) }} / unité · × {{ $ligne->quantite }}</p>
                                </div>
                            </div>
                            <span class="font-currency-display text-currency-display text-on-surface whitespace-nowrap">{{ format_fcfa($ligne->sous_total) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-space-md pt-space-md border-t border-surface-container flex flex-col gap-2 font-body-md text-body-md">
                    <div class="flex items-center justify-between text-on-surface-variant"><span>Sous-total articles</span><span class="font-label-lg text-label-lg text-on-surface">{{ format_fcfa($commande->sous_total) }}</span></div>
                    <div class="flex items-center justify-between text-on-surface-variant"><span>Livraison coursier express</span><span class="font-label-lg text-label-lg text-on-surface">{{ format_fcfa($commande->frais_livraison) }}</span></div>
                    <div class="mt-3 p-space-md rounded-xl bg-surface-container-low flex items-center justify-between">
                        <div>
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider block">Montant total</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $paiement?->statut?->value === 'reussi' ? 'RÉGLÉ' : 'À RÉGLER' }}</span>
                        </div>
                        <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ format_fcfa($commande->total) }}</span>
                    </div>
                </div>
            </section>

            <!-- Paiement -->
            @if ($paiement)
                <section class="{{ $carte }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-lg {{ $paiement->operateur?->value === 'orange_money' ? 'bg-[#ff7900] text-white' : 'bg-[#ffcc00] text-black' }} flex items-center justify-center font-bold text-[13px] tracking-tighter flex-shrink-0">{{ $paiement->operateur?->value === 'orange_money' ? 'OM' : 'MoMo' }}</span>
                        <div>
                            <p class="font-label-lg text-label-lg text-on-surface">{{ $paiement->operateur?->label() }}</p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Réf. <strong class="text-on-surface">{{ $paiement->reference }}</strong>@if($paiement->paye_at) · {{ $paiement->paye_at->format('d/m/Y H:i') }}@endif</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm font-semibold self-start sm:self-auto {{ $paiement->statut?->value === 'reussi' ? 'bg-[#dcfce9] text-[#14532d]' : 'bg-tertiary-fixed text-on-tertiary-fixed' }}">
                        <span class="material-symbols-outlined text-[16px]">{{ $paiement->statut?->value === 'reussi' ? 'check_circle' : 'pending' }}</span>
                        {{ $paiement->statut?->label() }}
                    </span>
                </section>
            @endif

            <!-- Avis -->
            @if ($commande->statut === CommandeStatut::Livree)
                <section class="{{ $carte }}" x-data="{ type: 'pharmacie', note: 5 }">
                    <div class="flex items-center gap-2 pb-space-md border-b border-surface-container-low">
                        <span class="material-symbols-outlined text-primary text-[22px]">rate_review</span>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Votre avis vérifié</h2>
                    </div>
                    @if ($commande->avis->isNotEmpty())
                        <div class="mt-space-md flex flex-col gap-2">
                            @foreach ($commande->avis as $avis)
                                <div class="p-3 rounded-xl bg-surface-container-low flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-label-md text-label-md text-on-surface">{{ $avis->cibleLabel() }}</p>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant italic">« {{ $avis->commentaire ?: 'Sans commentaire' }} »</p>
                                    </div>
                                    <x-etoiles :note="$avis->note" class="text-[16px] flex-shrink-0"/>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <form action="{{ route('commandes.avis', $commande) }}" method="POST" class="mt-space-md flex flex-col gap-space-md">
                        @csrf
                        <div class="grid grid-cols-3 gap-2">
                            @foreach (['pharmacie' => ['local_pharmacy', 'Officine'], 'medicament' => ['medication', 'Médicament'], 'livreur' => ['two_wheeler', 'Coursier']] as $valeur => [$icone, $libelle])
                                @continue($valeur === 'livreur' && ! $commande->livreur)
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="{{ $valeur }}" x-model="type" class="sr-only">
                                    <span class="flex flex-col items-center gap-1 p-3 rounded-xl font-label-md text-label-md transition-all" :class="type === '{{ $valeur }}' ? 'bg-[#dcfce9] text-[#14532d] ring-2 ring-primary' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container'">
                                        <span class="material-symbols-outlined text-[22px]">{{ $icone }}</span>{{ $libelle }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <div x-show="type === 'medicament'" x-cloak>
                            <x-champ name="medicament_id" type="select" icone="medication">
                                @foreach ($commande->lignes as $ligne)
                                    <option value="{{ $ligne->medicament_id }}">{{ $ligne->nom_medicament }}</option>
                                @endforeach
                            </x-champ>
                        </div>
                        <div class="flex items-center gap-1">
                            <input type="hidden" name="note" :value="note">
                            <template x-for="i in 5" :key="i">
                                <button type="button" @click="note = i" class="text-[30px] leading-none" :class="i <= note ? 'text-[#eab308]' : 'text-outline-variant'">
                                    <span class="material-symbols-outlined icon-fill" style="font-size: 30px">star</span>
                                </button>
                            </template>
                            <span class="ml-2 font-body-sm text-body-sm text-on-surface-variant" x-text="note + ' / 5'"></span>
                        </div>
                        <x-champ name="commentaire" type="textarea" rows="2" placeholder="Partagez votre expérience (optionnel)…"/>
                        <button class="self-start inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">send</span>Envoyer mon avis
                        </button>
                    </form>
                </section>
            @endif
        </div>

        <!-- Colonne droite -->
        <div class="lg:col-span-5 flex flex-col gap-space-lg lg:sticky lg:top-24">
            @if ($commande->livraison && $enCours)
                <a href="{{ route('suivi.show', $commande) }}" class="w-full py-4 px-6 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-headline-sm font-bold flex items-center justify-center gap-3 shadow-[0px_4px_20px_-2px_rgba(20,83,45,0.2)] transition-all">
                    <span class="material-symbols-outlined text-[24px]">pin_drop</span>
                    Suivre ma livraison en direct
                </a>
            @endif
            @if ($commande->statut === CommandeStatut::EnLivraison)
                <form action="{{ route('commandes.confirmer-reception', $commande) }}" method="POST" onsubmit="return confirm('Avez-vous bien reçu le colis avec le scellé intact ?')">
                    @csrf
                    <button class="w-full py-3.5 rounded-xl bg-[#dcfce9] text-[#14532d] hover:bg-[#bbf7d0] font-label-lg text-label-lg flex items-center justify-center gap-2 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">task_alt</span>Confirmer la réception du colis
                    </button>
                </form>
            @endif

            <section class="{{ $carte }}">
                <div class="flex items-center gap-2 mb-space-sm">
                    <span class="material-symbols-outlined text-primary">local_shipping</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Livraison</h2>
                </div>
                <p class="font-body-md text-body-md text-on-surface font-medium">{{ $commande->adresse_livraison }}</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $commande->ville_livraison }}</p>
                @if ($commande->notes)
                    <div class="mt-2.5 p-2.5 rounded-xl bg-[#f0fdf6] text-[#14532d] flex items-start gap-2 font-body-sm text-body-sm whitespace-pre-line"><span class="material-symbols-outlined text-[16px] mt-0.5 flex-shrink-0">info</span>{{ $commande->notes }}</div>
                @endif
                @if ($commande->livreur)
                    <div class="mt-space-md flex items-center gap-3 p-3 rounded-xl bg-surface-container-low">
                        <div class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-lg text-label-lg">{{ initiales($commande->livreur->user->name) }}</div>
                        <div>
                            <p class="font-label-lg text-label-lg text-on-surface">{{ $commande->livreur->user->name }}</p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $commande->livreur->vehiculeLabel() }}@if($commande->livreur->immatriculation) · {{ $commande->livreur->immatriculation }}@endif</p>
                        </div>
                    </div>
                @endif
            </section>

            <section class="{{ $carte }} flex flex-col gap-2">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-primary">support_agent</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Contacts</h2>
                </div>
                <form method="POST" action="{{ route('messagerie.demarrer', $commande->pharmacie->user) }}">
                    @csrf
                    <button class="w-full flex items-center gap-2 p-3 rounded-xl bg-surface-container-low hover:bg-surface-container font-label-md text-label-md text-on-surface transition-colors"><span class="material-symbols-outlined text-primary text-[20px]">chat</span>Écrire à la pharmacie</button>
                </form>
                @if ($commande->livreur)
                    <form method="POST" action="{{ route('messagerie.demarrer', $commande->livreur->user) }}">
                        @csrf
                        <button class="w-full flex items-center gap-2 p-3 rounded-xl bg-surface-container-low hover:bg-surface-container font-label-md text-label-md text-on-surface transition-colors"><span class="material-symbols-outlined text-primary text-[20px]">two_wheeler</span>Écrire au coursier</button>
                    </form>
                @endif
                @if ($commande->pharmacie->user?->telephone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $commande->pharmacie->user->telephone) }}" class="flex items-center gap-2 p-3 rounded-xl bg-surface-container-low hover:bg-surface-container font-label-md text-label-md text-on-surface transition-colors"><span class="material-symbols-outlined text-primary text-[20px]">call</span>Appeler la pharmacie ({{ $commande->pharmacie->user->telephone }})</a>
                @endif
            </section>

            @if ($commande->peutEtreAnnuleeParClient())
                <form action="{{ route('commandes.annuler', $commande) }}" method="POST" onsubmit="return confirm('Annuler cette commande ?')">
                    @csrf
                    <button class="w-full py-3 rounded-xl bg-white border border-[#fecaca] text-[#dc2626] hover:bg-[#fef2f2] font-label-lg text-label-lg flex items-center justify-center gap-2 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">cancel</span>Annuler la commande
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

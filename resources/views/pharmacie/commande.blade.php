@extends('layouts.pharmacie')

@section('titre', 'Commande '.$commande->numero)

@php
    use App\Enums\CommandeStatut;

    $statut = $commande->statut;
    $carte = 'bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md';
    $etapes = [
        [CommandeStatut::EnAttente, 'Reçue', $commande->created_at],
        [CommandeStatut::Confirmee, 'Acceptée', $commande->confirmee_at],
        [CommandeStatut::Prete, 'Prête', $commande->prete_at],
        [CommandeStatut::Assignee, 'Coursier assigné', $commande->assignee_at],
        [CommandeStatut::EnLivraison, 'En livraison', $commande->en_livraison_at],
        [CommandeStatut::Livree, 'Livrée', $commande->livree_at],
    ];
    $ordre = collect($etapes)->pluck(0)->map->value->all();
    $index = array_search($statut->value, $ordre, true);
@endphp

@section('contenu')
<div class="flex flex-col w-full gap-space-lg">
    <x-entete-pro :titre="'Commande #'.$commande->numero" icone="receipt_long" :sous-titre="($commande->client?->user?->name ?? 'Patient').' · '.$commande->created_at->format('d/m/Y à H\hi')">
        <a href="{{ route('pharmacie.commandes') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg"><span class="material-symbols-outlined text-[20px] text-outline">arrow_back</span>Toutes les commandes</a>
        <x-badge-statut :statut="$statut" class="!text-label-lg !px-4 !py-1.5"/>
    </x-entete-pro>

    @if ($index !== false)
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm overflow-x-auto">
            <div class="grid grid-cols-6 gap-2 min-w-[640px]">
                @foreach ($etapes as $i => [$etat, $libelle, $date])
                    <div class="flex flex-col items-center text-center {{ $i > $index ? 'opacity-50' : '' }}">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center mb-1.5 {{ $i < $index ? 'bg-[#dcfce9] text-primary' : ($i === $index ? 'bg-primary text-on-primary' : 'bg-surface-container text-outline') }}">
                            <span class="material-symbols-outlined text-[18px]">{{ $i < $index ? 'check' : ($i === $index ? 'radio_button_checked' : 'circle') }}</span>
                        </div>
                        <span class="font-label-md text-label-md {{ $i === $index ? 'text-primary font-bold' : 'text-on-surface' }}">{{ $libelle }}</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $date?->format('H:i') ?? '—' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <div class="lg:col-span-8 flex flex-col gap-space-lg">
            <section class="{{ $carte }}">
                <div class="flex items-center justify-between">
                    <h2 class="font-headline-md text-headline-md text-on-surface">Articles à préparer</h2>
                    <span class="font-label-md text-label-md text-on-surface-variant bg-surface-container px-2.5 py-1 rounded-lg">{{ $commande->lignes->sum('quantite') }} {{ \Illuminate\Support\Str::plural('article', $commande->lignes->sum('quantite')) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md">
                                <th class="py-3 px-4 rounded-l-lg">Médicament</th>
                                <th class="py-3 px-4 text-center">Quantité</th>
                                <th class="py-3 px-4 text-right">Prix unitaire</th>
                                <th class="py-3 px-4 text-right rounded-r-lg">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container font-body-md text-body-md">
                            @foreach ($commande->lignes as $ligne)
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-primary"><span class="material-symbols-outlined text-[20px]">medication</span></div>
                                            <div>
                                                <p class="font-label-lg text-label-lg text-on-surface">{{ $ligne->nom_medicament }}</p>
                                                @if ($ligne->medicament?->ordonnance_obligatoire)<p class="font-label-sm text-label-sm text-tertiary">Sur ordonnance</p>@endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-label-lg text-label-lg">× {{ $ligne->quantite }}</td>
                                    <td class="py-3 px-4 text-right text-on-surface-variant whitespace-nowrap">{{ format_fcfa($ligne->prix_unitaire) }}</td>
                                    <td class="py-3 px-4 text-right font-currency-display text-currency-display whitespace-nowrap">{{ format_fcfa($ligne->sous_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col gap-2 font-body-md text-body-md pt-2">
                    <div class="flex justify-between text-on-surface-variant"><span>Frais de livraison</span><span class="text-on-surface">{{ format_fcfa($commande->frais_livraison) }}</span></div>
                    <div class="p-space-md rounded-xl bg-surface-container-low flex items-center justify-between">
                        <span class="font-headline-sm text-headline-sm text-on-surface">Total</span>
                        <span class="font-headline-lg text-headline-lg text-primary">{{ format_fcfa($commande->total) }}</span>
                    </div>
                </div>
            </section>

            @if ($commande->paiement)
                <section class="{{ $carte }}">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Paiement Mobile Money</h2>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-lg {{ $commande->paiement->operateur?->value === 'orange_money' ? 'bg-[#ff7900] text-white' : 'bg-[#ffcc00] text-black' }} flex items-center justify-center font-bold text-[13px] tracking-tighter">{{ $commande->paiement->operateur?->value === 'orange_money' ? 'OM' : 'MoMo' }}</span>
                            <div>
                                <p class="font-label-lg text-label-lg text-on-surface">{{ $commande->paiement->operateur?->label() }} · {{ format_fcfa($commande->paiement->montant) }}</p>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Réf. {{ $commande->paiement->reference }} · Payeur : {{ $commande->paiement->numero_payeur }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm font-semibold {{ $commande->paiement->statut?->value === 'reussi' ? 'bg-[#dcfce9] text-[#14532d]' : 'bg-tertiary-fixed text-on-tertiary-fixed' }}">{{ $commande->paiement->statut?->label() }}</span>
                    </div>
                </section>
            @endif
        </div>

        <div class="lg:col-span-4 flex flex-col gap-space-lg">
            <section class="{{ $carte }}">
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Traitement</h2>
                @if ($statut === CommandeStatut::EnAttente)
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Vérifiez la disponibilité des produits puis acceptez la commande.</p>
                    <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST">
                        @csrf <input type="hidden" name="statut" value="confirmee">
                        <button class="w-full py-3 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg inline-flex items-center justify-center gap-2 shadow-sm"><span class="material-symbols-outlined text-[20px]">check_circle</span>Accepter la commande</button>
                    </form>
                    <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" onsubmit="return confirm('Refuser cette commande ? Le stock sera restitué.')">
                        @csrf <input type="hidden" name="statut" value="refusee">
                        <button class="w-full py-3 rounded-xl bg-white border border-[#fecaca] text-[#dc2626] hover:bg-[#fef2f2] font-label-lg text-label-lg inline-flex items-center justify-center gap-2"><span class="material-symbols-outlined text-[20px]">cancel</span>Refuser</button>
                    </form>
                @elseif ($statut === CommandeStatut::Confirmee)
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Préparez et scellez le sac isotherme, puis signalez qu'il est prêt.</p>
                    <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST">
                        @csrf <input type="hidden" name="statut" value="prete">
                        <button class="w-full py-3 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg inline-flex items-center justify-center gap-2 shadow-sm"><span class="material-symbols-outlined text-[20px]">inventory_2</span>Marquer prête</button>
                    </form>
                @elseif ($statut === CommandeStatut::Prete)
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Confiez le colis à un coursier disponible (ou attendez qu'un coursier l'accepte).</p>
                    @forelse ($livreursDisponibles as $livreur)
                        <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" class="p-3 rounded-xl bg-surface-container-low flex items-center justify-between gap-3">
                            @csrf
                            <input type="hidden" name="statut" value="assignee">
                            <input type="hidden" name="livreur_id" value="{{ $livreur->id }}">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-md text-label-md flex-shrink-0">{{ initiales($livreur->user->name) }}</div>
                                <div class="min-w-0">
                                    <p class="font-label-md text-label-md text-on-surface truncate">{{ $livreur->user->name }}</p>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $livreur->vehiculeLabel() }} · ★ {{ number_format((float) $livreur->note_moyenne, 1) }}</p>
                                </div>
                            </div>
                            <button class="px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md shadow-sm">Assigner</button>
                        </form>
                    @empty
                        <div class="p-3 rounded-xl bg-tertiary-fixed text-on-tertiary-fixed font-body-sm text-body-sm">Aucun coursier disponible actuellement. La course reste visible par les coursiers en ligne.</div>
                    @endforelse
                @elseif ($commande->livreur)
                    <div class="p-3 rounded-xl bg-[#dcfce9] text-[#14532d] flex items-center gap-2 font-body-md text-body-md"><span class="material-symbols-outlined">two_wheeler</span>Coursier : <strong>{{ $commande->livreur->user->name }}</strong></div>
                @else
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Aucune action requise pour cette commande.</p>
                @endif
            </section>

            <section class="{{ $carte }}">
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Livraison</h2>
                <div class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-tertiary-container text-[20px]">pin_drop</span>
                    <div>
                        <p class="font-body-md text-body-md text-on-surface font-medium">{{ $commande->adresse_livraison }}</p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $commande->ville_livraison }}</p>
                    </div>
                </div>
                @if ($commande->notes)
                    <div class="p-2.5 rounded-xl bg-[#f0fdf6] text-[#14532d] font-body-sm text-body-sm whitespace-pre-line">{{ $commande->notes }}</div>
                @endif
                @if ($commande->client?->user)
                    <form method="POST" action="{{ route('messagerie.demarrer', $commande->client->user) }}">
                        @csrf
                        <button class="w-full flex items-center gap-2 p-3 rounded-xl bg-surface-container-low hover:bg-surface-container font-label-md text-label-md text-on-surface transition-colors"><span class="material-symbols-outlined text-primary text-[20px]">chat</span>Contacter le patient</button>
                    </form>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

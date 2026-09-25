@extends('layouts.pharmacie')

@section('titre', 'Statistiques')

@push('scripts')
<script type="module">
    PharmaConnect.graphiqueCA(document.getElementById('graph-ca-mois'), @json($caParMois->pluck('mois')), @json($caParMois->pluck('total')));
    PharmaConnect.graphiqueStatuts(document.getElementById('graph-statuts'), @json($parStatut->keys()->map(fn ($s) => \App\Enums\CommandeStatut::from($s)->label())), @json($parStatut->values()));
    PharmaConnect.graphiqueBarres(document.getElementById('graph-top'), @json($topMedicaments->pluck('nom_medicament')), @json($topMedicaments->pluck('total_vendus')));
</script>
@endpush

@section('contenu')
<div class="flex flex-col w-full gap-space-lg">
    <x-entete-pro titre="Statistiques officinales" icone="monitoring" :sous-titre="$pharmacie->nom.' — activité des 12 derniers mois (commandes validées)'">
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg">
            <span class="material-symbols-outlined text-[20px] text-outline">print</span>Imprimer le rapport
        </button>
    </x-entete-pro>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <x-stat-pro libelle="CA total" :valeur="format_fcfa($caTotal)" icone="account_balance_wallet" couleur-valeur="text-primary"/>
        <x-stat-pro libelle="Commandes" :valeur="$nbCommandes" icone="shopping_bag" teinte="bg-surface-container text-on-surface-variant"/>
        <x-stat-pro libelle="Panier moyen" :valeur="format_fcfa($panierMoyen)" icone="shopping_cart" teinte="bg-surface-container text-on-surface-variant"/>
        <x-stat-pro libelle="Satisfaction" :valeur="number_format($noteMoyenne, 1, ',', ' ').' / 5'" icone="star" teinte="bg-[#fef3c7] text-[#b45309]">{{ $nbAvis }} avis patients</x-stat-pro>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
        <div class="lg:col-span-8 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface">Chiffre d'affaires mensuel</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Commandes en ligne validées (FCFA)</p>
            </div>
            <div class="h-72"><canvas id="graph-ca-mois"></canvas></div>
        </div>
        <div class="lg:col-span-4 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface">Commandes par statut</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Répartition de toutes les commandes</p>
            </div>
            <div class="h-72"><canvas id="graph-statuts"></canvas></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
        <div class="lg:col-span-7 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
            <h2 class="font-headline-md text-headline-md text-on-surface">Top médicaments (quantités)</h2>
            <div class="h-72"><canvas id="graph-top"></canvas></div>
        </div>
        <div class="lg:col-span-5 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
            <h2 class="font-headline-md text-headline-md text-on-surface">Classement par recette</h2>
            <div class="divide-y divide-surface-container">
                @forelse ($topMedicaments->sortByDesc('recette') as $top)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-7 h-7 rounded-full {{ $loop->first ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }} flex items-center justify-center font-label-sm text-label-sm flex-shrink-0">{{ $loop->iteration }}</span>
                            <div class="min-w-0">
                                <p class="font-label-lg text-label-lg text-on-surface truncate">{{ $top->nom_medicament }}</p>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $top->total_vendus }} {{ \Illuminate\Support\Str::plural('boîte', (int) $top->total_vendus) }}</p>
                            </div>
                        </div>
                        <span class="font-currency-display text-label-lg text-on-surface whitespace-nowrap">{{ format_fcfa($top->recette) }}</span>
                    </div>
                @empty
                    <p class="py-6 text-center font-body-md text-body-md text-on-surface-variant">Aucune vente enregistrée.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.pharmacie')

@section('titre', $filtreStock === 'bas' ? 'Stocks à réapprovisionner' : 'Mon catalogue')

@php
    $champTable = 'w-full px-2.5 py-1.5 rounded-lg bg-surface-container-low text-on-surface font-body-sm text-body-sm border-0 focus:ring-2 focus:ring-primary focus:outline-none';
@endphp

@section('contenu')
<div class="flex flex-col w-full gap-space-lg" x-data="{ formulaire: {{ $errors->any() ? 'true' : 'false' }} }">
    <x-entete-pro :titre="$filtreStock === 'bas' ? 'Stocks à réapprovisionner' : 'Catalogue & inventaire'" icone="{{ $filtreStock === 'bas' ? 'inventory_2' : 'medication' }}"
                  :sous-titre="$pharmacie->nom.' — prix en FCFA, stocks synchronisés avec le catalogue public'">
        @if ($filtreStock === 'bas')
            <a href="{{ route('pharmacie.medicaments') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg"><span class="material-symbols-outlined text-[20px] text-outline">list</span>Tout le catalogue</a>
        @endif
        <button type="button" @click="formulaire = ! formulaire; $nextTick(() => formulaire && document.getElementById('ajout').scrollIntoView({ behavior: 'smooth' }))" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm">
            <span class="material-symbols-outlined text-[20px]" x-text="formulaire ? 'close' : 'add_circle'">add_circle</span>
            <span x-text="formulaire ? 'Fermer le formulaire' : 'Ajouter un médicament'">Ajouter un médicament</span>
        </button>
    </x-entete-pro>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
        <x-stat-pro libelle="Références" :valeur="$nbReferences" icone="medication">Médicaments proposés en ligne</x-stat-pro>
        <a href="{{ route('pharmacie.medicaments', ['stock' => 'bas']) }}">
            <x-stat-pro libelle="Stocks bas" :valeur="$nbStockBas" icone="fmd_bad" teinte="bg-error-container text-error" couleur-valeur="text-error" class="h-full hover:shadow-md transition-all">Sous le seuil d'alerte</x-stat-pro>
        </a>
        <x-stat-pro libelle="Valeur du stock" :valeur="format_fcfa($valeurStock)" icone="account_balance_wallet">Prix de vente × quantités</x-stat-pro>
    </div>

    <!-- Formulaire d'ajout -->
    <form x-show="formulaire" x-cloak id="ajout" action="{{ route('pharmacie.medicaments.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md scroll-mt-24">
        @csrf
        <div class="flex items-center gap-2 pb-space-sm border-b border-surface-container-low">
            <span class="material-symbols-outlined text-primary text-[22px]">add_circle</span>
            <h2 class="font-headline-sm text-headline-sm text-on-surface">Nouveau médicament en rayon</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
            <x-champ label="Nom commercial *" name="nom" icone="medication" required placeholder="Paracétamol 500 mg"/>
            <x-champ label="Catégorie" name="categorie_id" type="select" icone="category">
                <option value="">—</option>
                @foreach ($categories as $categorie)
                    <option value="{{ $categorie->id }}" @selected(old('categorie_id') == $categorie->id)>{{ $categorie->nom }}</option>
                @endforeach
            </x-champ>
            <x-champ label="Laboratoire" name="fabricant" icone="factory"/>
            <x-champ label="Prix (FCFA) *" name="prix" type="number" min="0" icone="payments" required/>
            <x-champ label="Quantité *" name="quantite" type="number" min="0" icone="inventory_2" value="10" required/>
            <x-champ label="Seuil d'alerte" name="seuil_stock_bas" type="number" min="0" icone="notifications" value="5"/>
            <x-champ label="Forme" name="forme" icone="pill" placeholder="comprimé, sirop…"/>
            <x-champ label="Dosage (mg)" name="dosage_mg" type="number" min="0" icone="science"/>
            <x-champ label="Date de péremption" name="date_peremption" type="date" icone="event"/>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
            <x-champ label="Description" name="description" type="textarea" rows="2"/>
            <x-champ label="Posologie usuelle" name="posologie" type="textarea" rows="2"/>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <label class="flex items-center gap-2 font-body-md text-body-md text-on-surface cursor-pointer">
                <input type="checkbox" name="ordonnance_obligatoire" value="1" class="w-4 h-4 rounded text-primary accent-[#16a34a] focus:ring-0" @checked(old('ordonnance_obligatoire'))>
                Délivrance sur ordonnance uniquement
            </label>
            <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm"><span class="material-symbols-outlined text-[18px]">save</span>Enregistrer</button>
        </div>
    </form>

    <!-- Inventaire -->
    <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex flex-col">
                <h2 class="font-headline-md text-headline-md text-on-surface">Inventaire en rayon</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Modifiez prix, quantités et péremption directement dans le tableau.</p>
            </div>
            <form method="GET" action="{{ route('pharmacie.medicaments') }}" class="relative flex items-center">
                @if ($filtreStock)<input type="hidden" name="stock" value="{{ $filtreStock }}">@endif
                <span class="material-symbols-outlined absolute left-3 text-primary text-[20px]">search</span>
                <input name="q" value="{{ $q }}" class="pl-10 pr-4 py-2.5 rounded-xl bg-surface-container-low border-0 focus:ring-2 focus:ring-primary font-body-md text-body-md placeholder:text-outline w-64" placeholder="Filtrer par nom..."/>
            </form>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[860px]">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md">
                        <th class="py-3 px-4 rounded-l-lg">Médicament</th>
                        <th class="py-3 px-4">Prix (FCFA)</th>
                        <th class="py-3 px-4">Quantité</th>
                        <th class="py-3 px-4">Péremption</th>
                        <th class="py-3 px-4">État</th>
                        <th class="py-3 px-4 text-right rounded-r-lg">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container font-body-md text-body-md">
                    @forelse ($stocks as $stock)
                        @php $m = $stock->medicament; $theme = \App\Support\ThemeMedicament::pour($m->categorie?->nom); $formulaireId = 'stock-'.$stock->id; @endphp
                        <tr class="hover:bg-surface-container-low/60 transition-colors align-middle">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-primary flex-shrink-0"><span class="material-symbols-outlined text-[20px]">{{ $theme['icone'] }}</span></div>
                                    <div class="min-w-0">
                                        <p class="font-label-lg text-label-lg text-on-surface">{{ $m->nom }}</p>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ collect([$m->categorie?->nom, $m->forme, $m->ordonnance_obligatoire ? 'sur ordonnance' : null])->filter()->implode(' · ') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 w-32"><input form="{{ $formulaireId }}" type="number" name="prix" value="{{ $stock->prix }}" min="0" class="{{ $champTable }}" aria-label="Prix"></td>
                            <td class="py-3 px-4 w-28"><input form="{{ $formulaireId }}" type="number" name="quantite" value="{{ $stock->quantite }}" min="0" class="{{ $champTable }}" aria-label="Quantité"></td>
                            <td class="py-3 px-4 w-44"><input form="{{ $formulaireId }}" type="date" name="date_peremption" value="{{ $stock->date_peremption?->format('Y-m-d') }}" class="{{ $champTable }}" aria-label="Péremption"></td>
                            <td class="py-3 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @if ($stock->quantite <= 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-error"></span>Rupture</span>
                                    @elseif ($stock->stockBas())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>Stock bas</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary-container/40 text-on-secondary-container font-label-sm text-label-sm whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span>En stock</span>
                                    @endif
                                    @if ($stock->peremptionProche())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#fef3c7] text-[#92400e] font-label-sm text-label-sm whitespace-nowrap">Péremption proche</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form id="{{ $formulaireId }}" action="{{ route('pharmacie.stocks.update', $stock) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button class="px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md inline-flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[16px]">save</span>Enregistrer</button>
                                    </form>
                                    <form action="{{ route('pharmacie.stocks.destroy', $stock) }}" method="POST" onsubmit="return confirm('Retirer ce médicament du catalogue ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="p-1.5 rounded-lg bg-surface-container-lowest hover:bg-error-container text-error inline-flex items-center" title="Retirer du catalogue"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[36px] text-outline block mb-1">inventory_2</span>
                            {{ $filtreStock === 'bas' ? 'Aucun stock sous le seuil d\'alerte. 👍' : 'Catalogue vide — ajoutez votre premier médicament.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($stocks->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $stocks->firstItem() }} - {{ $stocks->lastItem() }} sur {{ $stocks->total() }} références</span>
                {{ $stocks->links('partials.pagination') }}
            </div>
        @endif
    </div>
</div>
@endsection

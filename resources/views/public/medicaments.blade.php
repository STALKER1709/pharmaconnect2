@extends('layouts.app')

@section('titre', 'Catalogue et recherche de médicaments')

@php
    $iconesCategories = [
        'palu' => 'coronavirus', 'antalg' => 'healing', 'antibio' => 'vaccines', 'vitamin' => 'vital_signs',
        'soin' => 'medical_services', 'respir' => 'pulmonology', 'digest' => 'fluid_med', 'hydrat' => 'water_drop',
    ];
    $iconeCategorie = function (string $nom) use ($iconesCategories) {
        foreach ($iconesCategories as $cle => $icone) {
            if (str_contains(mb_strtolower($nom), $cle)) {
                return $icone;
            }
        }

        return 'medication';
    };

    // URL courante sans un filtre donné (pastilles « Filtres actifs »)
    $sans = function (string $cle, $valeur = null) {
        $params = request()->except(['page', $cle]);
        if ($valeur !== null) {
            $params[$cle] = collect((array) request()->query($cle, []))->reject(fn ($v) => (string) $v === (string) $valeur)->values()->all();
        }

        return route('public.medicaments', $params);
    };

    $filtresActifs = collect();
    foreach ($categories->whereIn('id', $categoriesIds) as $c) {
        $filtresActifs->push([$c->nom, request()->has('categories') ? $sans('categories', $c->id) : $sans('categorie'), false]);
    }
    if ($enStock) { $filtresActifs->push(['En stock', $sans('en_stock'), false]); }
    if ($forme !== '') { $filtresActifs->push([ucfirst($forme), $sans('forme'), false]); }
    if ($prixMax > 0) { $filtresActifs->push(['≤ '.format_fcfa($prixMax), $sans('prix_max'), false]); }
    if ($quartier !== '') { $filtresActifs->push([$quartier, $sans('quartier'), true]); }
    if ($ordonnance === '1') { $filtresActifs->push(['Sur ordonnance', $sans('ordonnance'), false]); }
    if ($ordonnance === '0') { $filtresActifs->push(['Sans ordonnance', $sans('ordonnance'), false]); }
    $nbFiltres = $filtresActifs->count();
    $plafondPrix = max(1000, (int) (ceil($prixMaxGlobal / 500) * 500));
@endphp

@section('contenu')
<div class="flex flex-col w-full">
    <section class="w-full pt-space-md pb-space-lg">
        <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop">
            <nav class="flex items-center gap-2 mb-space-sm font-label-md text-label-md text-on-surface-variant">
                <a class="hover:text-primary transition-colors" href="{{ route('accueil') }}">Accueil</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="{{ route('public.medicaments') }}">Médicaments</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-on-surface font-semibold">Recherche</span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm mb-space-lg">
                <div>
                    <h1 class="font-headline-xl-mobile text-headline-xl-mobile md:font-headline-xl md:text-headline-xl text-on-surface tracking-tight">
                        Catalogue et recherche de médicaments
                    </h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant mt-1">
                        Trouvez la disponibilité immédiate de vos traitements officinaux certifiés à Douala et Yaoundé.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container text-on-surface font-label-sm text-label-sm self-start md:self-auto">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span>Officines connectées en temps réel</span>
                </div>
            </div>
            <form method="GET" action="{{ route('public.medicaments') }}" class="bg-surface-container-lowest rounded-2xl shadow-sm p-3 md:p-4">
                @foreach (request()->except(['q', 'page']) as $cle => $valeur)
                    @foreach ((array) $valeur as $v)
                        <input type="hidden" name="{{ is_array($valeur) ? $cle.'[]' : $cle }}" value="{{ $v }}">
                    @endforeach
                @endforeach
                <div class="flex items-center gap-3 bg-[#f0fdf6] rounded-xl px-4 py-3">
                    <span class="material-symbols-outlined text-primary text-[24px]">search</span>
                    <input name="q" class="w-full bg-transparent border-none outline-none focus:ring-0 p-0 font-body-lg text-body-lg text-on-surface placeholder:text-outline" placeholder="Rechercher par nom de molécule, spécialité (ex: Paracétamol, Coartem, Amoxicilline)…" type="text" value="{{ $q }}"/>
                    @if ($q !== '')
                        <a href="{{ $sans('q') }}" class="p-1 rounded-full text-on-surface-variant hover:text-on-surface transition-colors" title="Effacer la recherche">
                            <span class="material-symbols-outlined text-[20px]">cancel</span>
                        </a>
                    @endif
                    <button type="submit" class="hidden sm:inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-colors shadow-sm">
                        Rechercher
                    </button>
                </div>
                <div class="flex items-center gap-2 mt-3 overflow-x-auto pb-1 text-nowrap">
                    <span class="font-label-sm text-label-sm text-on-surface-variant pr-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">bolt</span> Fréquents :
                    </span>
                    @foreach (['Paracétamol', 'Coartem', 'Amoxicilline', 'Ibuprofène', 'Vitamine C', 'Quinine', 'Métronidazole'] as $terme)
                        <a href="{{ route('public.medicaments', ['q' => $terme]) }}" class="px-3 py-1 rounded-full font-label-sm text-label-sm transition-colors {{ mb_strtolower($q) === mb_strtolower($terme) ? 'bg-[#dcfce9] text-[#14532d] font-semibold hover:bg-primary hover:text-on-primary' : 'bg-surface-container text-on-surface hover:bg-[#dcfce9] hover:text-[#14532d]' }}">{{ $terme }}</a>
                    @endforeach
                </div>
            </form>
        </div>
    </section>

    <section class="w-full pb-space-xl">
        <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop flex flex-col lg:flex-row gap-gutter-desktop">
            <!-- Filtres -->
            <aside class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-space-md">
                <form method="GET" action="{{ route('public.medicaments') }}" id="filtres" class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm space-y-6">
                    @if ($q !== '')<input type="hidden" name="q" value="{{ $q }}">@endif
                    @if ($tri !== 'pertinence')<input type="hidden" name="tri" value="{{ $tri }}">@endif
                    <div class="flex items-center justify-between pb-3 border-b-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[22px]">tune</span>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface">Filtres</h2>
                            @if ($nbFiltres > 0)
                                <span class="px-2 py-0.5 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm font-semibold">{{ $nbFiltres }} {{ $nbFiltres > 1 ? 'actifs' : 'actif' }}</span>
                            @endif
                        </div>
                        <a href="{{ route('public.medicaments', array_filter(['q' => $q])) }}" class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors underline underline-offset-2">
                            Réinitialiser
                        </a>
                    </div>

                    <div class="space-y-3 bg-[#f0fdf6] p-4 rounded-xl">
                        <h3 class="font-label-lg text-label-lg text-on-surface flex items-center justify-between">
                            Disponibilité immédiate
                            <span class="material-symbols-outlined text-[16px] text-primary">verified_user</span>
                        </h3>
                        <label class="flex items-center justify-between cursor-pointer pt-1">
                            <span class="font-body-md text-body-md text-on-surface">En stock uniquement</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input name="en_stock" value="1" @checked($enStock) onchange="this.form.submit()" class="sr-only peer" type="checkbox"/>
                                <div class="w-11 h-6 bg-surface-container-high peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </div>
                        </label>
                        <label class="flex items-start gap-2.5 cursor-pointer pt-1">
                            <input name="ordonnance" value="0" @checked($ordonnance === '0') onchange="this.form.submit()" class="mt-1 w-4 h-4 rounded text-primary accent-[#16a34a] focus:ring-0" type="checkbox"/>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-body-md text-body-md text-on-surface font-medium">Sans ordonnance</span>
                                    <span class="px-1.5 py-0.5 rounded-full bg-[#fef3c7] text-[#92400e] font-label-sm text-label-sm font-semibold inline-flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[11px]">nights_stay</span> Libre accès
                                    </span>
                                </div>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Délivrance immédiate au comptoir</span>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-label-lg text-label-lg text-on-surface">Catégories thérapeutiques</h3>
                            <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $categoriesIds->count() }} {{ $categoriesIds->count() > 1 ? 'sélections' : 'sélection' }}</span>
                        </div>
                        <div class="space-y-2">
                            @foreach ($categories as $categorie)
                                @php $coche = $categoriesIds->contains($categorie->id); @endphp
                                <label class="flex items-center justify-between cursor-pointer p-1 rounded-lg hover:bg-[#f0fdf6] transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <input name="categories[]" value="{{ $categorie->id }}" @checked($coche) onchange="this.form.submit()" class="w-4 h-4 rounded text-primary accent-[#16a34a] focus:ring-0" type="checkbox"/>
                                        <span class="material-symbols-outlined text-[18px] {{ $coche ? 'text-primary' : 'text-outline' }}">{{ $iconeCategorie($categorie->nom) }}</span>
                                        <span class="font-body-md text-body-md text-on-surface {{ $coche ? 'font-medium' : '' }}">{{ $categorie->nom }}</span>
                                    </div>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant bg-surface-container px-2 py-0.5 rounded-full">{{ $categorie->medicaments_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-3 pt-3" x-data="{ prix: {{ $prixMax > 0 ? $prixMax : $plafondPrix }} }">
                        <div class="flex items-center justify-between">
                            <h3 class="font-label-lg text-label-lg text-on-surface">Prix réglementé (FCFA)</h3>
                            <span class="font-label-sm text-label-sm text-primary font-semibold">MINSANTE</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="bg-surface-container px-3 py-1.5 rounded-lg text-center flex-1">
                                <span class="font-body-sm text-body-sm text-on-surface-variant block">Min</span>
                                <span class="font-currency-display text-label-lg text-on-surface">{{ format_fcfa($prixMinGlobal) }}</span>
                            </div>
                            <span class="text-on-surface-variant">—</span>
                            <div class="bg-surface-container px-3 py-1.5 rounded-lg text-center flex-1">
                                <span class="font-body-sm text-body-sm text-on-surface-variant block">Max</span>
                                <span class="font-currency-display text-label-lg text-on-surface" x-text="new Intl.NumberFormat('fr-FR').format(prix).replace(/ /g, ' ') + ' FCFA'">{{ format_fcfa($prixMax > 0 ? $prixMax : $plafondPrix) }}</span>
                            </div>
                        </div>
                        <div class="py-2">
                            <input name="prix_max" x-model="prix" onchange="this.form.submit()" class="w-full accent-[#16a34a] bg-surface-container-high h-2 rounded-lg cursor-pointer" max="{{ $plafondPrix }}" min="{{ max(100, $prixMinGlobal) }}" step="100" type="range" value="{{ $prixMax > 0 ? $prixMax : $plafondPrix }}"/>
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between">
                            <h3 class="font-label-lg text-label-lg text-on-surface">Quartier de livraison</h3>
                            <span class="font-label-sm text-label-sm text-primary font-semibold">Douala</span>
                        </div>
                        <select name="quartier" onchange="this.form.submit()" class="w-full bg-surface-container-high border-none rounded-lg px-3 py-2 font-label-md text-label-md text-on-surface cursor-pointer focus:ring-0">
                            <option value="">Tout le Grand Douala</option>
                            @foreach ($quartiers as $nomQuartier)
                                <option value="{{ $nomQuartier }}" @selected($quartier === $nomQuartier)>{{ $nomQuartier }}</option>
                            @endforeach
                        </select>
                        <div class="flex items-center gap-1.5 text-[#14532d] bg-[#dcfce9] px-2.5 py-1.5 rounded-lg">
                            <span class="material-symbols-outlined text-[16px]">moped</span>
                            <span class="font-label-sm text-label-sm font-medium">Livraison en &lt; 35 min dans ce rayon</span>
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <h3 class="font-label-lg text-label-lg text-on-surface">Forme galénique</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($formes as $f)
                                <label class="cursor-pointer">
                                    <input type="radio" name="forme" value="{{ $f }}" @checked($forme === $f) onchange="this.form.submit()" class="sr-only">
                                    <span class="inline-block px-3 py-1 rounded-lg font-label-sm text-label-sm {{ $forme === $f ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors' }}">{{ ucfirst($f) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <noscript><button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg">Appliquer</button></noscript>
                </form>

                <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-sm flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#dcfce9] text-[#14532d] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[22px]">contact_support</span>
                    </div>
                    <div>
                        <h4 class="font-label-lg text-label-lg text-on-surface">Besoin d'un conseil ?</h4>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                            Un pharmacien d'officine partenaire est joignable pour valider vos posologies.
                        </p>
                        <a class="inline-flex items-center gap-1 font-label-sm text-label-sm text-primary font-semibold mt-2 hover:underline" href="{{ route('chatbot.index') }}">
                            <span>Poser une question</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Résultats -->
            <div class="flex-1 flex flex-col gap-space-md min-w-0">
                <div class="bg-surface-container-lowest rounded-2xl p-4 md:p-5 shadow-sm space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-baseline gap-2">
                                <span class="font-headline-lg text-headline-md text-on-surface tracking-tight">{{ $medicaments->total() }} {{ \Illuminate\Support\Str::plural('médicament', $medicaments->total()) }}</span>
                                <span class="font-body-md text-body-md text-on-surface-variant">{{ $medicaments->total() > 1 ? 'trouvés' : 'trouvé' }}</span>
                            </div>
                            <p class="font-body-sm text-body-sm text-primary flex items-center gap-1 mt-0.5 font-medium">
                                <span class="material-symbols-outlined text-[15px]">storefront</span>
                                dans {{ $nbPharmacies }} {{ \Illuminate\Support\Str::plural('pharmacie', $nbPharmacies) }} {{ $nbPharmacies > 1 ? 'partenaires' : 'partenaire' }} à Douala{{ $quartiers->isNotEmpty() ? ' ('.$quartiers->take(3)->implode(', ').')' : '' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 self-end md:self-auto">
                            <form method="GET" action="{{ route('public.medicaments') }}" class="flex items-center gap-2 bg-surface-container px-3 py-1.5 rounded-xl">
                                @foreach (request()->except(['tri', 'page']) as $cle => $valeur)
                                    @foreach ((array) $valeur as $v)
                                        <input type="hidden" name="{{ is_array($valeur) ? $cle.'[]' : $cle }}" value="{{ $v }}">
                                    @endforeach
                                @endforeach
                                <span class="font-label-sm text-label-sm text-on-surface-variant whitespace-nowrap">Trier par :</span>
                                <select name="tri" onchange="this.form.submit()" class="bg-transparent border-none text-on-surface font-label-md text-label-md outline-none cursor-pointer focus:ring-0">
                                    <option value="pertinence" @selected($tri === 'pertinence')>Pertinence</option>
                                    <option value="prix_asc" @selected($tri === 'prix_asc')>Prix croissant</option>
                                    <option value="prix_desc" @selected($tri === 'prix_desc')>Prix décroissant</option>
                                </select>
                            </form>
                            <div class="hidden sm:flex items-center gap-1 bg-surface-container p-1 rounded-xl">
                                <span class="p-1.5 rounded-lg bg-surface-container-lowest text-primary shadow-xs flex" title="Vue grille">
                                    <span class="material-symbols-outlined text-[18px]">grid_view</span>
                                </span>
                                <a href="{{ route('public.pharmacies') }}" class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface transition-colors flex" title="Voir les officines">
                                    <span class="material-symbols-outlined text-[18px]">view_list</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @if ($nbFiltres > 0)
                        <div class="flex flex-wrap items-center gap-2 pt-2">
                            <span class="font-label-sm text-label-sm text-on-surface-variant mr-1">Filtres actifs :</span>
                            @foreach ($filtresActifs as [$libelle, $url, $ambre])
                                <a href="{{ $url }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $ambre ? 'bg-[#fef3c7] text-[#92400e]' : 'bg-[#f0fdf6] text-[#14532d]' }} font-label-sm text-label-sm hover:bg-[#fee2e2] hover:text-[#991b1b] transition-colors group">
                                    <span>{{ $libelle }}</span>
                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-gutter-desktop">
                    @forelse ($medicaments as $medicament)
                        <x-carte-medicament-catalogue :medicament="$medicament"/>
                    @empty
                        <div class="col-span-full bg-surface-container-lowest rounded-2xl p-10 shadow-sm text-center">
                            <span class="material-symbols-outlined text-[40px] text-outline">search_off</span>
                            <p class="mt-2 font-headline-sm text-headline-sm text-on-surface">Aucun médicament trouvé</p>
                            <p class="mt-1 font-body-md text-body-md text-on-surface-variant">Essayez un autre nom de molécule ou retirez des filtres.</p>
                        </div>
                    @endforelse
                </div>

                @if ($medicaments->total() > 0)
                    <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="font-body-md text-body-md text-on-surface-variant">
                            Affichage de <span class="font-semibold text-on-surface">{{ $medicaments->firstItem() }} - {{ $medicaments->lastItem() }}</span> sur <span class="font-semibold text-on-surface">{{ $medicaments->total() }}</span> médicaments
                        </div>
                        {{ $medicaments->links('partials.pagination') }}
                    </div>
                @endif

                <div class="bg-[#dcfce9] rounded-2xl p-5 shadow-sm flex flex-col md:flex-row items-center gap-4 text-center md:text-left">
                    <div class="w-12 h-12 rounded-xl bg-primary text-on-primary flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[28px]">verified</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-headline-sm text-headline-sm text-[#14532d]">Conformité sanitaire et tarification encadrée</h4>
                        <p class="font-body-md text-body-md text-[#14532d]/90 mt-0.5">
                            Prix strictement réglementés par le Ministère de la Santé Publique (MINSANTE). Médicaments 100% certifiés, conservés sous chaîne du froid contrôlée et délivrés sous l'autorité exclusive d'un pharmacien inscrit à l'ONPC.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="px-3 py-1.5 rounded-xl bg-surface-container-lowest text-[#14532d] font-label-sm text-label-sm font-semibold shadow-xs">MINSANTE Certifié</span>
                        <span class="px-3 py-1.5 rounded-xl bg-surface-container-lowest text-[#14532d] font-label-sm text-label-sm font-semibold shadow-xs">ONPC Validé</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

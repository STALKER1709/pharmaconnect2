@extends('layouts.pharmacie')

@section('titre', 'Tableau de bord')

@php
    // ── Graphique en aire : CA cumulé par semaine (maquette tableau_de_bord_pharmacie) ──
    $cumul = $caParSemaine->reduce(fn ($acc, $v) => $acc->push(($acc->last() ?? 0) + $v), collect());
    $maxCa = max(1, $cumul->max());
    $pasAxe = max(1, (int) ceil($maxCa / 3 / 1000) * 1000);
    $plafond = $pasAxe * 3;
    $xs = [80, 270, 480, 660];
    $points = $cumul->values()->map(fn ($v, $i) => [$xs[$i], 210 - ($v / $plafond) * 180]);
    $ligne = 'M '.$points[0][0].' '.round($points[0][1], 1);
    for ($i = 1; $i < count($points); $i++) {
        [$x0, $y0] = $points[$i - 1];
        [$x1, $y1] = $points[$i];
        $cx = ($x0 + $x1) / 2;
        $ligne .= ' C '.$cx.' '.round($y0, 1).', '.$cx.' '.round($y1, 1).', '.$x1.' '.round($y1, 1);
    }
    $aire = $ligne.' L 660 210 L 80 210 Z';
    $kilo = fn ($v) => $v >= 1000000 ? rtrim(rtrim(number_format($v / 1000000, 1, '.', ''), '0'), '.').'M' : ($v >= 1000 ? round($v / 1000).'k' : (string) $v);

    // ── Histogramme : commandes par jour ──
    $maxJour = max(1, $commandesParJour->max('total'));
    $pic = $commandesParJour->sortByDesc('total')->first();
    $jourComplet = ['Lun' => 'Lundi', 'Mar' => 'Mardi', 'Mer' => 'Mercredi', 'Jeu' => 'Jeudi', 'Ven' => 'Vendredi', 'Sam' => 'Samedi', 'Dim' => 'Dimanche'];
    $aujourdhuiCourt = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'][now()->dayOfWeek];
@endphp

@section('contenu')
<div class="flex flex-col w-full gap-space-lg">
    <!-- En-tête -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-md bg-surface-container-lowest p-space-lg rounded-xl shadow-sm">
        <div class="flex flex-col gap-1 min-w-0">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-surface tracking-tight">Tableau de bord — {{ $pharmacie->nom }}</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-container/40 text-on-secondary-container font-label-sm text-label-sm">
                    <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                    Direct
                </span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-outline">location_on</span>
                {{ collect([$pharmacie->quartier, $pharmacie->ville])->filter()->implode(', ') }} • Synthèse d'activité en temps réel • {{ ucfirst(now()->translatedFormat('l j F Y')) }}
            </p>
        </div>
        <div class="flex items-center gap-space-sm flex-wrap">
            <a href="{{ route('pharmacie.statistiques') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[20px] text-outline">file_download</span>
                <span>Rapport &amp; statistiques</span>
            </a>
            <a href="{{ route('pharmacie.medicaments') }}#ajout" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Ajouter un médicament</span>
            </a>
        </div>
    </div>

    @if ($pharmacie->statut !== 'actif')
        <div class="p-4 rounded-xl bg-tertiary-fixed text-on-tertiary-fixed flex items-start gap-3">
            <span class="material-symbols-outlined">hourglass_top</span>
            <p class="font-body-md text-body-md">Votre officine est <strong>en attente de validation</strong> par l'administration : elle n'apparaît pas encore dans le catalogue public.</p>
        </div>
    @endif

    <!-- Indicateurs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col justify-between gap-space-md group hover:shadow-md transition-all">
            <div class="flex items-start justify-between gap-2">
                <div class="flex flex-col min-w-0">
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">CA du mois</span>
                    <span class="font-headline-lg text-headline-lg xl:font-headline-xl xl:text-headline-xl text-on-surface mt-1 whitespace-nowrap">{{ number_format($caMois, 0, ',', ' ') }} <span class="font-currency-display text-currency-display text-primary">FCFA</span></span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-secondary-container/30 flex items-center justify-center text-primary flex-shrink-0">
                    <span class="material-symbols-outlined text-[26px]">account_balance_wallet</span>
                </div>
            </div>
            <div class="flex items-center justify-between pt-2">
                <div class="inline-flex items-center gap-1 font-label-md text-label-md text-primary bg-secondary-container/40 px-2.5 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[16px]">{{ ($evolutionCa ?? 0) < 0 ? 'trending_down' : 'trending_up' }}</span>
                    <span>{{ $evolutionCa === null ? 'Aujourd\'hui : '.format_fcfa($caJour) : ($evolutionCa >= 0 ? '+' : '').str_replace('.', ',', $evolutionCa).'% vs mois précédent' }}</span>
                </div>
                <svg class="w-20 h-6 text-primary" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 80 24">
                    <path d="M 2 18 Q 20 20 30 13 T 55 9 T 78 4"></path>
                </svg>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col justify-between gap-space-md group hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Commandes</span>
                    <span class="font-headline-xl text-headline-xl text-on-surface mt-1">{{ $nbCommandesMois }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-[26px]">shopping_bag</span>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">Ce mois</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">+{{ $nbCommandesSemaine }} cette semaine · {{ $nbLivrees }} livrées</span>
            </div>
        </div>
        <a href="{{ route('pharmacie.commandes', ['statut' => 'en_attente']) }}" class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col justify-between gap-space-md group hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">En attente</span>
                    <span class="font-headline-xl text-headline-xl text-tertiary-container mt-1">{{ $nbEnAttente }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-tertiary-fixed flex items-center justify-center text-tertiary">
                    <span class="material-symbols-outlined text-[26px]">hourglass_top</span>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-2">
                @if ($nbEnAttente > 0)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                        À traiter immédiatement
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-container/40 text-on-secondary-container font-label-sm text-label-sm font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                        Tout est à jour
                    </span>
                @endif
            </div>
        </a>
        <a href="{{ route('pharmacie.medicaments', ['stock' => 'bas']) }}" class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col justify-between gap-space-md group hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Stocks bas</span>
                    <span class="font-headline-xl text-headline-xl text-error mt-1">{{ $nbStockBas }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-error-container flex items-center justify-center text-error">
                    <span class="material-symbols-outlined text-[26px]">fmd_bad</span>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-2 flex-wrap">
                @if ($nbStockBas > 0)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                        Rupture imminente
                    </span>
                    <span class="font-body-sm text-body-sm text-error">Réapprovisionnement requis</span>
                @else
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Stocks au-dessus des seuils d'alerte</span>
                @endif
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <div class="lg:col-span-8 flex flex-col gap-space-lg">
            <!-- CA hebdomadaire -->
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm">
                    <div class="flex flex-col">
                        <h2 class="font-headline-md text-headline-md text-on-surface">Chiffre d'affaires hebdomadaire</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Cumul des commandes validées du mois en cours (FCFA)</p>
                    </div>
                    <div class="inline-flex p-1 bg-surface-container-low rounded-xl gap-1">
                        <a href="{{ route('pharmacie.statistiques') }}" class="px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-on-surface font-label-md text-label-md transition-colors">7 jours</a>
                        <span class="px-3 py-1.5 rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md shadow-sm">Mois en cours</span>
                        <a href="{{ route('pharmacie.statistiques') }}" class="px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-on-surface font-label-md text-label-md transition-colors">Trimestre</a>
                    </div>
                </div>
                <div class="w-full h-64 relative mt-2 flex flex-col justify-end">
                    <svg class="w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 700 240">
                        <defs>
                            <linearGradient id="area-gradient" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#00873a" stop-opacity="0.32"></stop>
                                <stop offset="100%" stop-color="#00873a" stop-opacity="0.0"></stop>
                            </linearGradient>
                        </defs>
                        <line stroke="#dee8ff" stroke-dasharray="4 4" stroke-width="1" x1="60" x2="680" y1="30" y2="30"></line>
                        <line stroke="#dee8ff" stroke-dasharray="4 4" stroke-width="1" x1="60" x2="680" y1="90" y2="90"></line>
                        <line stroke="#dee8ff" stroke-dasharray="4 4" stroke-width="1" x1="60" x2="680" y1="150" y2="150"></line>
                        <line stroke="#e7eeff" stroke-width="1.5" x1="60" x2="680" y1="210" y2="210"></line>
                        <text class="font-label-sm text-[11px] fill-[#6e7b6c]" text-anchor="end" x="50" y="34">{{ $kilo($plafond) }}</text>
                        <text class="font-label-sm text-[11px] fill-[#6e7b6c]" text-anchor="end" x="50" y="94">{{ $kilo($pasAxe * 2) }}</text>
                        <text class="font-label-sm text-[11px] fill-[#6e7b6c]" text-anchor="end" x="50" y="154">{{ $kilo($pasAxe) }}</text>
                        <text class="font-label-sm text-[11px] fill-[#6e7b6c]" text-anchor="end" x="50" y="214">0</text>
                        <path d="{{ $aire }}" fill="url(#area-gradient)"></path>
                        <path d="{{ $ligne }}" fill="none" stroke="#00873a" stroke-linecap="round" stroke-width="3.5"></path>
                        @foreach ($points as $i => [$x, $y])
                            @if ($i < 3)
                                <circle cx="{{ $x }}" cy="{{ round($y, 1) }}" fill="#ffffff" r="5" stroke="#006b2c" stroke-width="3"></circle>
                            @else
                                <circle cx="{{ $x }}" cy="{{ round($y, 1) }}" fill="#00873a" r="6" stroke="#ffffff" stroke-width="3"></circle>
                            @endif
                        @endforeach
                    </svg>
                    <div class="flex justify-between pl-16 pr-6 pt-2 font-label-md text-label-md text-on-surface-variant">
                        @foreach ($cumul as $i => $valeur)
                            <span class="{{ $i === 3 ? 'text-primary font-semibold' : '' }}">Semaine {{ $i + 1 }} ({{ $kilo($valeur) }}{{ $i === 3 ? ' FCFA' : '' }})</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Commandes par jour -->
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex flex-col">
                        <h2 class="font-headline-md text-headline-md text-on-surface">Commandes par jour</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Fréquentation de la semaine en cours (commandes en ligne)</p>
                    </div>
                    @if ($pic['total'] > 0)
                        <span class="inline-flex items-center gap-1 text-primary font-label-md text-label-md bg-secondary-container/40 px-3 py-1 rounded-full">
                            <span class="material-symbols-outlined text-[16px]">stars</span> Pic le {{ $jourComplet[$pic['jour']] }} ({{ $pic['total'] }} cmd)
                        </span>
                    @endif
                </div>
                <div class="w-full">
                    <svg class="w-full h-48 overflow-visible" viewBox="0 0 700 180">
                        <line stroke="#f0f3ff" stroke-width="1" x1="40" x2="680" y1="20" y2="20"></line>
                        <line stroke="#f0f3ff" stroke-width="1" x1="40" x2="680" y1="70" y2="70"></line>
                        <line stroke="#f0f3ff" stroke-width="1" x1="40" x2="680" y1="120" y2="120"></line>
                        <line stroke="#dee8ff" stroke-width="1" x1="40" x2="680" y1="150" y2="150"></line>
                        @foreach ($commandesParJour as $i => $jour)
                            @php
                                $hauteur = max(6, round($jour['total'] / $maxJour * 125));
                                $x = 70 + $i * 90;
                                $estPic = $jour['total'] > 0 && $jour['jour'] === $pic['jour'];
                            @endphp
                            <g class="cursor-pointer group">
                                <rect class="{{ $estPic ? '' : 'group-hover:fill-primary-container transition-colors' }}" fill="{{ $estPic ? '#00873a' : ($jour['jour'] === $aujourdhuiCourt ? '#dee8ff' : '#d8e3fb') }}" height="{{ $hauteur }}" rx="8" width="44" x="{{ $x }}" y="{{ 150 - $hauteur }}"></rect>
                                <text class="font-label-md {{ $estPic ? 'text-[13px] fill-[#006b2c] font-bold' : 'text-[12px] fill-[#111c2d] font-semibold' }}" text-anchor="middle" x="{{ $x + 22 }}" y="{{ 150 - $hauteur - 10 }}">{{ $jour['total'] }}</text>
                                <text class="font-label-md text-[12px] {{ $estPic ? 'fill-[#006b2c] font-bold' : 'fill-[#6e7b6c]' }}" text-anchor="middle" x="{{ $x + 22 }}" y="170">{{ $jour['jour'] }}</text>
                            </g>
                        @endforeach
                    </svg>
                </div>
            </div>

            <!-- Top médicaments -->
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex flex-col">
                        <h2 class="font-headline-md text-headline-md text-on-surface">Top médicaments les plus dispensés</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Classement selon le volume et la valeur générée</p>
                    </div>
                    <a href="{{ route('pharmacie.medicaments') }}" class="text-primary hover:text-primary-container font-label-md text-label-md inline-flex items-center gap-1 flex-shrink-0">
                        <span>Gestion inventaire</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md">
                                <th class="py-3 px-4 rounded-l-lg">Médicament</th>
                                <th class="py-3 px-4">Catégorie</th>
                                <th class="py-3 px-4 text-center">Vendus</th>
                                <th class="py-3 px-4 text-right">Revenus FCFA</th>
                                <th class="py-3 px-4 text-right rounded-r-lg">État du stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container font-body-md text-body-md">
                            @forelse ($topMedicaments as $top)
                                @php
                                    $stock = $stocksParNom->get($top->nom_medicament);
                                    $theme = \App\Support\ThemeMedicament::pour($stock?->medicament?->categorie?->nom);
                                    $quantite = (int) ($stock?->quantite ?? 0);
                                    $etat = $quantite <= 0 ? 'rupture' : ($stock && $stock->stockBas() ? 'bas' : 'ok');
                                @endphp
                                <tr class="hover:bg-surface-container-low/60 transition-colors">
                                    <td class="py-3.5 px-4 font-label-lg text-label-lg text-on-surface">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ ['rupture' => 'bg-error-container text-error', 'bas' => 'bg-tertiary-fixed text-tertiary', 'ok' => 'bg-surface-container text-primary'][$etat] }}">
                                                <span class="material-symbols-outlined text-[20px]">{{ $theme['icone'] }}</span>
                                            </div>
                                            <span>{{ $top->nom_medicament }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-on-surface-variant">{{ $stock?->medicament?->categorie?->nom ?? '—' }}</td>
                                    <td class="py-3.5 px-4 text-center font-label-md text-label-md text-on-surface whitespace-nowrap">{{ $top->total_vendus }} {{ \Illuminate\Support\Str::plural('boîte', (int) $top->total_vendus) }}</td>
                                    <td class="py-3.5 px-4 text-right font-currency-display text-currency-display text-on-surface whitespace-nowrap">{{ format_fcfa($top->recette) }}</td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if ($etat === 'rupture')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-error"></span>Rupture (0)</span>
                                        @elseif ($etat === 'bas')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>Stock bas ({{ $quantite }})</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-secondary-container/40 text-on-secondary-container font-label-sm text-label-sm whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span>En stock ({{ $quantite }})</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-center text-on-surface-variant">Aucune vente enregistrée pour le moment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Commandes à traiter -->
        <div class="lg:col-span-4 flex flex-col gap-space-md">
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Commandes à traiter</h2>
                        @if ($nbATraiter > 0)
                            <span class="px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold whitespace-nowrap">{{ $nbATraiter }} {{ $nbATraiter > 1 ? 'urgentes' : 'urgente' }}</span>
                        @endif
                    </div>
                    <span class="material-symbols-outlined text-outline text-[20px]">notifications_active</span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Validez la disponibilité avant prise en charge par les coursiers.</p>
                <div class="flex flex-col gap-space-md">
                    @forelse ($aTraiter as $commande)
                        @php
                            $resume = $commande->lignes->map(fn ($l) => $l->quantite.'x '.$l->nom_medicament)->implode(', ');
                            $enAttente = $commande->statut === \App\Enums\CommandeStatut::EnAttente;
                        @endphp
                        <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-3 transition-all hover:bg-surface-container">
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('pharmacie.commandes.show', $commande) }}">
                                    <div class="font-label-lg text-label-lg text-on-surface">#{{ $commande->numero }}</div>
                                    <div class="font-body-sm text-body-sm text-on-surface-variant">{{ $commande->client?->user?->name ?? 'Client' }} • {{ \Illuminate\Support\Str::limit($commande->adresse_livraison, 22) }}</div>
                                </a>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md font-label-sm text-label-sm font-medium text-right {{ $enAttente ? 'bg-secondary-container/40 text-on-secondary-container' : 'bg-tertiary-fixed text-on-tertiary-fixed' }}">
                                    {{ $enAttente ? 'En attente validation' : 'Confirmée • À préparer' }}
                                </span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-surface-container-lowest text-on-surface font-body-sm text-body-sm">{{ $resume }}</div>
                            <div class="flex flex-wrap items-center justify-between text-on-surface pt-1 gap-2">
                                <div class="flex flex-col">
                                    <span class="font-label-sm text-label-sm text-outline">{{ $commande->paiement?->operateur?->label() ?? 'Mobile Money' }}</span>
                                    <span class="font-currency-display text-currency-display text-primary whitespace-nowrap">{{ format_fcfa($commande->total) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('pharmacie.commandes.statut', $commande) }}" onsubmit="return confirm('Refuser cette commande ? Le stock sera restitué.')">
                                        @csrf
                                        <input type="hidden" name="statut" value="refusee">
                                        <button class="px-3 py-1.5 rounded-lg bg-surface-container-lowest hover:bg-error-container text-error font-label-md text-label-md inline-flex items-center gap-1 transition-colors" type="submit">
                                            <span class="material-symbols-outlined text-[16px]">close</span>
                                            Refuser
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('pharmacie.commandes.statut', $commande) }}">
                                        @csrf
                                        <input type="hidden" name="statut" value="{{ $enAttente ? 'confirmee' : 'prete' }}">
                                        <button class="px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md inline-flex items-center gap-1 shadow-sm transition-colors" type="submit">
                                            <span class="material-symbols-outlined text-[16px]">check</span>
                                            {{ $enAttente ? 'Accepter' : 'Prête' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-surface-container-low p-4 rounded-xl text-center font-body-sm text-body-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-[28px] text-primary block mb-1">task_alt</span>
                            Aucune commande en attente. Bravo !
                        </div>
                    @endforelse
                </div>
                <a class="pt-2 text-center text-primary hover:text-primary-container font-label-md text-label-md transition-colors flex items-center justify-center gap-1" href="{{ route('pharmacie.commandes') }}">
                    <span>Voir toutes les {{ $nbCommandesMois }} commandes du mois</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_right_alt</span>
                </a>
            </div>
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-3">
                <div class="flex items-center gap-2 text-on-surface font-label-lg text-label-lg">
                    <span class="material-symbols-outlined text-primary text-[20px]">local_shipping</span>
                    <span>Coursiers en patrouille ({{ collect([$pharmacie->quartier, $pharmacie->ville])->filter()->implode(' - ') }})</span>
                </div>
                <a href="{{ route('pharmacie.livraisons') }}" class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low font-body-sm text-body-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $nbLivreursDisponibles > 0 ? 'bg-primary' : 'bg-outline' }}"></span>
                        <span>{{ $nbLivreursDisponibles }} {{ \Illuminate\Support\Str::plural('livreur', $nbLivreursDisponibles) }} PharmaConnect {{ $nbLivreursDisponibles > 1 ? 'disponibles' : 'disponible' }}</span>
                    </div>
                    <span class="font-label-md text-label-md text-on-surface font-semibold">Suivi</span>
                </a>
            </div>
            @if ($peremption->isNotEmpty())
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-3">
                    <div class="flex items-center gap-2 text-on-surface font-label-lg text-label-lg">
                        <span class="material-symbols-outlined text-tertiary text-[20px]">event_busy</span>
                        <span>Péremptions proches</span>
                    </div>
                    @foreach ($peremption as $stock)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low font-body-sm text-body-sm">
                            <span class="truncate">{{ $stock->medicament?->nom }}</span>
                            <span class="font-label-md text-label-md text-tertiary">{{ $stock->date_peremption->format('m/Y') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

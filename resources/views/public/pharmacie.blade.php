@extends('layouts.app')

@section('titre', $pharmacie->nom)

@php
    $aujourdhui = now()->dayOfWeek;
    $horaireJour = $pharmacie->horaireDuJour($aujourdhui);
    $ouverte = $pharmacie->estOuverte();
    $telephone = $pharmacie->user?->telephone;
    $telephoneLien = $telephone ? preg_replace('/[^0-9+]/', '', $telephone) : null;
    $whatsapp = $telephoneLien ? 'https://wa.me/'.ltrim(str_starts_with($telephoneLien, '+') ? $telephoneLien : '237'.$telephoneLien, '+') : null;
    $adresse = collect([$pharmacie->adresse, $pharmacie->quartier])->filter()->unique()->implode(' · ');
    $estClient = auth()->user()?->estClient();
    // Jours affichés du lundi au dimanche, comme la maquette
    $ordreJours = [1, 2, 3, 4, 5, 6, 0];
    $plagesOuvrees = $pharmacie->horaires->where('ouvert', true);
    $resumePlage = $plagesOuvrees->isNotEmpty() ? $plagesOuvrees->first()->plage() : null;
@endphp

@section('contenu')
<div class="flex flex-col w-full">
    <div class="max-w-[1280px] w-full mx-auto px-margin md:px-margin-desktop py-space-md md:py-space-lg flex flex-col gap-space-lg">
        <nav aria-label="Fil d'Ariane" class="flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md overflow-x-auto whitespace-nowrap">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('accueil') }}">
                <span class="material-symbols-outlined text-[16px]">home</span>
                Accueil
            </a>
            <span class="material-symbols-outlined text-[14px] text-outline-variant">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('public.pharmacies') }}">Pharmacies de garde</a>
            <span class="material-symbols-outlined text-[14px] text-outline-variant">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('public.pharmacies', ['q' => $pharmacie->ville]) }}">{{ $pharmacie->ville }}</a>
            <span class="material-symbols-outlined text-[14px] text-outline-variant">chevron_right</span>
            <span class="text-on-surface font-label-lg text-label-lg">{{ $pharmacie->nom }}</span>
        </nav>

        <!-- En-tête officine -->
        <section class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-sm flex flex-col gap-space-md">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-space-md">
                <div class="flex flex-col sm:flex-row items-start gap-space-md flex-1">
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-secondary-container/20 flex items-center justify-center flex-shrink-0">
                        <div class="w-16 h-16 rounded-xl bg-primary text-on-primary flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-[40px] icon-fill">local_pharmacy</span>
                        </div>
                        @if ($ouverte)
                            <span class="absolute -bottom-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-primary"></span>
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="font-headline-lg text-headline-lg text-on-surface">{{ $pharmacie->nom }}</h1>
                            <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full bg-surface-container text-primary font-label-sm text-label-sm">
                                <span class="material-symbols-outlined text-[14px]">verified</span>
                                Agréée ONPC #{{ mb_strtoupper(mb_substr($pharmacie->ville, 0, 3)) }}-{{ str_pad($pharmacie->id, 3, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant">
                            Officine conventionnée ONPC &amp; MINSANTE · {{ $pharmacie->quartier ? 'Quartier '.$pharmacie->quartier.', ' : '' }}{{ $pharmacie->ville }} ({{ $pharmacie->adresse }})
                        </p>
                        <div class="flex flex-wrap items-center gap-x-space-md gap-y-2 mt-1">
                            <div class="flex items-center gap-1.5 bg-surface-container-low px-2.5 py-1 rounded-full">
                                <div class="flex text-amber-500"><span class="material-symbols-outlined text-[18px] icon-fill">star</span></div>
                                <span class="font-label-lg text-label-lg text-on-surface">{{ number_format($noteMoyenne, 1, ',', ' ') }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">({{ $nbAvis }} avis vérifiés)</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-on-surface-variant font-label-md text-label-md">
                                <span class="material-symbols-outlined text-primary text-[18px]">near_me</span>
                                <span>{{ $pharmacie->quartier ?? $pharmacie->ville }}, {{ $pharmacie->ville }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-on-surface-variant font-label-md text-label-md">
                                <span class="material-symbols-outlined text-primary text-[18px]">bolt</span>
                                <span>{{ $pharmacie->on_livraison ? 'Livraison express : ~25 min · '.format_fcfa($pharmacie->frais_livraison) : 'Retrait au comptoir uniquement' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap sm:flex-nowrap lg:flex-col xl:flex-row gap-2.5 justify-start lg:justify-end items-stretch">
                    @if ($estClient && $pharmacie->user)
                        <form method="POST" action="{{ route('messagerie.demarrer', $pharmacie->user) }}" class="flex-1 sm:flex-none flex">
                            @csrf
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-surface-container-low text-primary font-label-lg text-label-lg hover:bg-surface-container transition-colors">
                                <span class="material-symbols-outlined text-[20px]">chat</span>
                                <span>Écrire à l'officine</span>
                            </button>
                        </form>
                    @elseif ($whatsapp)
                        <a class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-surface-container-low text-primary font-label-lg text-label-lg hover:bg-surface-container transition-colors" href="{{ $whatsapp }}" target="_blank" rel="noopener">
                            <span class="material-symbols-outlined text-[20px]">chat</span>
                            <span>WhatsApp Officiel</span>
                        </a>
                    @endif
                    @if ($telephone)
                        <a class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-colors shadow-sm" href="tel:{{ $telephoneLien }}">
                            <span class="material-symbols-outlined text-[20px]">call</span>
                            <span>{{ $telephone }}</span>
                        </a>
                    @endif
                    <a class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-surface-container-high transition-colors" href="#localisation">
                        <span class="material-symbols-outlined text-[20px]">directions</span>
                        <span>Itinéraire GPS</span>
                    </a>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-space-sm pt-space-xs bg-surface-container-low/60 rounded-xl p-space-sm">
                @if ($ouverte)
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-secondary-container/30 text-on-secondary-container font-label-sm text-label-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                        <span class="font-label-lg text-label-lg">Ouverte actuellement</span>
                        @if ($horaireJour)<span class="text-on-surface-variant font-body-sm text-body-sm">· Aujourd'hui {{ $horaireJour->plage() }}</span>@endif
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-error"></span>
                        <span class="font-label-lg text-label-lg">Fermée actuellement</span>
                        @if ($resumePlage)<span class="font-body-sm text-body-sm">· Horaires habituels {{ $resumePlage }}</span>@endif
                    </div>
                @endif
                @if ($pharmacie->on_livraison)
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[16px] icon-fill">two_wheeler</span>
                        <span>Livraison à domicile par coursier PharmaConnect</span>
                    </div>
                @endif
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-sm text-label-sm">
                    <span class="material-symbols-outlined text-[16px]">health_and_safety</span>
                    <span>Dispensation sous contrôle du pharmacien titulaire</span>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
            <!-- Colonne gauche -->
            <div class="lg:col-span-7 flex flex-col gap-space-lg">
                <!-- Horaires -->
                <section class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-sm flex flex-col gap-space-md">
                    <div class="flex items-center justify-between pb-space-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[20px]">calendar_clock</span>
                            </div>
                            <h2 class="font-headline-md text-headline-md text-on-surface">Horaires d'ouverture &amp; Garde</h2>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-secondary-container/20 text-on-secondary-container font-label-sm text-label-sm">Semaine {{ now()->isoWeek() }} · {{ now()->year }}</span>
                    </div>
                    <div class="flex flex-col rounded-xl overflow-hidden bg-surface-container-low/40">
                        @foreach ($ordreJours as $i => $jour)
                            @php $h = $pharmacie->horaireDuJour($jour); $plage = $h?->plage() ?? 'Fermé'; @endphp
                            @if ($jour === $aujourdhui)
                                <div class="flex items-center justify-between px-space-md py-3 bg-secondary-container/20 text-on-surface font-label-lg text-label-lg">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                                        <span>{{ $jours[$jour] }} (Aujourd'hui)</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-primary font-bold">{{ $plage }}</span>
                                        <span class="px-2 py-0.5 rounded-full {{ $ouverte ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface' }} font-label-sm text-label-sm">{{ $ouverte ? 'Ouvert' : 'Fermé' }}</span>
                                    </div>
                                </div>
                            @elseif ($h?->estContinu())
                                <div class="flex items-center justify-between px-space-md py-3 bg-surface-container-high/60 text-on-surface font-label-md text-label-md">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[18px] text-primary">local_police</span>
                                        <span class="font-label-lg text-label-lg">{{ $jours[$jour] }} (Garde continue)</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-right">
                                        <span class="text-primary font-bold">24h / 24</span>
                                        <span class="hidden sm:inline px-2 py-0.5 rounded-full bg-surface-container-highest text-on-surface font-label-sm text-label-sm">Guichet nuit</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between px-space-md py-2.5 text-on-surface-variant font-body-md text-body-md {{ $i % 2 === 1 ? 'bg-surface-container-lowest' : '' }} hover:bg-surface-container-low transition-colors">
                                    <span>{{ $jours[$jour] }}</span>
                                    <span>{{ $plage }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="p-3.5 rounded-xl bg-surface-container-low flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-[22px] flex-shrink-0 mt-0.5">emergency</span>
                        <div class="text-on-surface-variant font-body-sm text-body-sm flex flex-col gap-1">
                            <p class="font-label-lg text-label-lg text-on-surface">Urgences nocturnes &amp; permanence ordonnances</p>
                            <p>En dehors des horaires d'ouverture, consultez la liste des pharmacies de garde de Douala ou appelez le SAMU (119). Présentation de l'ordonnance médicale requise pour les spécialités réglementées.</p>
                        </div>
                    </div>
                </section>

                <!-- Services -->
                <section class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-sm flex flex-col gap-space-md">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[20px]">medical_services</span>
                        </div>
                        <h2 class="font-headline-md text-headline-md text-on-surface">Services officinaux &amp; Prise en charge</h2>
                    </div>
                    @if ($pharmacie->description)
                        <p class="font-body-md text-body-md text-on-surface-variant">{{ $pharmacie->description }}</p>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ([
                            ['vital_signs', 'Tension artérielle', 'Prise automatisée gratuite au comptoir conseil'],
                            ['biotech', 'Dépistage TDR Paludisme', "Test rapide d'orientation diagnostique sous 15 min"],
                            ['prescriptions', 'Ordonnances PharmaConnect', 'Préparation prioritaire scellée avant retrait'],
                            ['contact_support', 'Conseils posologiques', 'Accompagnement diabète, HTA et pédiatrie'],
                        ] as [$icone, $titre, $texte])
                            <div class="p-3.5 rounded-xl bg-surface-container-low/50 flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary text-[20px] mt-0.5">{{ $icone }}</span>
                                <div class="flex flex-col">
                                    <span class="font-label-lg text-label-lg text-on-surface">{{ $titre }}</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $texte }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pt-2 flex flex-col gap-2">
                        <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Moyens de paiement acceptés au comptoir &amp; en livraison</span>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-surface-container-low text-on-surface font-label-md text-label-md"><span class="w-3 h-3 rounded-full bg-amber-400"></span><span>MTN Mobile Money (MoMo)</span></div>
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-surface-container-low text-on-surface font-label-md text-label-md"><span class="w-3 h-3 rounded-full bg-orange-500"></span><span>Orange Money Cameroun</span></div>
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-surface-container-low text-on-surface font-label-md text-label-md"><span class="material-symbols-outlined text-primary text-[18px]">payments</span><span>Espèces (FCFA)</span></div>
                        </div>
                    </div>
                </section>

                <!-- Médicaments en rayon -->
                <section class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-sm flex flex-col gap-space-md" x-data="{ recherche: '', tout: false }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined text-[20px]">pill</span>
                                </div>
                                <h2 class="font-headline-md text-headline-md text-on-surface">Médicaments phares en rayon</h2>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Inventaire direct synchronisé avec le stock physique de {{ $pharmacie->quartier ?? $pharmacie->ville }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-container/20 text-on-secondary-container font-label-sm text-label-sm self-start sm:self-auto">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            En stock direct
                        </span>
                    </div>
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                        </span>
                        <input x-model="recherche" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant/70 focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Rechercher un médicament dans cette officine..." type="text"/>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md pt-space-xs">
                        @forelse ($stocks as $stock)
                            @php $m = $stock->medicament; @endphp
                            <div class="p-4 rounded-xl bg-surface-container-low/40 hover:bg-surface-container-low flex flex-col justify-between gap-3 transition-all"
                                 x-show="(tout || {{ $loop->index }} < 6 || recherche !== '') && {{ \Illuminate\Support\Js::from(mb_strtolower($m->nom)) }}.includes(recherche.toLowerCase().trim())">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $m->categorie?->nom }}</span>
                                        <h3 class="font-headline-sm text-headline-sm text-on-surface"><a href="{{ route('public.medicament', $m) }}" class="hover:text-primary">{{ $m->nom }}</a></h3>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ ucfirst($m->forme ?? '') }}@if($m->fabricant) · {{ $m->fabricant }}@endif</p>
                                    </div>
                                    @if ($m->ordonnance_obligatoire)
                                        <span class="px-2 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-sm text-label-sm whitespace-nowrap">Sur ordonnance</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-secondary-container/30 text-on-secondary-container font-label-sm text-label-sm whitespace-nowrap">{{ $stock->quantite }} en stock</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between pt-2">
                                    <span class="font-currency-display text-currency-display text-primary">{{ format_fcfa($stock->prix) }}</span>
                                    @if ($estClient)
                                        <form method="POST" action="{{ route('panier.ajouter', $stock) }}">
                                            @csrf
                                            <input type="hidden" name="quantite" value="1">
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors shadow-sm">
                                                <span class="material-symbols-outlined text-[16px]">{{ $m->ordonnance_obligatoire ? 'prescriptions' : 'shopping_bag' }}</span>
                                                <span>Commander</span>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ auth()->check() ? route('public.medicament', $m) : route('login') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors shadow-sm">
                                            <span class="material-symbols-outlined text-[16px]">{{ $m->ordonnance_obligatoire ? 'prescriptions' : 'shopping_bag' }}</span>
                                            <span>Commander</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="sm:col-span-2 text-center font-body-md text-body-md text-on-surface-variant py-6">Aucun médicament en stock pour le moment.</p>
                        @endforelse
                    </div>
                    @if ($stocks->count() > 6)
                        <div class="pt-2 text-center" x-show="! tout && recherche === ''">
                            <button type="button" @click="tout = true" class="inline-flex items-center gap-2 font-label-lg text-label-lg text-primary hover:text-primary-container transition-colors">
                                <span>Voir l'intégralité des {{ $stocks->count() }} médicaments disponibles</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </button>
                        </div>
                    @endif
                </section>
            </div>

            <!-- Colonne droite -->
            <div class="lg:col-span-5 flex flex-col gap-space-lg">
                <section class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-sm flex flex-col gap-space-md scroll-mt-24" id="localisation">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[20px]">pin_drop</span>
                            </div>
                            <h2 class="font-headline-md text-headline-md text-on-surface">Localisation &amp; Accès</h2>
                        </div>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $pharmacie->quartier ?? $pharmacie->ville }}</span>
                    </div>
                    <div class="relative w-full h-56 rounded-xl overflow-hidden shadow-inner group bg-surface-container-low">
                        <div id="carte-pharmacie" class="w-full h-full"></div>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-[400]">
                            <div class="relative flex flex-col items-center -mt-6">
                                <div class="px-3 py-1 rounded-full bg-primary text-on-primary font-label-sm text-label-sm shadow-md flex items-center gap-1.5 whitespace-nowrap mb-1">
                                    <span class="material-symbols-outlined text-[14px]">local_pharmacy</span>
                                    <span>{{ $pharmacie->nom }}</span>
                                </div>
                                <div class="w-4 h-4 bg-primary rotate-45 -mt-2"></div>
                            </div>
                        </div>
                        <div class="absolute top-2 right-2 flex flex-col gap-1 z-[400]">
                            <button type="button" id="carte-zoom-plus" class="w-8 h-8 rounded-lg bg-surface-container-lowest text-on-surface shadow flex items-center justify-center hover:bg-surface-container-low transition-colors" title="Zoomer">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                            </button>
                            <button type="button" id="carte-zoom-moins" class="w-8 h-8 rounded-lg bg-surface-container-lowest text-on-surface shadow flex items-center justify-center hover:bg-surface-container-low transition-colors" title="Dézoomer">
                                <span class="material-symbols-outlined text-[18px]">remove</span>
                            </button>
                        </div>
                        <a class="absolute bottom-2 left-2 z-[400] px-3 py-1.5 rounded-lg bg-surface-container-lowest/90 backdrop-blur text-on-surface font-label-sm text-label-sm shadow flex items-center gap-1 hover:bg-surface-container-lowest transition-colors" href="https://www.openstreetmap.org/?mlat={{ $pharmacie->latitude ?? 4.0511 }}&mlon={{ $pharmacie->longitude ?? 9.7679 }}#map=17/{{ $pharmacie->latitude ?? 4.0511 }}/{{ $pharmacie->longitude ?? 9.7679 }}" target="_blank" rel="noopener">
                            <span class="material-symbols-outlined text-[16px] text-primary">open_in_new</span>
                            <span>Agrandir le plan</span>
                        </a>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="flex items-start gap-2.5 text-on-surface">
                            <span class="material-symbols-outlined text-primary text-[20px] flex-shrink-0 mt-0.5">location_on</span>
                            <div class="flex flex-col">
                                <span class="font-label-lg text-label-lg">{{ $pharmacie->adresse }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ collect([$pharmacie->quartier, $pharmacie->ville, 'Cameroun'])->filter()->implode(' · ') }}</span>
                            </div>
                        </div>
                        @if ($pharmacie->latitude && $pharmacie->longitude)
                            <div class="flex items-start gap-2.5 text-on-surface">
                                <span class="material-symbols-outlined text-primary text-[20px] flex-shrink-0 mt-0.5">explore</span>
                                <p class="font-body-sm text-body-sm text-on-surface-variant"><span class="font-label-md text-label-md text-on-surface">Coordonnées GPS :</span> {{ number_format($pharmacie->latitude, 4) }}, {{ number_format($pharmacie->longitude, 4) }}</p>
                            </div>
                        @endif
                        <div class="p-3.5 rounded-xl bg-secondary-container/20 flex items-start gap-3 mt-1">
                            <span class="material-symbols-outlined text-on-secondary-container text-[22px] flex-shrink-0 mt-0.5">timer</span>
                            <div class="flex flex-col">
                                <span class="font-label-lg text-label-lg text-on-secondary-container">Retrait Express Click &amp; Collect</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Votre panier ou ordonnance préparé et disponible au comptoir dédié sous 15 minutes sans file d'attente.</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Avis -->
                <section class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-sm flex flex-col gap-space-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[20px]">rate_review</span>
                            </div>
                            <h2 class="font-headline-md text-headline-md text-on-surface">Avis patients vérifiés</h2>
                        </div>
                        <span class="font-label-sm text-label-sm text-primary flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                            Conformes ONPC
                        </span>
                    </div>
                    <div class="flex items-center gap-space-md p-3.5 rounded-xl bg-surface-container-low/40">
                        <div class="flex flex-col items-center justify-center pr-3 border-r border-outline-variant/30">
                            <span class="font-headline-xl text-headline-xl text-on-surface">{{ number_format($noteMoyenne, 1, ',', ' ') }}</span>
                            <x-etoiles :note="$noteMoyenne" class="text-amber-500 my-0.5 text-[16px]"/>
                            <span class="font-body-sm text-body-sm text-on-surface-variant whitespace-nowrap">{{ $nbAvis }} avis</span>
                        </div>
                        <div class="flex-1 flex flex-col gap-1">
                            @foreach ($repartition as $n => $pourcent)
                                <div class="flex items-center gap-2 font-label-sm text-label-sm text-on-surface-variant">
                                    <span class="w-3">{{ $n }}</span>
                                    <div class="flex-1 h-2 rounded-full bg-surface-container overflow-hidden">
                                        <div class="h-full {{ $n >= 4 ? 'bg-primary' : ($n === 3 ? 'bg-primary/60' : 'bg-outline-variant') }} rounded-full" style="width: {{ $pourcent }}%;"></div>
                                    </div>
                                    <span class="w-8 text-right">{{ $pourcent }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex flex-col gap-space-sm divide-y divide-surface-container">
                        @forelse ($avis as $unAvis)
                            @php $nom = $unAvis->client?->user?->name ?? 'Patient vérifié'; $couleursAvatar = ['bg-primary/10 text-primary', 'bg-secondary-container text-on-secondary-container', 'bg-surface-container-highest text-on-surface-variant']; @endphp
                            <div class="pt-space-sm first:pt-0 flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full {{ $couleursAvatar[$loop->index % 3] }} font-label-md text-label-md flex items-center justify-center">{{ initiales($nom) }}</div>
                                        <div>
                                            <h4 class="font-label-lg text-label-lg text-on-surface">{{ $nom }}</h4>
                                            <span class="font-body-sm text-body-sm text-on-surface-variant">Patient · {{ $unAvis->client?->quartier ?? $pharmacie->ville }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <x-etoiles :note="$unAvis->note" class="text-amber-500 text-[14px]"/>
                                        <span class="font-body-sm text-body-sm text-on-surface-variant ml-1">{{ $unAvis->created_at->diffForHumans(null, true, true) }}</span>
                                    </div>
                                </div>
                                <p class="font-body-sm text-body-sm text-on-surface-variant italic">“{{ $unAvis->commentaire ?: 'Commande conforme, rien à signaler.' }}”</p>
                                @if ($unAvis->commande_id)
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-primary">
                                            <span class="material-symbols-outlined text-[14px]">verified_user</span> Achat vérifié
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="font-body-sm text-body-sm text-on-surface-variant text-center py-4">Aucun avis pour le moment.</p>
                        @endforelse
                    </div>
                    @if ($estClient)
                        <a href="{{ route('commandes.index') }}" class="pt-space-xs text-center font-label-lg text-label-lg text-primary hover:text-primary-container transition-colors">Laisser un avis vérifié après ma commande</a>
                    @endif
                </section>
            </div>
        </div>

        <aside class="bg-surface-container-lowest rounded-2xl p-space-md shadow-sm flex flex-col md:flex-row items-center justify-between gap-space-md mt-space-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-secondary-container/30 flex items-center justify-center text-primary flex-shrink-0">
                    <span class="material-symbols-outlined text-[24px]">verified</span>
                </div>
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Officine certifiée par l'Ordre National des Pharmaciens du Cameroun (ONPC)</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Traçabilité intégrale des lots pharmaceutiques, conformité aux chaînes du froid et dispensation sécurisée sous contrôle d'un pharmacien titulaire diplômé.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Licence ministérielle active :</span>
                <span class="px-3 py-1 rounded-full bg-surface-container font-label-md text-label-md text-on-surface">MINSANTE / DPH-{{ $pharmacie->created_at?->year ?? now()->year }}</span>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    const lat = {{ $pharmacie->latitude ?? 4.0511 }}, lng = {{ $pharmacie->longitude ?? 9.7679 }};
    const carte = PharmaConnect.carte('carte-pharmacie', { zoomControl: false, attributionControl: false });
    carte.setView([lat, lng], 15);
    document.getElementById('carte-zoom-plus')?.addEventListener('click', () => carte.zoomIn());
    document.getElementById('carte-zoom-moins')?.addEventListener('click', () => carte.zoomOut());
</script>
@endpush

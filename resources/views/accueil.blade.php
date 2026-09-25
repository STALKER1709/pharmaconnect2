@extends('layouts.app')

@section('titre', 'Santé & Médicaments au Cameroun')

@php
    $ombreCarte = 'shadow-[0_4px_20px_-2px_rgba(22,163,74,0.06),0_2px_6px_-1px_rgba(0,0,0,0.04)]';
@endphp

@section('contenu')
<div class="flex flex-col w-full">
    <!-- Bandeau de certification -->
    <section class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop pt-space-lg pb-space-xs">
        <div class="flex flex-col items-center text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#dcfce7] border border-[#bbf7d0] text-[#15803d] font-label-md text-label-md shadow-sm transition-all hover:bg-[#d1fae5]">
                <span class="inline-block w-2 h-2 rounded-full bg-[#16a34a] animate-pulse"></span>
                <span>Service pharmaceutique certifié à Douala &amp; Yaoundé</span>
                <span class="text-xs text-[#16a34a] font-medium hidden sm:inline">• Agréé ONPC N° 2024/CMR</span>
            </div>
        </div>
    </section>

    <!-- 1. HERO -->
    <section class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-md md:py-space-lg">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="font-headline-xl text-headline-xl md:text-[48px] md:leading-[54px] font-bold text-slate-900 tracking-tight">
                Vos médicaments, livrés à <span class="text-primary underline decoration-[#86efac] decoration-wavy decoration-2 underline-offset-8">Douala</span>
            </h1>
            <p class="mt-4 font-body-lg text-body-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Commandez directement auprès des pharmacies agréées de votre quartier. Livraison rapide et sécurisée en moins de 45 minutes avec suivi sous chaîne du froid.
            </p>

            <!-- Recherche -->
            <form method="GET" action="{{ route('public.medicaments') }}" class="mt-8 bg-surface-container-lowest rounded-2xl p-2.5 border border-slate-200 shadow-[0_10px_30px_-5px_rgba(20,83,45,0.08)] flex flex-col md:flex-row items-stretch md:items-center gap-2">
                <div class="flex items-center flex-1 px-3 py-2 text-slate-400">
                    <svg class="w-5 h-5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                    <input class="w-full bg-transparent px-3 font-body-md text-body-md text-slate-800 placeholder:text-slate-400 focus:outline-none border-0 focus:ring-0" id="searchInput" name="q" value="{{ $recherche }}" placeholder="Rechercher un médicament, une molécule ou un symptôme..." type="text"/>
                </div>
                <div class="h-6 w-px bg-slate-200 hidden md:block"></div>
                <div class="flex items-center px-3 py-2 bg-slate-50 md:bg-transparent rounded-xl md:rounded-none">
                    <span class="text-primary text-[18px] mr-1.5 flex-shrink-0 material-symbols-outlined">location_on</span>
                    <select name="quartier" class="bg-transparent font-label-md text-label-md text-slate-700 focus:outline-none cursor-pointer focus:ring-0 md:min-w-[210px]">
                        <option value="">Douala (Tout le Grand Douala)</option>
                        @foreach ($quartiers as $quartier)
                            <option value="{{ $quartier }}">{{ $quartier }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="px-6 py-3.5 bg-[#16a34a] hover:bg-[#15803d] text-white font-label-lg text-label-lg rounded-[12px] flex items-center justify-center gap-2 transition-colors shadow-sm" type="submit">
                    <span>Trouver en officine</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </button>
            </form>

            <!-- Recherches fréquentes -->
            <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-slate-500 font-label-sm text-label-sm">
                <span class="font-medium text-slate-600">Recherches fréquentes :</span>
                @foreach (['Paracétamol', 'Coartem', 'Amoxicilline', 'Vitamine C', 'Spasfon', 'Sérum physiologique'] as $terme)
                    <a href="{{ route('public.medicaments', ['q' => $terme]) }}" class="px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-[#16a34a] hover:text-[#16a34a] transition-all">{{ $terme }}</a>
                @endforeach
            </div>

            <!-- Moyens de paiement -->
            <div class="mt-6 pt-5 border-t border-[#dcfce7] flex flex-col sm:flex-row items-center justify-center gap-3">
                <span class="font-label-sm text-label-sm text-slate-600 font-medium">Règlement instantané et sans contact certifié :</span>
                <div class="flex items-center flex-wrap justify-center gap-2">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#fef08a] border border-[#fde047] text-[#713f12] rounded-full font-label-sm text-label-sm font-semibold shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#eab308]"></span>
                        <span>MTN MoMo (*126#)</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#ffedd5] border border-[#fed7aa] text-[#9a3412] rounded-full font-label-sm text-label-sm font-semibold shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#f97316]"></span>
                        <span>Orange Money (#150*)</span>
                    </div>
                    <div class="inline-flex items-center gap-1 px-3 py-1 bg-white border border-slate-200 text-slate-700 rounded-full font-label-sm text-label-sm shadow-xs">
                        <span class="material-symbols-outlined text-[16px] text-primary">payments</span>
                        <span>Espèces à la livraison</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. GARANTIES -->
    <section id="comment-ca-marche" class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-sm scroll-mt-24">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ([
                ['verified_user', 'bg-[#dcfce7] text-[#15803d]', 'Pharmacies agréées', "100% de médicaments authentiques issus de pharmaciens certifiés par l'Ordre National (ONPC)."],
                ['phonelink_ring', 'bg-[#e0f2fe] text-[#0369a1]', 'Paiement Mobile Money', 'Paiement direct sans frais additionnels via MTN MoMo ou Orange Money à la commande ou à réception.'],
                ['electric_moped', 'bg-[#fef3c7] text-[#b45309]', 'Livraison express < 45 min', 'Coursiers formés et équipés de sacs isothermes garantissant le respect strict de la chaîne du froid.'],
            ] as [$icone, $couleurs, $titre, $texte])
                <div class="bg-surface-container-lowest rounded-2xl p-5 border border-[#e2e8f0] {{ $ombreCarte }} flex items-start gap-3.5 transition-transform hover:-translate-y-0.5">
                    <div class="w-12 h-12 rounded-xl {{ $couleurs }} flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[26px]">{{ $icone }}</span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-slate-900 font-semibold">{{ $titre }}</h3>
                        <p class="mt-1 font-body-sm text-body-sm text-slate-600 leading-snug">{{ $texte }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 3. MÉDICAMENTS -->
    <section class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-xl">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 font-label-md text-label-md font-semibold text-primary uppercase tracking-wider mb-1">
                    <span class="material-symbols-outlined text-[16px]">medication</span>
                    <span>Officine numérique Douala</span>
                </div>
                <h2 class="font-headline-lg text-headline-lg font-bold text-slate-900">Médicaments disponibles en officine</h2>
                <p class="mt-1 font-body-md text-body-md text-slate-600">Prix transparents réglementés en FCFA, délivrés selon les normes pharmaceutiques nationales.</p>
            </div>
            <a class="inline-flex items-center gap-1.5 font-label-lg text-label-lg text-primary hover:text-[#15803d] font-semibold transition-colors pb-1" href="{{ route('public.medicaments') }}">
                <span>Voir tout le catalogue ({{ number_format($nbMedicaments, 0, ',', ' ') }})</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse ($medicaments as $medicament)
                <x-carte-medicament :medicament="$medicament"/>
            @empty
                <p class="col-span-full text-center font-body-md text-body-md text-slate-500 py-10">Aucun médicament ne correspond à votre recherche.</p>
            @endforelse
        </div>
    </section>

    <!-- 4. PHARMACIES PARTENAIRES -->
    <section class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-xl">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-8 gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 font-label-md text-label-md font-semibold text-primary uppercase tracking-wider mb-1">
                    <span class="material-symbols-outlined text-[16px]">local_pharmacy</span>
                    <span>Réseau conventionné</span>
                </div>
                <h2 class="font-headline-lg text-headline-lg font-bold text-slate-900">Pharmacies partenaires à Douala</h2>
                <p class="mt-1 font-body-md text-body-md text-slate-600">Commandez auprès des officines de référence certifiées avec dispensation sous la responsabilité du titulaire.</p>
            </div>
            <div class="flex items-center flex-wrap gap-1.5 bg-surface-container-lowest p-1.5 rounded-2xl border border-slate-200">
                <a href="{{ route('public.pharmacies') }}" class="px-3.5 py-1.5 rounded-xl bg-[#16a34a] text-white font-label-sm text-label-sm font-semibold transition-all">Toutes ({{ $nbPharmacies }})</a>
                @foreach ($quartiers->take(4) as $quartier)
                    <a href="{{ route('public.pharmacies', ['q' => $quartier]) }}" class="px-3 py-1.5 rounded-xl text-slate-600 hover:bg-slate-100 font-label-sm text-label-sm font-medium transition-all">{{ $quartier }}</a>
                @endforeach
                <a href="{{ route('public.pharmacies') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 font-label-sm text-label-sm font-semibold hover:bg-amber-100 transition-all">
                    <span>🌙 De garde ce soir</span>
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($pharmacies as $pharmacie)
                <x-carte-pharmacie :pharmacie="$pharmacie" :mise-en-avant="$loop->index % 3 === 1"/>
            @empty
                <p class="col-span-full text-center font-body-md text-body-md text-slate-500 py-10">Aucune pharmacie trouvée.</p>
            @endforelse
        </div>
    </section>

    <!-- 5. ORDONNANCE -->
    <section id="ordonnance" class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-md scroll-mt-24">
        <div class="bg-surface-container-lowest rounded-3xl p-8 md:p-12 border border-[#bbf7d0] shadow-[0_10px_35px_-5px_rgba(20,83,45,0.08)] flex flex-col lg:flex-row items-center justify-between gap-8 relative overflow-hidden">
            <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-[#dcfce7] opacity-40 pointer-events-none"></div>
            <div class="max-w-2xl relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#dcfce7] text-[#15803d] font-label-sm text-label-sm font-semibold mb-3">
                    <span class="material-symbols-outlined text-[16px]">document_scanner</span>
                    <span>Traitement express 10 minutes</span>
                </div>
                <h2 class="font-headline-lg text-headline-lg md:text-[34px] font-bold text-slate-900 leading-tight">
                    Vous avez une ordonnance manuscrite ?
                </h2>
                <p class="mt-3 font-body-lg text-body-lg text-slate-600 leading-relaxed">
                    Prenez-la simplement en photo avec votre smartphone. Notre pharmacien partenaire certifié déchiffre la posologie, prépare votre paquet scellé et vous envoie le devis Mobile Money instantanément.
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-4">
                    <a href="{{ auth()->check() ? route('messagerie.index') : route('register') }}" class="px-6 py-3.5 bg-[#16a34a] hover:bg-[#15803d] text-white font-label-lg text-label-lg rounded-[12px] flex items-center gap-2 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[20px]">upload_file</span>
                        <span>Téléverser mon ordonnance 📄</span>
                    </a>
                    <a href="{{ route('chatbot.index') }}" class="px-6 py-3.5 bg-white border border-[#bbf7d0] text-[#16a34a] hover:bg-[#f0fdf6] font-label-lg text-label-lg rounded-[12px] transition-all">
                        En savoir plus
                    </a>
                </div>
                <div class="mt-5 flex items-center gap-6 text-slate-500 font-label-sm text-label-sm">
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-primary">lock</span>Confidentialité médicale garantie</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-primary">check_circle</span>Validation pharmacien ONPC</span>
                </div>
            </div>
            <div class="w-full lg:w-80 flex-shrink-0 relative z-10">
                <div class="bg-[#f0fdf6] rounded-2xl p-5 border border-[#bbf7d0] shadow-sm">
                    <div class="flex items-center gap-3 border-b border-green-200/60 pb-3">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-primary shadow-xs">
                            <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                        </div>
                        <div>
                            <p class="font-label-md text-label-md font-bold text-slate-900">Devis ordonnance express</p>
                            <p class="text-xs text-slate-500">Estimé en &lt; 10 min</p>
                        </div>
                    </div>
                    <div class="py-3 space-y-2 text-xs text-slate-600 font-body-sm">
                        <div class="flex justify-between"><span>3 produits identifiés</span><span class="font-bold text-slate-800">Conforme</span></div>
                        <div class="flex justify-between"><span>Livraison Bonapriso</span><span class="font-bold text-primary">Inclus</span></div>
                        <div class="flex justify-between border-t border-green-200/60 pt-2 font-bold text-slate-900 text-sm">
                            <span>Total devis :</span>
                            <span class="text-primary font-currency-display text-[16px]">7 100 FCFA</span>
                        </div>
                    </div>
                    <div class="mt-2 w-full py-2 bg-white rounded-lg text-center font-label-sm text-primary font-bold border border-green-200 text-xs">
                        Validation immédiate par SMS / WhatsApp
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. TÉMOIGNAGES -->
    <section class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-xl">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <div class="inline-flex items-center gap-1.5 font-label-md text-label-md font-semibold text-primary uppercase tracking-wider mb-1">
                <span class="material-symbols-outlined text-[16px]">recommend</span>
                <span>Avis de nos concitoyens</span>
            </div>
            <h2 class="font-headline-lg text-headline-lg font-bold text-slate-900">Ils font confiance à PharmaConnect</h2>
            <p class="mt-1 font-body-md text-body-md text-slate-600">Des patients, médecins et pharmaciens engagés pour un accès sain et garanti aux soins à Douala.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ([
                ['Vérifié', "En pleine crise de paludisme à 23h à Bonapriso, impossible de me déplacer. J'ai trouvé la pharmacie de garde sur le site, payé via Orange Money et le livreur était là en 35 minutes chrono.", 'CM', 'bg-[#dcfce7] text-[#15803d]', 'Carine M.', 'Patiente • Bonapriso, Douala'],
                ['Médecin Praticien', "Le risque des faux médicaments est réel dans notre région. Avec PharmaConnect, j'ai la certitude que mes patients reçoivent des spécialités homologuées directement délivrées par des officines certifiées.", 'ET', 'bg-blue-100 text-blue-700', 'Dr. Emmanuel T.', "Médecin Généraliste • Clinique d'Akwa"],
                ['Vérifié', "Le service photo d'ordonnance est d'une simplicité redoutable pour mes parents âgés à Deido. Je valide le panier par MTN MoMo depuis mon bureau et ils sont livrés sans stress.", 'PK', 'bg-amber-100 text-amber-800', 'Patrick K.', 'Client régulier • Deido, Douala'],
            ] as [$badge, $texte, $initiales, $couleurs, $nom, $role])
                <div class="bg-surface-container-lowest rounded-2xl p-6 border border-[#e2e8f0] {{ $ombreCarte }} flex flex-col justify-between">
                    <div>
                        <div class="flex items-center text-amber-500 mb-3">
                            <span>★★★★★</span>
                            <span class="ml-2 text-xs font-semibold text-slate-400">{{ $badge }}</span>
                        </div>
                        <p class="font-body-md text-body-md text-slate-700 italic">"{{ $texte }}"</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $couleurs }} flex items-center justify-center font-bold">{{ $initiales }}</div>
                        <div>
                            <h4 class="font-label-md text-label-md font-bold text-slate-900">{{ $nom }}</h4>
                            <p class="text-xs text-slate-500">{{ $role }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

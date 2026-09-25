@extends('layouts.app')

@section('titre', $medicament->nom)

@php
    $theme = \App\Support\ThemeMedicament::pour($medicament->categorie?->nom);
    $ombre = 'shadow-[0px_4px_20px_-2px_rgba(20,83,45,0.05)]';
    $estClient = auth()->user()?->estClient();
    $maxBoites = $medicament->ordonnance_obligatoire ? 10 : 4;
    $offres = $stocks->map(function ($stock) use ($maxBoites) {
        $p = $stock->pharmacie;
        $horaire = $p->horaireDuJour(now()->dayOfWeek);

        return [
            'id' => $stock->id,
            'url' => route('panier.ajouter', $stock),
            'prix' => (int) $stock->prix,
            'max' => min($maxBoites, (int) $stock->quantite),
            'nom' => $p->nom,
            'lieu' => collect([$p->quartier, $p->ville])->filter()->implode(', '),
            'statut' => $p->estOuverte() ? 'Ouvert'.($horaire?->fermeture() ? " jusqu'à ".$horaire->fermeture() : '') : 'Fermé actuellement',
        ];
    })->values();
@endphp

@section('contenu')
<div class="flex flex-col w-full" x-data="{
        offres: {{ \Illuminate\Support\Js::from($offres) }},
        choix: 0,
        qte: 1,
        get offre() { return this.offres[this.choix] ?? null },
        get total() { return this.offre ? this.offre.prix * this.qte : 0 },
        fcfa(n) { return new Intl.NumberFormat('fr-FR').format(n).replace(/ /g, ' ') + ' FCFA' },
        choisir(i) { this.choix = i; this.qte = Math.min(this.qte, this.offres[i].max); document.getElementById('commande').scrollIntoView({ behavior: 'smooth', block: 'center' }) },
    }">
    <!-- Fil d'Ariane -->
    <div class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop pt-6 pb-4">
        <nav class="flex items-center gap-2 text-on-surface-variant font-label-md text-label-md flex-wrap">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('accueil') }}">
                <span class="material-symbols-outlined text-[16px]">home</span>
                Accueil
            </a>
            <span class="material-symbols-outlined text-[14px] text-outline-variant">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('public.medicaments') }}">Médicaments</a>
            @if ($medicament->categorie)
                <span class="material-symbols-outlined text-[14px] text-outline-variant">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="{{ route('public.medicaments', ['categorie' => $medicament->categorie_id]) }}">{{ $medicament->categorie->nom }}</a>
            @endif
            <span class="material-symbols-outlined text-[14px] text-outline-variant">chevron_right</span>
            <span class="text-on-surface font-label-lg text-label-lg font-medium">{{ $medicament->nom }}</span>
        </nav>
    </div>

    <div class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-start">
            <!-- Colonne gauche -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                <div class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 {{ $ombre }}">
                    <div class="flex flex-wrap items-center gap-2 mb-6">
                        @if ($offre)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm font-semibold">
                                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                                En stock en officine
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#fee2e2] text-[#991b1b] font-label-sm text-label-sm font-semibold">
                                <span class="w-2 h-2 rounded-full bg-[#dc2626]"></span>
                                En rupture
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">
                            <span class="material-symbols-outlined text-[15px] text-primary">verified</span>
                            Médicament essentiel MINSANTE
                        </span>
                        @if ($medicament->categorie)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm">{{ $medicament->categorie->nom }}</span>
                        @endif
                        @if ($medicament->ordonnance_obligatoire)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#e2e8f0] text-[#334155] font-label-sm text-label-sm font-semibold">
                                <span class="material-symbols-outlined text-[15px]">prescriptions</span>
                                Sur ordonnance
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-col md:flex-row gap-6 items-center">
                        <div class="relative w-full md:w-3/5 rounded-xl bg-surface-container-low overflow-hidden flex items-center justify-center p-6 aspect-square">
                            @if ($medicament->photo)
                                <img alt="{{ $medicament->nom }}" class="w-full h-full object-contain mix-blend-multiply transition-transform hover:scale-105 duration-300" src="{{ asset('storage/'.$medicament->photo) }}"/>
                            @else
                                <div class="w-3/4 h-3/4 rounded-2xl border {{ $theme['fond'] }} flex items-center justify-center transition-transform hover:scale-105 duration-300">
                                    <x-svg-medicament :type="$theme['svg']" class="w-2/3 h-2/3" :libelle="$medicament->dosage_mg ? ($medicament->dosage_mg >= 1000 ? ($medicament->dosage_mg / 1000).'g' : $medicament->dosage_mg) : null"/>
                                </div>
                            @endif
                            <div class="absolute bottom-3 left-3 bg-surface-container-lowest/90 backdrop-blur-sm px-2.5 py-1 rounded-lg shadow-sm text-on-surface-variant font-label-sm text-label-sm">
                                {{ $medicament->reference ? 'CIP: '.$medicament->reference : 'Réf: PC-'.str_pad($medicament->id, 5, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        <div class="w-full md:w-2/5 flex flex-col justify-between self-stretch py-1">
                            <div>
                                <span class="text-primary font-label-md text-label-md tracking-wider uppercase">{{ $medicament->fabricant ?? 'Laboratoire agréé' }}</span>
                                <h1 class="font-headline-lg text-headline-lg text-on-surface mt-1 mb-2 font-bold tracking-tight">{{ $medicament->nom }}</h1>
                                <p class="text-on-surface-variant font-body-md text-body-md mb-4">{{ $medicament->description }}</p>
                            </div>
                            <div class="bg-surface-container-low rounded-xl p-3.5 space-y-2">
                                <div class="flex justify-between items-center text-on-surface-variant font-label-sm text-label-sm gap-2">
                                    <span>Principe actif :</span>
                                    <span class="font-semibold text-on-surface text-right">{{ $medicament->nom }}</span>
                                </div>
                                <div class="flex justify-between items-center text-on-surface-variant font-label-sm text-label-sm">
                                    <span>Forme :</span>
                                    <span class="font-semibold text-on-surface">{{ ucfirst($medicament->forme ?? '—') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-on-surface-variant font-label-sm text-label-sm">
                                    <span>Dosage :</span>
                                    <span class="font-semibold text-on-surface">{{ $medicament->dosage_mg ? $medicament->dosage_mg.' mg' : '—' }}</span>
                                </div>
                                <div class="flex justify-between items-center text-on-surface-variant font-label-sm text-label-sm">
                                    <span>Homologation :</span>
                                    <span class="font-semibold text-primary">Agrément ONPC-CMR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Réglementation -->
                <div class="bg-[#fffbeb] rounded-2xl p-5 shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-[#fef3c7] flex items-center justify-center flex-shrink-0 text-[#b45309]">
                        <span class="material-symbols-outlined text-[24px]">gpp_maybe</span>
                    </div>
                    <div class="flex-1 space-y-2">
                        <h2 class="font-headline-sm text-headline-sm text-[#92400e] font-semibold">Réglementation &amp; Posologie Maximale de Sécurité</h2>
                        <p class="font-body-md text-body-md text-[#78350f] leading-relaxed">
                            @if ($medicament->ordonnance_obligatoire)
                                Médicament délivré <strong>uniquement sur ordonnance</strong>. Votre prescription sera vérifiée par le pharmacien titulaire avant la remise du colis. Respectez strictement la durée et la posologie prescrites par votre médecin.
                            @else
                                Médicament délivré en <strong>vente libre</strong> pour ce dosage. Respectez la dose quotidienne maximale indiquée sur la notice et espacez les prises. Ne pas associer à d'autres spécialités contenant le même principe actif.
                            @endif
                        </p>
                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#fde68a] text-[#78350f] font-label-sm text-label-sm font-semibold">
                                <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                Remboursable CNPS &amp; Assurances (sur ordonnance)
                            </span>
                            <span class="text-[#92400e] font-label-sm text-label-sm">Mentionnez votre numéro d'assuré lors de la validation.</span>
                        </div>
                    </div>
                </div>

                <!-- Fiche thérapeutique -->
                <div class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 {{ $ombre }} space-y-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">clinical_notes</span>
                            Fiche Thérapeutique &amp; Posologie
                        </h2>
                        <span class="text-on-surface-variant font-label-sm text-label-sm">Dernière mise à jour : {{ ucfirst($medicament->updated_at->translatedFormat('F Y')) }}</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-surface-container-low p-4 rounded-xl space-y-1.5">
                            <div class="flex items-center gap-2 text-primary font-label-lg text-label-lg">
                                <span class="material-symbols-outlined text-[20px]">schedule</span>
                                Posologie usuelle
                            </div>
                            <p class="font-body-md text-body-md text-on-surface-variant">{{ $medicament->posologie ?: 'Selon la prescription ou l\'avis de votre pharmacien.' }}</p>
                        </div>
                        <div class="bg-surface-container-low p-4 rounded-xl space-y-1.5">
                            <div class="flex items-center gap-2 text-primary font-label-lg text-label-lg">
                                <span class="material-symbols-outlined text-[20px]">child_care</span>
                                Enfants &amp; femmes enceintes
                            </div>
                            <p class="font-body-md text-body-md text-on-surface-variant">Demandez systématiquement l'avis du pharmacien : la dose est adaptée au poids et certaines formes sont réservées à l'adulte.</p>
                        </div>
                    </div>
                    <div class="space-y-4 pt-2">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-error text-[20px] mt-0.5">do_not_disturb_on</span>
                            <div>
                                <h3 class="font-label-lg text-label-lg text-on-surface font-semibold">Contre-indications majeures</h3>
                                <p class="font-body-md text-body-md text-on-surface-variant">Hypersensibilité connue au principe actif ou aux excipients. Signalez au pharmacien tout traitement en cours, une grossesse ou une maladie chronique (foie, reins).</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary text-[20px] mt-0.5">thermostat</span>
                            <div>
                                <h3 class="font-label-lg text-label-lg text-on-surface font-semibold">Conservation adaptée au climat équatorial de Douala</h3>
                                <p class="font-body-md text-body-md text-on-surface-variant">Conserver à une température ne dépassant pas 30°C dans un endroit sec à l'abri direct de l'humidité littorale. Les officines partenaires de Douala garantissent un stockage continu en zone climatisée.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : commande -->
            <div class="lg:col-span-5 lg:sticky top-24 space-y-4" id="commande">
                <div class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 {{ $ombre }} space-y-6">
                    @if ($offre)
                        <div class="flex items-baseline justify-between gap-3">
                            <div>
                                <span class="font-headline-xl text-headline-xl font-bold text-on-surface tracking-tight" x-text="fcfa(total)">{{ format_fcfa($offre->prix) }}</span>
                                <p class="text-on-surface-variant font-label-md text-label-md mt-0.5">
                                    Soit <span class="font-semibold text-on-surface" x-text="fcfa(offre.prix)">{{ format_fcfa($offre->prix) }}</span> par boîte
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm font-semibold whitespace-nowrap">
                                <span class="material-symbols-outlined text-[14px]">policy</span>
                                Prix officiel MINSANTE
                            </span>
                        </div>
                        <div class="bg-surface-container-low rounded-xl p-3.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-lg bg-surface-container-lowest flex items-center justify-center text-primary flex-shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">local_pharmacy</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-on-surface font-label-lg text-label-lg font-semibold truncate" x-text="offre.nom + ' — ' + offre.lieu">{{ $offre->pharmacie->nom }}</p>
                                    <p class="text-on-surface-variant font-label-sm text-label-sm truncate" x-text="offre.statut"></p>
                                </div>
                            </div>
                            <a class="text-primary font-label-sm text-label-sm hover:underline font-semibold flex-shrink-0" href="#pharmacies-list">Modifier</a>
                        </div>
                        <div class="flex items-center justify-between bg-surface-container-low p-2 rounded-xl">
                            <span class="text-on-surface font-label-lg text-label-lg ml-2">Quantité désirée</span>
                            <div class="flex items-center gap-3 bg-surface-container-lowest rounded-lg p-1 shadow-sm">
                                <button aria-label="Diminuer la quantité" class="w-8 h-8 rounded flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors disabled:opacity-30" type="button" @click="qte = Math.max(1, qte - 1)" :disabled="qte <= 1">
                                    <span class="material-symbols-outlined text-[18px]">remove</span>
                                </button>
                                <span class="font-headline-sm text-headline-sm font-semibold text-on-surface px-1 min-w-[20px] text-center" x-text="qte">1</span>
                                <button aria-label="Augmenter la quantité" class="w-8 h-8 rounded flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors disabled:opacity-30" type="button" @click="qte = Math.min(offre.max, qte + 1)" :disabled="qte >= offre.max">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                </button>
                            </div>
                        </div>
                        <p class="text-[11px] text-on-surface-variant text-center font-label-sm">
                            @if ($medicament->ordonnance_obligatoire)
                                Ordonnance à présenter au coursier ou au comptoir lors de la remise.
                            @else
                                Limite sanitaire : 4 boîtes maximum par commande sans ordonnance.
                            @endif
                        </p>
                        @if ($estClient)
                            <form method="POST" :action="offre.url" action="{{ route('panier.ajouter', $offre) }}">
                                @csrf
                                <input type="hidden" name="quantite" :value="qte" value="1">
                                <button class="w-full bg-[#16a34a] hover:bg-[#15803d] active:bg-[#14532d] text-white font-label-lg text-label-lg py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 shadow-[0px_4px_20px_-2px_rgba(20,83,45,0.15)] transition-all cursor-pointer" type="submit">
                                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                                    <span x-text="'Ajouter au panier • ' + fcfa(total)">Ajouter au panier • {{ format_fcfa($offre->prix) }}</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="w-full bg-[#16a34a] hover:bg-[#15803d] active:bg-[#14532d] text-white font-label-lg text-label-lg py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 shadow-[0px_4px_20px_-2px_rgba(20,83,45,0.15)] transition-all">
                                <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                                <span x-text="'Ajouter au panier • ' + fcfa(total)">Ajouter au panier • {{ format_fcfa($offre->prix) }}</span>
                            </a>
                        @endif
                        <div class="pt-2 border-t border-[#f0fdf6] space-y-2">
                            <p class="text-center font-label-sm text-label-sm text-on-surface-variant">Ou commande directe instantanée</p>
                            @if ($estClient)
                                <form method="POST" :action="offre.url" action="{{ route('panier.ajouter', $offre) }}">
                                    @csrf
                                    <input type="hidden" name="quantite" :value="qte" value="1">
                                    <input type="hidden" name="suite" value="commande">
                                    <button class="w-full bg-surface-container-lowest hover:bg-[#f0fdf6] text-on-surface border border-[#bbf7d0] font-label-lg text-label-lg py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors cursor-pointer" type="submit">
                                        <span class="material-symbols-outlined text-primary text-[20px]">bolt</span>
                                        Paiement 1-Clic via MTN MoMo / Orange Money
                                    </button>
                                </form>
                            @else
                                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="w-full bg-surface-container-lowest hover:bg-[#f0fdf6] text-on-surface border border-[#bbf7d0] font-label-lg text-label-lg py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors">
                                    <span class="material-symbols-outlined text-primary text-[20px]">bolt</span>
                                    Paiement 1-Clic via MTN MoMo / Orange Money
                                </a>
                            @endif
                            <div class="flex items-center justify-center gap-4 pt-1">
                                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#ffcc00]"></span> MTN Mobile Money</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#ff7900]"></span> Orange Money Cameroun</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center space-y-3 py-4">
                            <span class="material-symbols-outlined text-[40px] text-outline">production_quantity_limits</span>
                            <p class="font-headline-sm text-headline-sm text-on-surface">Momentanément indisponible</p>
                            <p class="font-body-md text-body-md text-on-surface-variant">Aucune officine partenaire n'a ce médicament en stock pour le moment. Revenez bientôt ou demandez conseil à PharmaBot.</p>
                            <a href="{{ route('chatbot.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-label-md text-label-md">
                                <span class="material-symbols-outlined text-[18px] text-slate-500">notifications_active</span>
                                M'alerter du retour
                            </a>
                        </div>
                    @endif
                    <div class="space-y-3 pt-4 border-t border-surface-container">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary text-[20px]">two_wheeler</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant"><strong class="text-on-surface">Livraison express 35 min :</strong> Akwa, Bonanjo, Bali, Deido, Bonapriso. Sac isotherme scellé.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary text-[20px]">verified_user</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant"><strong class="text-on-surface">Origine certifiée :</strong> Traçabilité complète ONPC, lot et date d'expiration vérifiés avant remise.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary text-[20px]">storefront</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant"><strong class="text-on-surface">Click &amp; Collect gratuit :</strong> Retrait comptoir sans file d'attente dans l'officine choisie.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-[#dcfce9] rounded-2xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined text-[20px]">phone_in_talk</span>
                        </div>
                        <div>
                            <p class="font-label-sm text-label-sm text-[#14532d] uppercase tracking-wider font-semibold">Conseil Officinal Direct</p>
                            <p class="font-label-lg text-label-lg text-[#14532d] font-bold">Un doute sur la posologie ?</p>
                        </div>
                    </div>
                    <a class="px-3 py-1.5 rounded-xl bg-white text-[#14532d] hover:bg-white/80 font-label-sm text-label-sm font-semibold transition-colors flex items-center gap-1" href="{{ route('chatbot.index') }}">Demander</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparatif des officines -->
    <section class="w-full bg-surface-container-lowest py-12 scroll-mt-24" id="pharmacies-list">
        <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm font-semibold mb-2">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        Stocks géolocalisés en temps réel
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">
                        @if ($stocks->isEmpty())
                            Aucune officine n'a ce médicament en stock
                        @else
                            Disponible immédiatement dans {{ $stocks->count() }} {{ \Illuminate\Support\Str::plural('officine', $stocks->count()) }} à Douala
                        @endif
                    </h2>
                    <p class="text-on-surface-variant font-body-md text-body-md mt-1">Sélectionnez votre officine préférée selon votre localisation ou le statut de garde.</p>
                </div>
                <div class="flex items-center gap-2 bg-surface-container-low px-4 py-2 rounded-xl text-on-surface-variant font-label-sm text-label-sm self-start md:self-auto">
                    <span class="material-symbols-outlined text-primary text-[18px]">near_me</span>
                    <span>Centré sur : <strong>Douala 1er &amp; 2ème Arrondissement</strong></span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($stocks as $stock)
                    @php $p = $stock->pharmacie; $ouverte = $p->estOuverte(); $horaire = $p->horaireDuJour(now()->dayOfWeek); @endphp
                    <div class="bg-surface rounded-2xl p-5 {{ $ombre }} hover:-translate-y-1 transition-transform flex flex-col justify-between relative overflow-hidden">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                @if ($ouverte)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                                        {{ $horaire?->fermeture() ? 'Ouvert • '.$horaire->fermeture() : 'Ouvert maintenant' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#fef3c7] text-[#92400e] font-label-sm text-label-sm font-bold">
                                        <span class="material-symbols-outlined text-[13px]">nightlight</span>
                                        Fermé
                                    </span>
                                @endif
                                <span class="text-on-surface-variant font-label-sm text-label-sm flex items-center gap-0.5">
                                    <span class="material-symbols-outlined text-[14px]">distance</span> {{ $p->quartier ?? $p->ville }}
                                </span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $p->nom }}</h3>
                                <p class="text-on-surface-variant font-body-sm text-body-sm">{{ collect([$p->quartier, $p->adresse])->filter()->unique()->implode(', ') }}</p>
                            </div>
                            <div class="bg-surface-container-lowest p-3 rounded-xl space-y-1.5">
                                <div class="flex justify-between items-center text-on-surface-variant font-label-sm text-label-sm">
                                    <span>Prix homologué :</span>
                                    <span class="font-bold text-on-surface font-currency-display">{{ format_fcfa($stock->prix) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-on-surface-variant font-label-sm text-label-sm">
                                    <span>Disponibilité :</span>
                                    <span class="text-primary font-semibold">{{ $stock->quantite }} {{ \Illuminate\Support\Str::plural('boîte', $stock->quantite) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-on-surface-variant font-label-sm text-label-sm">
                                    <span>{{ $p->on_livraison ? 'Frais de livraison :' : 'Retrait :' }}</span>
                                    <span class="text-on-surface">{{ $p->on_livraison ? format_fcfa($p->frais_livraison) : 'Au comptoir' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 space-y-2">
                            <button type="button" @click="choisir({{ $loop->index }})"
                                    :class="choix === {{ $loop->index }} ? 'bg-primary hover:bg-primary-container text-on-primary' : 'bg-surface-container-lowest border border-[#bbf7d0] hover:bg-[#f0fdf6] text-primary'"
                                    class="w-full font-label-sm text-label-sm py-2.5 rounded-xl font-semibold transition-colors flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]" x-show="choix === {{ $loop->index }}">check_circle</span>
                                Commander ici
                            </button>
                            @if ($p->user?->telephone)
                                <a class="w-full text-center text-on-surface-variant hover:text-primary font-label-sm text-label-sm py-1 block" href="tel:{{ preg_replace('/\s+/', '', $p->user->telephone) }}">Appel officine : {{ $p->user->telephone }}</a>
                            @else
                                <a class="w-full text-center text-on-surface-variant hover:text-primary font-label-sm text-label-sm py-1 block" href="{{ route('public.pharmacie', $p) }}">Voir l'officine</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 bg-surface-container-low rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-surface-container-lowest flex items-center justify-center text-primary flex-shrink-0">
                        <span class="material-symbols-outlined text-[26px]">map</span>
                    </div>
                    <div>
                        <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">Zones de livraison couverte à Douala</h4>
                        <p class="text-on-surface-variant font-body-sm text-body-sm">Akwa, Bonanjo, Bali, Bonapriso, Deido, New-Bell, Bépanda, Makepe, Bonamoussadi, Kotto.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-on-surface-variant font-label-sm text-label-sm">Tarif coursier standard : <strong>{{ format_fcfa($stocks->min(fn ($s) => $s->pharmacie->frais_livraison) ?? 1000) }}</strong></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Conseils pharmaceutiques -->
    <section class="w-full py-12">
        <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop">
            <div class="max-w-3xl mx-auto space-y-8">
                <div class="text-center space-y-2">
                    <span class="text-primary font-label-md text-label-md uppercase tracking-wider font-semibold">Conseils Pharmaceutiques</span>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Questions fréquentes sur {{ $medicament->nom }}</h2>
                    <p class="text-on-surface-variant font-body-md text-body-md">Réponses rédigées par des pharmaciens membres de l'Ordre National des Pharmaciens du Cameroun.</p>
                </div>
                <div class="space-y-4">
                    @foreach ([
                        ['medication', 'Puis-je associer ce médicament à un traitement antipaludique (ex : Coartem, Artefan) ?', "Dans la majorité des cas oui, mais seul votre pharmacien peut vérifier l'absence d'interaction avec votre traitement en cours. Précisez-le dans la messagerie lors de la commande."],
                        ['family_restroom', 'Quelle posologie pour les enfants de moins de 12 ans ?', "La dose de l'enfant se calcule selon son poids. Privilégiez les formes pédiatriques (sirop, sachet) et demandez toujours conseil avant de fractionner un comprimé adulte."],
                        ['timer', "Combien de temps pour être livré à Douala ?", "Une fois le paiement Mobile Money validé, l'officine prépare votre sac scellé et un coursier le livre en 25 à 45 minutes selon votre quartier. Vous suivez la course en temps réel."],
                    ] as [$icone, $question, $reponse])
                        <div class="bg-surface-container-lowest rounded-2xl p-6 {{ $ombre }}">
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold flex items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-primary text-[22px]">{{ $icone }}</span>
                                {{ $question }}
                            </h3>
                            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">{{ $reponse }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Avis -->
    <section class="w-full bg-surface-container-lowest py-12">
        <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10 items-center">
                <div class="lg:col-span-5 space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm font-semibold">
                        <span class="material-symbols-outlined text-[15px]">verified</span>
                        Avis vérifiés par bon de livraison Douala
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Retour d'expérience des patients</h2>
                    <p class="text-on-surface-variant font-body-md text-body-md">Tous les témoignages émanent d'utilisateurs authentifiés ayant passé commande auprès de nos officines partenaires agréées.</p>
                </div>
                <div class="lg:col-span-7 bg-surface-container-low p-6 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <span class="font-headline-xl text-headline-xl font-bold text-on-surface">{{ number_format($noteMoyenne, 1) }}</span>
                        <div>
                            <x-etoiles :note="$noteMoyenne" class="text-[20px]"/>
                            <p class="text-on-surface-variant font-label-md text-label-md mt-0.5">Basé sur {{ $nbAvis }} {{ \Illuminate\Support\Str::plural('commande', $nbAvis) }} {{ $nbAvis > 1 ? 'vérifiées' : 'vérifiée' }}</p>
                        </div>
                    </div>
                    <div class="w-full sm:w-1/2 space-y-1.5">
                        @foreach ([5, 4, 3] as $n)
                            <div class="flex items-center gap-2 text-label-sm font-label-sm text-on-surface-variant">
                                <span>{{ $n }}★</span>
                                <div class="flex-1 bg-surface-container rounded-full h-2 overflow-hidden">
                                    <div class="bg-primary h-full rounded-full" style="width: {{ $repartition[$n] }}%"></div>
                                </div>
                                <span class="w-6 text-right font-medium">{{ $repartition[$n] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($avis as $unAvis)
                    @php $nom = $unAvis->client?->user?->name ?? 'Patient vérifié'; @endphp
                    <div class="bg-surface rounded-2xl p-6 {{ $ombre }} flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <x-etoiles :note="$unAvis->note" class="text-[18px]"/>
                                <span class="text-on-surface-variant font-label-sm text-label-sm">{{ ucfirst($unAvis->created_at->diffForHumans()) }}</span>
                            </div>
                            <p class="text-on-surface font-body-md text-body-md italic leading-relaxed">"{{ $unAvis->commentaire ?: 'Commande conforme, rien à signaler.' }}"</p>
                        </div>
                        <div class="pt-4 mt-2 border-t border-surface-container flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-label-md">{{ initiales($nom) }}</div>
                            <div>
                                <p class="font-label-md text-label-md font-bold text-on-surface">{{ $nom }}</p>
                                <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $unAvis->pharmacie ? 'Commande • '.$unAvis->pharmacie->nom : 'Commande vérifiée' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-3 bg-surface rounded-2xl p-8 text-center {{ $ombre }}">
                        <span class="material-symbols-outlined text-[36px] text-outline">rate_review</span>
                        <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Aucun avis pour le moment. Les patients peuvent noter ce médicament après réception de leur commande.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection

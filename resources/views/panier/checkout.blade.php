@extends('layouts.app')

@section('titre', 'Finalisation de votre commande')

@php
    $total = $sousTotal + $fraisLivraison;
    $nbArticles = $lignes->sum('quantite');
    $utilisateur = auth()->user();
    $zones = [
        'Douala 1er (Akwa / Bonanjo / Bonapriso / Koumassi)',
        'Douala 2e (New Bell / Nkololoun / Babylone)',
        'Douala 3e (Bassa / Logbaba / Ndogpassi / Nyalla)',
        'Douala 4e (Bonabéri / Grand Moulin / Mambanda)',
        'Douala 5e (Makepe / Bonamoussadi / Denver / Kotto)',
    ];
    $telephone = preg_replace('/^(\+?237)/', '', preg_replace('/\s+/', '', (string) $utilisateur?->telephone));
@endphp

@section('contenu')
<div class="flex flex-col w-full">
    <div class="relative w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop pt-space-md pb-space-xl">
        <nav aria-label="Fil d'Ariane" class="flex items-center gap-2 mb-space-md font-body-sm text-body-sm text-on-surface-variant">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('accueil') }}">
                <span class="material-symbols-outlined text-[16px]">home</span>
                <span>Accueil</span>
            </a>
            <span class="text-outline-variant">/</span>
            <a class="hover:text-primary transition-colors" href="{{ route('public.pharmacie', $pharmacie) }}">{{ $pharmacie->nom }}</a>
            <span class="text-outline-variant">/</span>
            <span class="text-on-surface font-label-md text-label-md">Votre panier &amp; Paiement</span>
        </nav>

        @include('panier.partials.etapes', ['etape' => 2])

        @if ($errors->any())
            <div class="mb-space-lg p-4 rounded-xl bg-error-container text-on-error-container font-body-md text-body-md flex items-start gap-3">
                <span class="material-symbols-outlined text-[22px]">error</span>
                <ul class="space-y-1">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-start">
            <!-- Colonne gauche -->
            <div class="lg:col-span-7 flex flex-col gap-space-lg">
                @include('panier.partials.recapitulatif')

                <!-- Livraison -->
                <section class="bg-surface-container-lowest rounded-2xl p-space-md sm:p-space-lg shadow-sm"
                         x-data="{ lat: '{{ old('latitude', $client?->latitude) }}', lng: '{{ old('longitude', $client?->longitude) }}', etat: '' }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm pb-space-md border-b border-surface-container-low">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[22px]">local_shipping</span>
                                <h2 class="font-headline-sm text-headline-sm text-on-surface">Informations de livraison à {{ $pharmacie->ville }}</h2>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Assurez-vous de renseigner un numéro joignable pour le coursier</p>
                        </div>
                        <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#dcfce9] text-[#14532d] hover:bg-[#bbf7d0] transition-colors font-label-md text-label-md self-start sm:self-auto"
                                @click="etat = 'recherche'; PharmaConnect.geolocaliser((la, lo) => { lat = la.toFixed(6); lng = lo.toFixed(6); etat = 'ok' }, () => etat = 'erreur')">
                            <span class="material-symbols-outlined text-[18px] text-primary" x-text="etat === 'ok' ? 'check_circle' : 'my_location'">my_location</span>
                            <span x-text="etat === 'ok' ? 'Position enregistrée' : (etat === 'erreur' ? 'Position indisponible' : 'Utiliser ma position')">Utiliser ma position</span>
                            <span class="w-2 h-2 rounded-full bg-primary animate-ping ml-0.5" x-show="etat !== 'ok'"></span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('commande.store') }}" id="form-commande" class="mt-space-md grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                        @csrf
                        <input type="hidden" name="latitude" :value="lat">
                        <input type="hidden" name="longitude" :value="lng">
                        <div>
                            <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="destinataire">Nom complet du destinataire *</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3 text-outline text-[18px]">person</span>
                                <input class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary focus:outline-none" id="destinataire" name="destinataire" type="text" required value="{{ old('destinataire', $utilisateur?->name) }}"/>
                            </div>
                        </div>
                        <div>
                            <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="telephone_contact">Téléphone de contact direct *</label>
                            <div class="relative flex">
                                <span class="inline-flex items-center px-3 rounded-l-xl bg-surface-container text-on-surface font-label-md text-label-md whitespace-nowrap">🇨🇲 +237</span>
                                <input class="w-full px-3 py-2.5 rounded-r-xl bg-surface-container-low text-on-surface font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary focus:outline-none" id="telephone_contact" name="telephone_contact" type="tel" required value="{{ old('telephone_contact', $telephone) }}" placeholder="699 45 88 12"/>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="ville_livraison">Ville / Zone de distribution *</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3 text-outline text-[18px]">location_city</span>
                                <select class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary focus:outline-none appearance-none cursor-pointer" id="ville_livraison" name="ville_livraison" required>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone }}" @selected(old('ville_livraison') === $zone)>{{ $zone }}</option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-3 text-outline text-[18px] pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="adresse_livraison">Adresse de livraison &amp; repères visuels camerounais *</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3 text-outline text-[18px]">pin_drop</span>
                                <input class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary focus:outline-none" id="adresse_livraison" name="adresse_livraison" type="text" required value="{{ old('adresse_livraison', collect([$client?->quartier ? 'Quartier '.$client->quartier : null, $client?->adresse])->filter()->implode(', ')) }}" placeholder="Quartier Bonapriso, Rue des Palmiers, face Clinique de l'Aéroport, Portail blanc"/>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Mentionnez un carrefour, une station-service ou un commerce connu pour faciliter la course.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="notes">Instructions pour le coursier (Optionnel)</label>
                            <textarea class="w-full p-3 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary focus:outline-none resize-none" id="notes" name="notes" rows="2" placeholder="Appeler dès l'arrivée à la barrière, sonnerie en panne.">{{ old('notes') }}</textarea>
                        </div>
                    </form>
                    <div class="mt-space-md p-3 rounded-xl bg-[#dcfce9] flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-[24px] flex-shrink-0">timer</span>
                        <div class="font-body-sm text-body-sm text-[#14532d]">
                            <span class="font-label-md text-label-md block">Délai estimé de livraison : 25 à 35 minutes</span>
                            Votre colis médical est pris en charge immédiatement dès la confirmation du paiement.
                        </div>
                    </div>
                </section>
            </div>

            <!-- Colonne droite : paiement -->
            <div class="lg:col-span-5 flex flex-col gap-space-lg lg:sticky lg:top-24"
                 x-data="{ operateur: '{{ old('operateur', 'mtn_momo') }}', envoi: false }">
                <section class="bg-surface-container-lowest rounded-2xl p-space-md sm:p-space-lg shadow-md">
                    <div class="pb-space-md border-b border-surface-container-low">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[24px]">contactless</span>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface">Paiement Mobile Money</h2>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Débit instantané et sans frais de transaction supplémentaires</p>
                    </div>
                    <div class="mt-space-md">
                        <span class="font-label-md text-label-md text-on-surface block mb-space-xs">Sélectionnez votre opérateur camerounais</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="cursor-pointer relative flex flex-col p-3 rounded-xl transition-all"
                                   :class="operateur === 'mtn_momo' ? 'bg-[#fff9e6] hover:bg-[#fff3cc] shadow-sm' : 'bg-surface-container-low hover:bg-[#fff9e6]'">
                                <input class="sr-only" name="operateur" form="form-commande" type="radio" value="mtn_momo" x-model="operateur"/>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-lg bg-[#ffcc00] flex items-center justify-center font-bold text-black text-[13px] shadow-sm tracking-tighter">MoMo</span>
                                        <span class="font-label-lg text-label-lg text-black font-bold">MTN MoMo</span>
                                    </div>
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center" :class="operateur === 'mtn_momo' ? 'bg-[#ffcc00]' : 'bg-surface-container opacity-40'">
                                        <span class="material-symbols-outlined text-[14px]" :class="operateur === 'mtn_momo' ? 'text-black font-bold' : 'text-on-surface-variant'" x-text="operateur === 'mtn_momo' ? 'check' : 'radio_button_unchecked'">check</span>
                                    </div>
                                </div>
                                <span class="font-body-sm text-body-sm" :class="operateur === 'mtn_momo' ? 'text-[#4d3e00]' : 'text-on-surface-variant'">Validation USSD via *126#</span>
                            </label>
                            <label class="cursor-pointer relative flex flex-col p-3 rounded-xl transition-all"
                                   :class="operateur === 'orange_money' ? 'bg-[#fff2e8] shadow-sm' : 'bg-surface-container-low hover:bg-[#fff2e8]'">
                                <input class="sr-only" name="operateur" form="form-commande" type="radio" value="orange_money" x-model="operateur"/>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-lg bg-[#ff7900] flex items-center justify-center font-bold text-white text-[13px] shadow-sm tracking-tighter">OM</span>
                                        <span class="font-label-lg text-label-lg text-on-surface font-bold">Orange Money</span>
                                    </div>
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center" :class="operateur === 'orange_money' ? 'bg-[#ff7900]' : 'bg-surface-container opacity-40'">
                                        <span class="material-symbols-outlined text-[14px]" :class="operateur === 'orange_money' ? 'text-white font-bold' : 'text-on-surface-variant'" x-text="operateur === 'orange_money' ? 'check' : 'radio_button_unchecked'">radio_button_unchecked</span>
                                    </div>
                                </div>
                                <span class="font-body-sm text-body-sm" :class="operateur === 'orange_money' ? 'text-[#7a3a00]' : 'text-on-surface-variant'">Validation USSD via #150#</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-space-md">
                        <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="numero_mobile_money">Numéro Mobile Money (6XX XX XX XX) *</label>
                        <div class="relative flex">
                            <span class="inline-flex items-center gap-1 px-3 rounded-l-xl bg-surface-container text-on-surface font-label-md text-label-md">
                                <span>🇨🇲</span>
                                <span class="font-bold">+237</span>
                            </span>
                            <input class="w-full px-3.5 py-3 rounded-r-xl bg-surface-container-low text-on-surface font-headline-sm text-headline-sm font-semibold tracking-wide border-0 focus:ring-2 focus:ring-primary focus:outline-none placeholder:text-outline/60" id="numero_mobile_money" name="numero_mobile_money" form="form-commande" required placeholder="677 12 34 56" type="tel" value="{{ old('numero_mobile_money', $telephone) }}"/>
                        </div>
                        <p class="font-body-sm text-body-sm text-[#006e2f] mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">info</span>
                            <span x-text="operateur === 'mtn_momo' ? 'Numéro éligible pour le push automatique MTN MoMo' : 'Numéro éligible pour le push automatique Orange Money'">Numéro éligible pour le push automatique MTN MoMo</span>
                        </p>
                    </div>
                    <div class="mt-space-md p-3.5 rounded-xl bg-surface-container-low">
                        <span class="font-label-md text-label-md text-on-surface block mb-2">Comment se déroule le paiement ?</span>
                        <ol class="space-y-1.5 font-body-sm text-body-sm text-on-surface-variant">
                            <li class="flex items-start gap-2">
                                <span class="font-label-md text-label-md text-primary bg-surface-container-lowest w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                                <span>Cliquez sur le bouton <strong>Payer {{ format_fcfa($total) }}</strong> ci-dessous.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-label-md text-label-md text-primary bg-surface-container-lowest w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                                <span>Une notification push s'affichera directement sur votre téléphone.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-label-md text-label-md text-primary bg-surface-container-lowest w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                                <span>Composez votre code secret Mobile Money pour autoriser le prélèvement.</span>
                            </li>
                        </ol>
                    </div>
                    <button class="w-full mt-space-md py-4 px-6 rounded-xl bg-primary hover:bg-primary-container active:bg-[#14532d] text-on-primary font-label-lg text-headline-sm font-bold flex items-center justify-center gap-3 shadow-[0px_4px_20px_-2px_rgba(20,83,45,0.2)] transition-all disabled:opacity-70"
                            type="submit" form="form-commande" @click="if (document.getElementById('form-commande').checkValidity()) envoi = true">
                        <span class="material-symbols-outlined text-[24px]">lock</span>
                        <span>Payer {{ format_fcfa($total) }}</span>
                    </button>
                    <div class="mt-3.5 text-center flex flex-col items-center gap-1 text-on-surface-variant font-body-sm text-body-sm">
                        <div class="flex items-center justify-center gap-1.5 text-primary font-label-sm text-label-sm">
                            <span class="material-symbols-outlined text-[16px]">security</span>
                            <span>Chiffrement Bancaire SSL 256-bit</span>
                        </div>
                        <p class="text-outline">Paiement simulé en local · Conforme aux normes GIMAC / BEAC</p>
                    </div>
                </section>

                <div class="flex flex-col gap-3">
                    <div class="bg-surface-container-lowest rounded-2xl p-space-md shadow-sm flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#dcfce9] text-[#14532d] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-[22px]">health_and_safety</span>
                        </div>
                        <div>
                            <h3 class="font-label-lg text-label-lg text-on-surface">Garantie Traçabilité MINSANTE</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Vos médicaments sont préparés, vérifiés et scellés sous la supervision directe du pharmacien titulaire de la <strong>{{ $pharmacie->nom }}</strong>.</p>
                        </div>
                    </div>
                    <div class="bg-surface-container-lowest rounded-2xl p-space-md shadow-sm flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-surface-container-low text-primary flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[22px]">support_agent</span>
                            </div>
                            <div>
                                <h4 class="font-label-md text-label-md text-on-surface">Besoin d'aide pour régler ?</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Support Douala : <strong class="text-primary">+237 670 00 00 00</strong></p>
                            </div>
                        </div>
                        @if ($pharmacie->user)
                            <form method="POST" action="{{ route('messagerie.demarrer', $pharmacie->user) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm hover:bg-[#bbf7d0] transition-colors flex items-center gap-1 flex-shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">chat</span>
                                    <span>Message</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Attente de validation (affichée pendant l'envoi du formulaire) -->
                <div class="fixed inset-0 z-50 bg-[rgba(30,41,59,0.4)] flex items-center justify-center p-4" x-show="envoi" x-cloak>
                    <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full p-space-lg shadow-xl text-center">
                        <div class="w-16 h-16 rounded-full bg-[#dcfce9] text-primary flex items-center justify-center mx-auto mb-space-md animate-bounce">
                            <span class="material-symbols-outlined text-[36px]">cell_tower</span>
                        </div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Demande envoyée au téléphone</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant mt-2">Consultez l'écran de votre smartphone. Saisissez votre code secret pour valider le montant de <strong class="text-on-surface">{{ format_fcfa($total) }}</strong>.</p>
                        <div class="mt-space-md p-3 rounded-xl bg-surface-container-low flex items-center justify-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-primary animate-ping"></span>
                            <span class="font-label-md text-label-md text-on-surface">En attente de votre validation USSD...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

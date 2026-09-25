@extends('layouts.app')

@section('titre', 'Mon panier')

@section('contenu')
<div class="flex flex-col w-full">
    <div class="relative w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop pt-space-md pb-space-xl">
        <nav aria-label="Fil d'Ariane" class="flex items-center gap-2 mb-space-md font-body-sm text-body-sm text-on-surface-variant">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('accueil') }}">
                <span class="material-symbols-outlined text-[16px]">home</span>
                <span>Accueil</span>
            </a>
            <span class="text-outline-variant">/</span>
            <span class="text-on-surface font-label-md text-label-md">Votre panier</span>
        </nav>

        @include('panier.partials.etapes', ['etape' => 1, 'titre' => 'Votre panier'])

        @if ($lignes->isEmpty())
            <section class="bg-surface-container-lowest rounded-2xl p-space-xl shadow-sm text-center max-w-2xl mx-auto">
                <div class="w-16 h-16 rounded-full bg-[#dcfce9] text-primary flex items-center justify-center mx-auto mb-space-md">
                    <span class="material-symbols-outlined text-[34px]">shopping_bag</span>
                </div>
                <h2 class="font-headline-md text-headline-md text-on-surface">Votre panier est vide</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Recherchez un médicament et ajoutez-le depuis une officine partenaire. Un panier ne concerne qu'une seule pharmacie à la fois.</p>
                <div class="mt-space-lg flex flex-wrap justify-center gap-3">
                    <a href="{{ route('public.medicaments') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                        Rechercher un médicament
                    </a>
                    <a href="{{ route('public.pharmacies') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white border border-[#bbf7d0] text-primary font-label-lg text-label-lg hover:bg-[#f0fdf6] transition-colors">
                        Voir les officines
                    </a>
                </div>
            </section>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-start">
                <div class="lg:col-span-7 flex flex-col gap-space-lg">
                    @include('panier.partials.recapitulatif')
                </div>
                <div class="lg:col-span-5 flex flex-col gap-space-lg lg:sticky lg:top-24">
                    <section class="bg-surface-container-lowest rounded-2xl p-space-md sm:p-space-lg shadow-md">
                        <div class="pb-space-md border-b border-surface-container-low">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[24px]">receipt_long</span>
                                <h2 class="font-headline-sm text-headline-sm text-on-surface">Récapitulatif</h2>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Paiement Mobile Money à l'étape suivante</p>
                        </div>
                        <div class="mt-space-md flex flex-col gap-2 font-body-md text-body-md text-on-surface-variant">
                            <div class="flex justify-between"><span>Sous-total</span><span class="font-label-lg text-label-lg text-on-surface">{{ format_fcfa($sousTotal) }}</span></div>
                            <div class="flex justify-between"><span>Livraison</span><span class="font-label-lg text-label-lg text-on-surface">{{ format_fcfa($fraisLivraison) }}</span></div>
                            <div class="flex justify-between items-center pt-2 mt-1 border-t border-surface-container">
                                <span class="font-headline-sm text-headline-sm text-on-surface">Total</span>
                                <span class="font-headline-md text-headline-md text-primary font-bold">{{ format_fcfa($total) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('commande.create') }}" class="w-full mt-space-md py-4 px-6 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-headline-sm font-bold flex items-center justify-center gap-3 shadow-[0px_4px_20px_-2px_rgba(20,83,45,0.2)] transition-all">
                            <span>Livraison &amp; Paiement</span>
                            <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
                        </a>
                        <div class="mt-3 flex items-center justify-between">
                            <a href="{{ route('public.pharmacie', $pharmacie) }}" class="font-label-md text-label-md text-primary hover:underline">Continuer mes achats</a>
                            <form method="POST" action="{{ route('panier.vider') }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="font-label-md text-label-md text-tertiary hover:underline">Vider le panier</button>
                            </form>
                        </div>
                    </section>
                    <div class="bg-surface-container-lowest rounded-2xl p-space-md shadow-sm flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#dcfce9] text-[#14532d] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-[22px]">health_and_safety</span>
                        </div>
                        <div>
                            <h3 class="font-label-lg text-label-lg text-on-surface">Une officine par commande</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Chaque pharmacie gère son stock, ses prix et sa livraison. Pour commander ailleurs, videz d'abord ce panier.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

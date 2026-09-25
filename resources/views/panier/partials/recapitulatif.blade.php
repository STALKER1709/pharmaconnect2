{{-- Carte « Votre panier » — maquette validation_de_commande (quantités et retrait réels) --}}
@php
    $nbArticles = $lignes->sum('quantite');
@endphp
<section class="bg-surface-container-lowest rounded-2xl p-space-md sm:p-space-lg shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm pb-space-md border-b border-surface-container-low">
        <div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[22px]">local_pharmacy</span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Votre panier — {{ $pharmacie->nom }}</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-1.5">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                    {{ collect([$pharmacie->quartier, $pharmacie->ville])->filter()->implode(', ') }}
                </span>
                <span class="text-on-surface-variant font-body-sm text-body-sm flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px] text-primary">schedule</span>
                    Préparation garantie sous 15 min
                </span>
            </div>
        </div>
        <span class="self-start sm:self-center font-label-md text-label-md text-on-surface-variant bg-surface-container px-2.5 py-1 rounded-lg">{{ $nbArticles }} {{ \Illuminate\Support\Str::plural('article', $nbArticles) }}</span>
    </div>
    <div class="divide-y divide-surface-container-low">
        @foreach ($lignes as $ligne)
            @php $stock = $ligne['stock']; $m = $stock->medicament; $theme = \App\Support\ThemeMedicament::pour($m->categorie?->nom); @endphp
            <div class="py-space-md flex items-center justify-between gap-space-sm">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-primary flex-shrink-0">
                        <span class="material-symbols-outlined text-[24px]">{{ $theme['icone'] }}</span>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-label-lg text-label-lg text-on-surface truncate">{{ $m->nom }}</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ ucfirst($m->forme ?? '') }}@if($m->fabricant) · {{ $m->fabricant }}@endif</p>
                        <p class="font-label-sm text-label-sm text-primary mt-0.5">{{ format_fcfa($stock->prix) }} / unité</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:gap-5 flex-shrink-0">
                    <div class="inline-flex items-center bg-surface-container-low rounded-lg p-1">
                        <form method="POST" action="{{ route('panier.modifier') }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                            <input type="hidden" name="quantite" value="{{ max(1, $ligne['quantite'] - 1) }}">
                            <button class="w-6 h-6 flex items-center justify-center rounded text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors disabled:opacity-30" type="submit" @disabled($ligne['quantite'] <= 1) aria-label="Diminuer">
                                <span class="material-symbols-outlined text-[16px]">remove</span>
                            </button>
                        </form>
                        <span class="w-7 text-center font-label-md text-label-md text-on-surface">{{ $ligne['quantite'] }}</span>
                        <form method="POST" action="{{ route('panier.modifier') }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                            <input type="hidden" name="quantite" value="{{ $ligne['quantite'] + 1 }}">
                            <button class="w-6 h-6 flex items-center justify-center rounded text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors disabled:opacity-30" type="submit" @disabled($ligne['quantite'] >= $stock->quantite) aria-label="Augmenter">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                            </button>
                        </form>
                    </div>
                    <span class="font-currency-display text-currency-display text-on-surface w-24 text-right">{{ format_fcfa($ligne['sous_total']) }}</span>
                    <form method="POST" action="{{ route('panier.supprimer') }}">
                        @csrf @method('DELETE')
                        <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                        <button class="p-1 text-outline hover:text-error hover:bg-error-container/20 rounded-md transition-colors" title="Retirer l'article" type="submit">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-space-sm pt-space-sm border-t border-surface-container-low">
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer font-label-md text-label-md text-primary hover:text-primary-container transition-colors py-1">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">loyalty</span>
                    <span>Avez-vous un code de réduction ou bon mutuelle ?</span>
                </span>
                <span class="material-symbols-outlined text-[18px] group-open:rotate-180 transition-transform">expand_more</span>
            </summary>
            <p class="mt-3 font-body-sm text-body-sm text-on-surface-variant">Les bons mutuelle sont vérifiés par le pharmacien : mentionnez votre numéro d'assuré dans les instructions de livraison ou via la messagerie de l'officine.</p>
        </details>
    </div>
    <div class="mt-space-md pt-space-md border-t border-surface-container flex flex-col gap-2 font-body-md text-body-md">
        <div class="flex items-center justify-between text-on-surface-variant">
            <span>Sous-total articles</span>
            <span class="font-label-lg text-label-lg text-on-surface">{{ format_fcfa($sousTotal) }}</span>
        </div>
        <div class="flex items-start justify-between text-on-surface-variant">
            <div>
                <span class="block text-on-surface">Livraison coursier express ({{ $pharmacie->quartier ?? $pharmacie->ville }})</span>
                <span class="font-body-sm text-body-sm text-outline flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-primary">inventory_2</span>
                    Sac isotherme scellé &amp; remise en main propre
                </span>
            </div>
            <span class="font-label-lg text-label-lg text-on-surface">{{ format_fcfa($fraisLivraison) }}</span>
        </div>
        <div class="mt-3 p-space-md rounded-xl bg-surface-container-low flex items-center justify-between">
            <div>
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider block">Montant net à régler</span>
                <span class="font-headline-sm text-headline-sm text-on-surface font-bold">TOTAL À PAYER</span>
            </div>
            <span class="font-headline-lg text-headline-lg text-primary font-bold">{{ format_fcfa($sousTotal + $fraisLivraison) }}</span>
        </div>
    </div>
</section>

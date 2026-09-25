@props(['medicament'])
{{-- Carte médicament — maquette accueil (section « Médicaments disponibles en officine ») --}}
@php
    $theme = \App\Support\ThemeMedicament::pour($medicament->categorie?->nom);
    $offre = $medicament->meilleureOffre();
    $ombre = 'shadow-[0_4px_20px_-2px_rgba(22,163,74,0.06),0_2px_6px_-1px_rgba(0,0,0,0.04)]';
@endphp
<div class="bg-surface-container-lowest rounded-2xl p-5 border border-[#e2e8f0] {{ $ombre }} flex flex-col justify-between transition-all hover:-translate-y-1 hover:shadow-[0_10px_25px_-4px_rgba(20,83,45,0.08)] {{ $offre ? '' : 'opacity-95' }}">
    <div>
        <a href="{{ route('public.medicament', $medicament) }}" class="w-full h-36 rounded-xl {{ $offre ? $theme['fond'] : 'bg-slate-50 border-slate-200' }} border flex items-center justify-center relative p-3 overflow-hidden">
            <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-full {{ $theme['pastille'] }} font-label-sm text-label-sm font-medium">{{ $medicament->categorie?->nom ?? 'Officine' }}</span>
            @if ($offre)
                <span class="absolute top-2.5 right-2.5 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-label-sm text-label-sm font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>En stock
                </span>
            @else
                <span class="absolute top-2.5 right-2.5 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 font-label-sm text-label-sm font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Rupture
                </span>
            @endif
            <x-svg-medicament :type="$offre ? $theme['svg'] : 'spray'" :libelle="$medicament->dosage_mg ? ($medicament->dosage_mg >= 1000 ? ($medicament->dosage_mg / 1000).'g' : $medicament->dosage_mg) : null"/>
        </a>
        <div class="mt-4">
            <span class="text-xs text-slate-400 font-mono">{{ $medicament->reference ? 'CIP: '.$medicament->reference : 'Réf. PC-'.str_pad($medicament->id, 5, '0', STR_PAD_LEFT) }}</span>
            <h3 class="font-headline-sm text-headline-sm text-slate-900 font-bold leading-tight mt-0.5">
                <a href="{{ route('public.medicament', $medicament) }}" class="hover:text-primary transition-colors">{{ $medicament->nom }}</a>
            </h3>
            <p class="font-body-sm text-body-sm text-slate-500 mt-1">{{ ucfirst($medicament->forme ?? '') }}@if($medicament->fabricant) • {{ $medicament->fabricant }}@endif</p>
        </div>
    </div>
    <div class="mt-5 pt-3 border-t border-slate-100">
        <div class="flex items-center justify-between mb-3 gap-2">
            @if ($offre)
                <span class="font-currency-display text-currency-display font-bold text-slate-900 whitespace-nowrap">{{ format_fcfa($offre->prix) }}</span>
                <span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-slate-500 text-right">
                    <span class="material-symbols-outlined text-[14px] text-primary">store</span>
                    {{ $offre->pharmacie->nom }}
                </span>
            @else
                <span class="font-currency-display text-currency-display font-bold text-slate-900">—</span>
                <span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-rose-600">
                    <span class="material-symbols-outlined text-[14px]">warning</span>
                    Non disponible
                </span>
            @endif
        </div>
        @if ($offre)
            @if (auth()->user()?->estClient())
                <form method="POST" action="{{ route('panier.ajouter', $offre) }}">
                    @csrf
                    <input type="hidden" name="quantite" value="1">
                    <button class="w-full py-2.5 px-3 rounded-[12px] bg-[#16a34a] hover:bg-[#15803d] text-white font-label-md text-label-md font-semibold flex items-center justify-center gap-1.5 transition-colors shadow-sm" type="submit">
                        <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                        <span>Ajouter au panier</span>
                    </button>
                </form>
            @else
                <a href="{{ auth()->check() ? route('public.medicament', $medicament) : route('login') }}" class="w-full py-2.5 px-3 rounded-[12px] bg-[#16a34a] hover:bg-[#15803d] text-white font-label-md text-label-md font-semibold flex items-center justify-center gap-1.5 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                    <span>Ajouter au panier</span>
                </a>
            @endif
        @else
            <a href="{{ route('public.medicament', $medicament) }}" class="w-full py-2.5 px-3 rounded-[12px] bg-slate-100 hover:bg-slate-200 text-slate-700 font-label-md text-label-md font-medium flex items-center justify-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px] text-slate-500">notifications_active</span>
                <span>M'alerter du retour</span>
            </a>
        @endif
    </div>
</div>

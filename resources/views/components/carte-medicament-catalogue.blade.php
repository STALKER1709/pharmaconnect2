@props(['medicament'])
{{-- Carte médicament — maquette recherche_de_medicaments (grille 3 colonnes) --}}
@php
    $theme = \App\Support\ThemeMedicament::pour($medicament->categorie?->nom);
    $offre = $medicament->meilleureOffre();
    $nbPharmacies = $medicament->nbPharmaciesEnStock();
    $nomCategorie = $medicament->categorie?->nom ?? 'Officine';
    $pastille = str_contains(mb_strtolower($nomCategorie), 'palu')
        ? 'bg-[#fef3c7] text-[#92400e]'
        : (str_contains(mb_strtolower($nomCategorie), 'antalg') ? 'bg-[#f0fdf6] text-[#14532d]' : 'bg-surface-container text-on-surface');
@endphp
<div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
    <div>
        <div class="flex items-center justify-between mb-3">
            <span class="px-2.5 py-0.5 rounded-full {{ $pastille }} font-label-sm text-label-sm font-medium">{{ $nomCategorie }}</span>
            @if ($offre)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span> En stock
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#fee2e2] text-[#991b1b] font-label-sm text-label-sm font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#dc2626]"></span> Rupture
                </span>
            @endif
        </div>
        <a href="{{ route('public.medicament', $medicament) }}" class="relative w-full h-36 bg-[#f0fdf6] rounded-xl flex flex-col items-center justify-center p-3 mb-4 overflow-hidden">
            <div class="w-16 h-16 rounded-2xl bg-surface-container-lowest shadow-sm flex items-center justify-center text-primary group-hover:scale-105 transition-transform">
                <span class="material-symbols-outlined text-[36px]">{{ $theme['icone'] }}</span>
            </div>
            <div class="absolute bottom-2 left-3 right-3 flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                <span>{{ $medicament->fabricant ?? 'Laboratoire' }}</span>
                <span>{{ $medicament->reference ? 'CIP: '.$medicament->reference : 'Réf: PC-'.str_pad($medicament->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </a>
        <h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold group-hover:text-primary transition-colors">
            <a href="{{ route('public.medicament', $medicament) }}">{{ $medicament->nom }}</a>
        </h3>
        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
            @if($medicament->dosage_mg){{ $medicament->dosage_mg }}mg — @endif{{ ucfirst($medicament->forme ?? 'Présentation officinale') }}
        </p>
        <div class="mt-3 flex items-center gap-1.5 text-on-surface font-label-sm text-label-sm">
            <span class="material-symbols-outlined text-primary text-[16px]">location_on</span>
            @if ($nbPharmacies > 0)
                <span>Disponible dans <strong class="text-primary font-semibold">{{ $nbPharmacies }} {{ \Illuminate\Support\Str::plural('pharmacie', $nbPharmacies) }}</strong></span>
            @else
                <span>Momentanément indisponible</span>
            @endif
        </div>
        @if ($medicament->ordonnance_obligatoire)
            <div class="mt-1 flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-[14px]">prescriptions</span>
                <span>Prescription médicale requise</span>
            </div>
        @elseif ($offre)
            <div class="mt-1 flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                <span>Disponible en livraison express</span>
            </div>
        @endif
    </div>
    <div class="pt-4 mt-4 border-t-0 flex items-center justify-between">
        <div>
            <span class="font-body-sm text-body-sm text-on-surface-variant block">{{ $offre ? 'Prix officiel' : 'Dernier prix' }}</span>
            <span class="font-currency-display text-currency-display text-on-surface tracking-tight">{{ $offre ? format_fcfa($offre->prix) : '—' }}</span>
        </div>
        <a href="{{ route('public.medicament', $medicament) }}" class="px-4 py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-colors shadow-sm flex items-center gap-1.5">
            <span>Voir</span>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
    </div>
</div>

@props(['titre', 'sousTitre' => null, 'icone' => 'space_dashboard', 'badge' => null])
{{-- Bandeau d'en-tête des espaces professionnels — maquette tableau_de_bord_pharmacie --}}
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-md bg-surface-container-lowest p-space-lg rounded-xl shadow-sm">
    <div class="flex items-center gap-4 min-w-0">
        <div class="w-12 h-12 rounded-xl bg-secondary-container/30 flex items-center justify-center text-primary flex-shrink-0">
            <span class="material-symbols-outlined text-[26px]">{{ $icone }}</span>
        </div>
        <div class="flex flex-col gap-1 min-w-0">
            <div class="flex items-center gap-space-xs flex-wrap">
                <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-surface tracking-tight">{{ $titre }}</h1>
                @if ($badge)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-container/40 text-on-secondary-container font-label-sm text-label-sm">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>{{ $badge }}
                    </span>
                @endif
            </div>
            @if ($sousTitre)
                <p class="font-body-md text-body-md text-on-surface-variant">{{ $sousTitre }}</p>
            @endif
        </div>
    </div>
    @if (trim($slot) !== '')
        <div class="flex items-center gap-space-sm flex-wrap">{{ $slot }}</div>
    @endif
</div>

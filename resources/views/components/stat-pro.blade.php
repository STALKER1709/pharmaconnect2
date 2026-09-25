@props(['libelle', 'valeur', 'icone', 'teinte' => 'bg-secondary-container/30 text-primary', 'couleurValeur' => 'text-on-surface'])
{{-- Carte indicateur des espaces professionnels --}}
<div {{ $attributes->merge(['class' => 'bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col justify-between gap-space-sm']) }}>
    <div class="flex items-start justify-between gap-2">
        <div class="flex flex-col min-w-0">
            <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">{{ $libelle }}</span>
            <span class="font-headline-lg text-headline-lg {{ $couleurValeur }} mt-1 leading-tight break-words">{{ $valeur }}</span>
        </div>
        <div class="w-12 h-12 rounded-xl {{ $teinte }} flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-[26px]">{{ $icone }}</span>
        </div>
    </div>
    @if (trim($slot) !== '')
        <div class="font-body-sm text-body-sm text-on-surface-variant">{{ $slot }}</div>
    @endif
</div>

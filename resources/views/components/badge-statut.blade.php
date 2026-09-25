@props(['statut'])
{{-- Pastille de statut (commande ou livraison) aux couleurs de l'énumération --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold whitespace-nowrap '.(method_exists($statut, 'couleur') ? $statut->couleur() : 'bg-surface-container text-on-surface')]) }}>
    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
    {{ $statut->label() }}
</span>

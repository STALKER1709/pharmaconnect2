{{-- Pagination discrète « Précédent 1 Suivant » — maquette validation_gestion_des_comptes --}}
<div class="flex items-center gap-space-xs">
    @if ($paginator->onFirstPage())
        <span class="px-space-sm py-1 rounded bg-surface-container-low text-on-surface-variant opacity-60 text-label-sm font-label-sm">Précédent</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-space-sm py-1 rounded bg-surface-container-low text-on-surface-variant hover:text-on-surface text-label-sm font-label-sm">Précédent</a>
    @endif
    <span class="px-space-sm py-1 rounded bg-primary text-on-primary text-label-sm font-label-sm">{{ $paginator->currentPage() }}</span>
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-space-sm py-1 rounded bg-surface-container-low text-on-surface-variant hover:text-on-surface text-label-sm font-label-sm">Suivant</a>
    @else
        <span class="px-space-sm py-1 rounded bg-surface-container-low text-on-surface-variant opacity-60 text-label-sm font-label-sm">Suivant</span>
    @endif
</div>

{{-- Pagination — maquette recherche_de_medicaments (boutons 40×40 arrondis) --}}
@if ($paginator->hasPages())
    @php
        $courante = $paginator->currentPage();
        $derniere = $paginator->lastPage();
        $pages = collect([1, $courante - 1, $courante, $courante + 1, $derniere])
            ->filter(fn ($p) => $p >= 1 && $p <= $derniere)->unique()->sort()->values();
        if ($courante <= 2) {
            $pages = $pages->merge([2, 3])->filter(fn ($p) => $p <= $derniere)->unique()->sort()->values();
        }
        $classeBouton = 'w-10 h-10 rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-[#dcfce9] transition-colors flex items-center justify-center';
        $classeFleche = 'w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-on-surface-variant hover:bg-[#dcfce9] hover:text-[#14532d] transition-colors';
    @endphp
    <nav class="flex items-center gap-1.5" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="{{ $classeFleche }} opacity-40"><span class="material-symbols-outlined text-[20px]">chevron_left</span></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="{{ $classeFleche }}" rel="prev" aria-label="Page précédente"><span class="material-symbols-outlined text-[20px]">chevron_left</span></a>
        @endif

        @foreach ($pages as $i => $page)
            @if ($i > 0 && $page - $pages[$i - 1] > 1)
                <span class="px-2 text-on-surface-variant">…</span>
            @endif
            @if ($page === $courante)
                <span class="w-10 h-10 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-bold shadow-sm flex items-center justify-center" aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" class="{{ $classeBouton }}">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="{{ $classeFleche }}" rel="next" aria-label="Page suivante"><span class="material-symbols-outlined text-[20px]">chevron_right</span></a>
        @else
            <span class="{{ $classeFleche }} opacity-40"><span class="material-symbols-outlined text-[20px]">chevron_right</span></span>
        @endif
    </nav>
@endif

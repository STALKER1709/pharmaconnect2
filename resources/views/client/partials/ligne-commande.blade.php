{{-- Ligne de commande (tableau de bord et historique du patient) --}}
<a href="{{ route('commandes.show', $commande) }}" class="flex flex-wrap items-center justify-between gap-3 p-space-md hover:bg-surface-container-low/60 transition-colors">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-11 h-11 rounded-xl bg-[#dcfce7] text-[#15803d] flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-[22px]">receipt_long</span>
        </div>
        <div class="min-w-0">
            <p class="font-label-lg text-label-lg text-on-surface truncate">#{{ $commande->numero }} — {{ $commande->pharmacie?->nom }}</p>
            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $commande->lignes->sum('quantite') }} {{ \Illuminate\Support\Str::plural('article', $commande->lignes->sum('quantite')) }} · {{ $commande->created_at->format('d/m/Y à H\hi') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <span class="font-currency-display text-currency-display text-on-surface whitespace-nowrap">{{ format_fcfa($commande->total) }}</span>
        <x-badge-statut :statut="$commande->statut"/>
        <span class="material-symbols-outlined text-outline">chevron_right</span>
    </div>
</a>

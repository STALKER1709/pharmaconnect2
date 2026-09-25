{{-- En-tête + indicateur d'étapes — maquette validation_de_commande --}}
@php
    $etapes = [1 => 'Panier', 2 => 'Livraison & Paiement', 3 => 'Confirmation'];
@endphp
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md pb-space-lg mb-space-lg border-b border-surface-container">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container-low text-primary font-label-sm text-label-sm mb-space-xs">
            <span class="material-symbols-outlined text-[15px]">verified_user</span>
            <span>Commande Sécurisée &amp; Traçabilité Certifiée</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ $titre ?? 'Finalisation de votre commande' }}</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Officine certifiée ONPC — Livraison express sur Douala et environs</p>
    </div>
    <div class="bg-surface-container-lowest p-2 sm:p-3 rounded-xl shadow-sm flex items-center gap-2 sm:gap-4 self-start lg:self-auto">
        @foreach ($etapes as $numero => $libelle)
            @if ($numero > 1)
                <div class="w-6 sm:w-10 h-0.5 {{ $numero <= $etape ? 'bg-primary' : 'bg-surface-container-highest' }}"></div>
            @endif
            @if ($numero < $etape)
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                    </div>
                    <span class="font-label-md text-label-md text-on-surface hidden sm:inline">{{ $libelle }}</span>
                </div>
            @elseif ($numero === $etape)
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-primary-fixed-dim text-on-primary-fixed flex items-center justify-center font-label-sm text-label-sm ring-2 ring-primary">{{ $numero }}</div>
                    <span class="font-label-md text-label-md text-primary font-bold">{{ $libelle }}</span>
                </div>
            @else
                <div class="flex items-center gap-2 opacity-60">
                    <div class="w-7 h-7 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center font-label-sm text-label-sm">{{ $numero }}</div>
                    <span class="font-label-md text-label-md text-on-surface-variant hidden sm:inline">{{ $libelle }}</span>
                </div>
            @endif
        @endforeach
    </div>
</div>

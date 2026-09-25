{{-- Fil d'Ariane + bandeau réglementaire de la messagerie (maquette messagerie_en_direct) --}}
<div class="flex flex-wrap items-center justify-between gap-space-sm mb-space-sm">
    <div class="flex items-center gap-space-xs text-on-surface-variant">
        <a class="hover:text-primary transition-colors flex items-center gap-1 font-body-sm text-body-sm" href="{{ route('dashboard') }}">
            <span class="material-symbols-outlined text-[16px]">home</span>
            Tableau de bord
        </a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('messagerie.index') }}" class="font-label-md text-label-md text-primary bg-primary/10 px-space-xs py-0.5 rounded-full">Messagerie Sécurisée</a>
        @isset($fil)
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="font-body-sm text-body-sm text-on-surface font-semibold">{{ $fil }}</span>
        @endisset
    </div>
    <div class="flex items-center gap-space-sm bg-surface-container-lowest px-space-sm py-1 rounded-full shadow-sm">
        <span class="inline-flex items-center justify-center w-2 h-2 rounded-full bg-primary animate-ping"></span>
        <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
            <span class="material-symbols-outlined text-primary text-[14px]">verified_user</span>
            Canal conforme ONPC • Douala • Yaoundé
        </span>
    </div>
</div>

@extends(layout_espace())

@section('titre', 'Messagerie')
@section('classe_main', 'w-full pt-20 bg-background')

@section('contenu')
<div class="flex flex-col w-full">
    <div class="max-w-[1280px] w-full mx-auto {{ auth()->user()->estPharmacie() || auth()->user()->estAdmin() ? 'py-space-md' : 'px-gutter md:px-gutter-desktop py-space-sm md:py-space-md' }}">
        @include('messagerie.partials.cadre')
        <div class="w-full bg-surface-container-lowest rounded-2xl shadow-sm flex flex-col lg:flex-row lg:h-[calc(100vh-165px)] lg:min-h-[640px] overflow-hidden">
            @include('messagerie.partials.liste')
            <div class="hidden lg:flex flex-1 flex-col items-center justify-center bg-surface-bright text-center p-space-xl">
                <div class="w-16 h-16 rounded-2xl bg-surface-container-highest text-primary flex items-center justify-center mb-space-md">
                    <span class="material-symbols-outlined text-[34px]">forum</span>
                </div>
                <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Sélectionnez une discussion</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1 max-w-sm">Échangez en direct avec votre officine ou votre coursier : posologie, allergies, précisions de livraison.</p>
                <div class="mt-space-md max-w-xl bg-surface-container-lowest/90 px-space-md py-space-xs rounded-xl shadow-sm flex items-center gap-space-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-primary text-[16px] flex-shrink-0">lock</span>
                    <span class="font-body-sm text-body-sm text-[12px]">Échange médical sécurisé conforme à l'Ordre National des Pharmaciens du Cameroun (ONPC)</span>
                </div>
            </div>
        </div>
        @include('messagerie.partials.pied')
    </div>
</div>
@endsection

{{-- En-tête public — maquettes Stitch (accueil, recherche, fiche pharmacie, paiement, suivi) --}}
@php
    $utilisateur = auth()->user();
    $estClient = $utilisateur?->estClient();
    $nbArticles = collect(session('panier.items', []))->sum('quantite');

    $liens = [
        ['Médicaments', route('public.medicaments'), request()->routeIs('public.medicament*')],
        ['Pharmacies de garde', route('public.pharmacies'), request()->routeIs('public.pharmacie*')],
        ['Ordonnances', route('accueil').'#ordonnance', false],
    ];
    if ($estClient) {
        $liens[] = ['Suivi de livraison', route('commandes.index'), request()->routeIs('commandes.*', 'suivi.*', 'paiement.*')];
        $liens[] = ['Messages', route('messagerie.index'), request()->routeIs('messagerie.*')];
    } else {
        $liens[] = ['Comment ça marche', route('accueil').'#comment-ca-marche', false];
    }

    $classeLien = 'px-space-md py-2 text-on-surface-variant font-label-lg text-label-lg hover:text-on-surface hover:bg-surface-container transition-all rounded-xl';
    $classeActif = 'px-space-md py-2 transition-all bg-surface-container-high text-on-surface font-label-lg text-label-lg rounded-xl';
@endphp
<header class="fixed top-0 left-0 right-0 w-full z-50 bg-surface-container-lowest border-b border-[#e2e8f0]" x-data="{ menuMobile: false }">
    <div class="h-20 max-w-[1280px] mx-auto px-margin md:px-margin-desktop flex items-center justify-between gap-space-md">
        <div class="flex items-center gap-space-sm flex-shrink-0">
            <a class="flex items-center gap-space-sm focus:outline-none flex-shrink-0 whitespace-nowrap" href="{{ route('accueil') }}">
                <img alt="PharmaConnect Logo" class="h-8 w-auto object-contain" src="{{ asset('images/logo-pharmaconnect.svg') }}"/>
                <span class="hidden sm:inline font-headline-sm text-headline-sm text-primary tracking-tight">PharmaConnect</span>
            </a>
        </div>
        <nav class="hidden lg:flex items-center gap-space-xs p-1">
            @foreach ($liens as [$libelle, $url, $actif])
                <a class="{{ $actif ? $classeActif : $classeLien }}" href="{{ $url }}" @if($actif) aria-current="page" @endif>{{ $libelle }}</a>
            @endforeach
        </nav>
        <div class="flex items-center gap-space-sm">
            <div class="hidden md:inline-flex items-center gap-1.5 px-space-md py-1.5 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm">
                <span class="material-symbols-outlined text-[16px] text-primary">location_on</span><span>Douala</span>
            </div>
            @guest
                <a class="hidden sm:inline-flex items-center justify-center px-space-md py-2 rounded-xl bg-surface-container-lowest border border-[#bbf7d0] text-primary font-label-lg text-label-lg hover:bg-[#f0fdf6] transition-colors" href="{{ route('login') }}">Connexion</a>
                <a class="inline-flex items-center justify-center px-space-md py-2 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-colors shadow-[0px_4px_20px_-2px_rgba(20,83,45,0.05)]" href="{{ route('register') }}">Créer un compte</a>
            @else
                @if ($estClient)
                    <a class="hidden sm:inline-flex items-center justify-center gap-1.5 px-space-md py-2 rounded-xl bg-surface-container-lowest border border-[#bbf7d0] text-primary font-label-lg text-label-lg hover:bg-[#f0fdf6] transition-colors" href="{{ route('panier.index') }}">
                        <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                        Panier @if($nbArticles > 0)<span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-primary text-on-primary font-label-sm text-label-sm">{{ $nbArticles }}</span>@endif
                    </a>
                @else
                    <a class="inline-flex items-center justify-center px-space-md py-2 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-colors shadow-[0px_4px_20px_-2px_rgba(20,83,45,0.05)]" href="{{ route('dashboard') }}">Mon espace</a>
                @endif
            @endguest
            @include('partials.menu-utilisateur')
            <button type="button" class="lg:hidden w-9 h-9 rounded-xl flex items-center justify-center text-on-surface-variant hover:bg-surface-container" @click="menuMobile = ! menuMobile" aria-label="Menu">
                <span class="material-symbols-outlined text-[22px]" x-text="menuMobile ? 'close' : 'menu'">menu</span>
            </button>
        </div>
    </div>
    <nav class="lg:hidden border-t border-[#e2e8f0] bg-surface-container-lowest px-margin py-space-sm flex flex-col gap-space-xs" x-show="menuMobile" x-cloak @click.outside="menuMobile = false">
        @foreach ($liens as [$libelle, $url, $actif])
            <a class="{{ $actif ? $classeActif : $classeLien }}" href="{{ $url }}">{{ $libelle }}</a>
        @endforeach
        @guest
            <a class="{{ $classeLien }} sm:hidden" href="{{ route('login') }}">Connexion</a>
        @endguest
        @if ($estClient)
            <a class="{{ $classeLien }} sm:hidden" href="{{ route('panier.index') }}">Panier ({{ $nbArticles }})</a>
        @endif
    </nav>
</header>

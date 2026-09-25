<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
@php
    $utilisateur = auth()->user();
    $liens = [
        ['Utilisateurs', 'group', route('admin.utilisateurs'), request()->routeIs('admin.utilisateurs*')],
        ['Statistiques', 'monitoring', route('admin.dashboard'), request()->routeIs('admin.dashboard')],
        ['Paramètres', 'settings', route('profil.edit'), request()->routeIs('profil.*')],
    ];
@endphp
<body class="bg-surface font-body-md text-on-surface" x-data="{ menu: false }">
    <!-- Barre latérale — maquette validation_gestion_des_comptes_administration -->
    <aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_1px_8px_rgba(20,83,45,0.04)] z-50 flex-col justify-between lg:flex"
           :class="menu ? 'flex' : 'hidden'" @click.outside="if (window.innerWidth < 1024) menu = false">
        <div class="flex flex-col">
            <div class="h-16 px-space-md flex items-center gap-space-sm bg-surface-container-low">
                <a href="{{ route('accueil') }}"><img alt="PharmaConnect" class="h-8 w-auto object-contain" src="{{ asset('images/logo-pharmaconnect.svg') }}"/></a>
                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm text-primary tracking-tight">PharmaConnect</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">Cameroun Régulation</span>
                </div>
            </div>
            <div class="px-space-md pt-space-md pb-space-sm">
                <div class="px-space-sm py-space-xs rounded-xl bg-surface-container-low text-on-surface">
                    <p class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Espace Central</p>
                    <p class="font-label-lg text-label-lg text-primary">Administration &amp; Modération</p>
                </div>
            </div>
            <nav class="flex flex-col gap-space-xs px-space-md mt-space-xs">
                @foreach ($liens as [$libelle, $icone, $url, $actif])
                    <a @if($actif) aria-current="page" @endif href="{{ $url }}"
                       class="flex items-center gap-space-sm px-space-md py-space-sm transition-all rounded-xl {{ $actif ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $icone }}</span>
                        <span>{{ $libelle }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
        <div class="p-space-md flex flex-col gap-space-sm">
            <div class="p-space-sm rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Cadre Légal</span>
                    <span class="font-label-sm text-label-sm text-primary font-bold">Conforme</span>
                </div>
                <div class="font-body-sm text-body-sm text-on-surface-variant">Supervision ONPC &amp; MINSANTE Cameroun active.</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="px-space-xs">
                @csrf
                <button type="submit" class="flex items-center gap-space-xs text-tertiary hover:text-tertiary-container transition-colors">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span class="font-label-sm text-label-sm">Déconnexion sécurisée</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="lg:pl-72">
        <header class="fixed top-0 left-0 lg:left-72 right-0 h-16 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(20,83,45,0.04)] z-40 px-margin md:px-gutter-desktop flex items-center justify-between gap-space-sm">
            <button type="button" class="lg:hidden w-9 h-9 rounded-xl bg-surface-container-low flex items-center justify-center text-on-surface-variant" @click.stop="menu = ! menu" aria-label="Menu">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <form method="GET" action="{{ route('admin.utilisateurs') }}" class="flex items-center gap-space-md w-full max-w-96">
                <div class="flex items-center w-full px-space-sm py-space-xs bg-surface-container-low rounded-xl">
                    <span class="material-symbols-outlined text-primary text-[20px] mr-space-xs">search</span>
                    <input name="q" value="{{ request()->routeIs('admin.utilisateurs') ? request('q') : '' }}" class="w-full bg-transparent border-0 p-0 focus:ring-0 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none" placeholder="Rechercher un utilisateur, une pharmacie..." type="search"/>
                </div>
            </form>
            <div class="flex items-center gap-space-md">
                <div class="hidden xl:flex items-center gap-space-xs px-space-sm py-space-xs rounded-full bg-secondary-container/30 text-on-secondary-container">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="font-label-sm text-label-sm font-semibold">MINSANTE / ONPC Direct</span>
                </div>
                <div class="hidden md:flex items-center gap-space-xs px-space-sm py-space-xs rounded-xl bg-surface-container-low text-on-surface">
                    <span class="material-symbols-outlined text-outline text-[18px]">location_on</span>
                    <span class="font-label-sm text-label-sm font-medium">Littoral - Douala</span>
                </div>
                @include('partials.cloche-notifications', ['classe' => 'relative p-space-xs rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors'])
                <div class="flex items-center gap-space-sm pl-space-xs">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="font-label-sm text-label-sm font-semibold text-on-surface leading-tight">{{ $utilisateur->name }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">Système Central</span>
                    </div>
                    @include('partials.menu-utilisateur')
                </div>
            </div>
        </header>

        <main class="w-full pt-16 px-margin md:px-gutter-desktop pb-space-xl bg-surface">
            @yield('contenu')
        </main>
    </div>

    @include('partials.flash')
    @yield('scripts')
</body>
</html>

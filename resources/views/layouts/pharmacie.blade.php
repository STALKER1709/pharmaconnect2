<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
@php
    $utilisateur = auth()->user();
    $officine = $utilisateur->pharmacie;
    $ouverte = $officine?->loadMissing('horaires')->estOuverte() ?? false;
    $liens = [
        ['Tableau de bord', 'space_dashboard', route('pharmacie.dashboard'), request()->routeIs('pharmacie.dashboard')],
        ['Médicaments', 'medication', route('pharmacie.medicaments'), request()->routeIs('pharmacie.medicaments') && ! request()->has('stock')],
        ['Stocks', 'inventory_2', route('pharmacie.medicaments', ['stock' => 'bas']), request()->routeIs('pharmacie.medicaments') && request()->has('stock')],
        ['Commandes', 'receipt_long', route('pharmacie.commandes'), request()->routeIs('pharmacie.commandes*')],
        ['Livraisons', 'local_shipping', route('pharmacie.livraisons'), request()->routeIs('pharmacie.livraisons')],
        ['Paiements', 'payments', route('pharmacie.paiements'), request()->routeIs('pharmacie.paiements')],
        ['Messagerie', 'chat', route('messagerie.index'), request()->routeIs('messagerie.*')],
        ['Horaires', 'schedule', route('pharmacie.horaires'), request()->routeIs('pharmacie.horaires')],
        ['Statistiques', 'monitoring', route('pharmacie.statistiques'), request()->routeIs('pharmacie.statistiques')],
    ];
@endphp
<body class="bg-background font-body-md text-on-surface antialiased min-h-screen" x-data="{ menu: false }">
    <!-- Barre latérale — maquette tableau_de_bord_pharmacie_du_centre -->
    <aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest z-50 flex-col shadow-[0_1px_8px_rgba(0,0,0,0.04)] justify-between lg:flex"
           :class="menu ? 'flex' : 'hidden'" @click.outside="if (window.innerWidth < 1024) menu = false">
        <div class="flex flex-col overflow-y-auto">
            <div class="h-20 px-space-lg flex items-center gap-space-sm flex-shrink-0">
                <a href="{{ route('accueil') }}"><img alt="PharmaConnect" class="h-8 w-auto object-contain" src="{{ asset('images/logo-pharmaconnect.svg') }}"/></a>
                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm text-primary tracking-tight leading-none">PharmaConnect</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Portail Professionnel</span>
                </div>
            </div>
            <div class="px-space-md py-space-xs">
                <div class="bg-surface-container-low px-space-sm py-space-xs rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-space-xs">
                        <span class="w-2 h-2 rounded-full {{ $officine?->statut === 'actif' ? 'bg-primary animate-pulse' : 'bg-outline' }}"></span>
                        <span class="font-label-md text-label-md text-on-surface">{{ $officine?->statut === 'actif' ? 'Système Actif' : 'En attente de validation' }}</span>
                    </div>
                    <span class="font-label-sm text-label-sm text-outline">{{ $officine?->quartier ?? $officine?->ville }}</span>
                </div>
            </div>
            <nav class="px-space-md py-space-sm flex flex-col gap-space-xs">
                @foreach ($liens as [$libelle, $icone, $url, $actif])
                    <a @if($actif) aria-current="page" @endif href="{{ $url }}"
                       class="flex items-center gap-space-sm px-space-md py-3 transition-colors rounded-xl {{ $actif ? 'bg-primary-container text-on-primary-container font-label-lg shadow-[0_1px_8px_rgba(0,0,0,0.04)]' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $icone }}</span>
                        <span class="font-label-lg text-label-lg">{{ $libelle }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
        <div class="p-space-md flex flex-col gap-space-sm bg-surface-container-lowest">
            <div class="bg-surface-container-low p-space-sm rounded-xl flex items-center justify-between gap-space-xs">
                <div class="flex items-center gap-space-xs min-w-0">
                    <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-lg text-label-lg flex-shrink-0">{{ initiales($utilisateur->name) }}</div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-md text-label-md text-on-surface truncate">{{ $utilisateur->name }}</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ $officine?->nom }}</span>
                        <span class="font-label-sm text-label-sm text-outline truncate">{{ collect([$officine?->ville, $officine?->quartier])->filter()->implode(' ') }}</span>
                    </div>
                </div>
                <a class="p-space-xs text-on-surface-variant hover:text-on-surface transition-colors rounded-lg" href="{{ route('profil.edit') }}" title="Paramètres du compte"><span class="material-symbols-outlined text-[20px]">settings</span></a>
            </div>
            <div class="flex items-center justify-between px-space-xs">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-space-xs text-tertiary hover:text-tertiary-container transition-colors">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        <span class="font-label-sm text-label-sm">Déconnexion sécurisée</span>
                    </button>
                </form>
                <span class="font-label-sm text-label-sm text-outline">v2.4-cm</span>
            </div>
        </div>
    </aside>

    <div class="lg:pl-72">
        <header class="fixed top-0 left-0 lg:left-72 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 px-margin md:px-space-lg flex items-center justify-between gap-space-md">
            <button type="button" class="lg:hidden w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-on-surface-variant" @click.stop="menu = ! menu" aria-label="Menu">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <form method="GET" action="{{ route('pharmacie.medicaments') }}" class="flex-1 max-w-lg">
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-4 text-primary text-[20px]">search</span>
                    <input name="q" value="{{ request()->routeIs('pharmacie.medicaments') ? request('q') : '' }}" class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-surface-container-low text-on-surface placeholder:text-outline font-body-md text-body-md border-0 focus:outline-none focus:ring-2 focus:ring-secondary-container transition-all" placeholder="Rechercher un médicament dans mon catalogue..." type="text"/>
                </div>
            </form>
            <div class="flex items-center gap-space-md">
                <div class="hidden md:flex items-center gap-space-xs bg-surface-container-low px-3 py-2 rounded-xl">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">calendar_today</span>
                    <span class="font-label-md text-label-md text-on-surface">{{ ucfirst(now()->translatedFormat('F Y')) }}</span>
                </div>
                @if ($ouverte)
                    <a href="{{ route('pharmacie.horaires') }}" class="hidden sm:flex items-center gap-2 bg-secondary-container/30 px-3 py-2 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-secondary-container animate-ping"></span>
                        <span class="material-symbols-outlined text-on-secondary-container text-[18px]">local_pharmacy</span>
                        <span class="font-label-md text-label-md text-on-secondary-container">Officine Ouverte</span>
                    </a>
                @else
                    <a href="{{ route('pharmacie.horaires') }}" class="hidden sm:flex items-center gap-2 bg-surface-container px-3 py-2 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-outline"></span>
                        <span class="material-symbols-outlined text-on-surface-variant text-[18px]">local_pharmacy</span>
                        <span class="font-label-md text-label-md text-on-surface-variant">Officine Fermée</span>
                    </a>
                @endif
                @include('partials.cloche-notifications')
                @include('partials.menu-utilisateur')
            </div>
        </header>

        <main class="w-full pt-20 px-margin md:px-space-lg pb-10 bg-background min-h-[calc(100vh-48px)]">
            @yield('contenu')
        </main>

        <footer class="w-full min-h-12 py-3 bg-surface-container-lowest px-space-lg flex flex-col sm:flex-row items-center justify-between gap-2 text-outline shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
            <div class="flex items-center gap-space-xs">
                <span class="material-symbols-outlined text-primary text-[16px]">verified</span>
                <span class="font-body-sm text-body-sm">Plateforme officinale certifiée ONPC &amp; MINSANTE Cameroun</span>
            </div>
            <div class="flex items-center gap-space-md">
                <span class="font-body-sm text-body-sm">Liaison Régionale Littoral - Douala</span>
                <span class="font-body-sm text-body-sm">Système national de traçabilité v2.4</span>
            </div>
        </footer>
    </div>

    @include('partials.flash')
    @yield('scripts')
</body>
</html>

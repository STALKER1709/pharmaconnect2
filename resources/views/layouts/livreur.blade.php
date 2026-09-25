<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
@php
    $utilisateur = auth()->user();
    $profil = $utilisateur->livreur;
    $livreesDuJour = $profil
        ? \App\Models\Livraison::where('livreur_id', $profil->id)->where('statut', 'livree')->whereDate('livree_at', today())->with('commande:id,frais_livraison')->get()
        : collect();
    $gainsDuJour = (int) $livreesDuJour->sum(fn ($l) => $l->commande?->frais_livraison ?? 0);
    $enLigne = $profil?->disponibilite !== 'hors_ligne';
    $liens = [
        ['Missions en direct', route('livreur.dashboard'), request()->routeIs('livreur.dashboard', 'livreur.livraison')],
        ['Messagerie', route('messagerie.index'), request()->routeIs('messagerie.*')],
        ['Mon véhicule', route('profil.edit'), request()->routeIs('profil.*')],
        ['Support coursier', route('chatbot.index'), request()->routeIs('chatbot.*')],
    ];
@endphp
<body class="bg-surface font-body-md text-on-surface antialiased" x-data="{ menu: false }">
    <!-- En-tête — maquette tableau_de_bord_coursier -->
    <header class="fixed top-0 w-full z-50 bg-surface/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(20,83,45,0.06)]">
        <div class="h-20 max-w-7xl mx-auto px-margin md:px-margin-desktop flex items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-md">
                <a href="{{ route('livreur.dashboard') }}" class="flex items-center gap-space-xs">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-on-primary shadow-sm">
                        <span class="material-symbols-outlined text-[24px]">local_pharmacy</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-headline-sm text-headline-sm text-primary leading-tight tracking-tight">PharmaConnect</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Douala Santé Express</span>
                    </div>
                </a>
                <div class="hidden xl:flex items-center gap-space-xs bg-surface-container-low px-3 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-primary text-[16px]">two_wheeler</span>
                    <span class="font-label-sm text-label-sm text-on-surface font-medium">Espace Coursier Express {{ $profil?->ville ?? 'Douala' }}</span>
                </div>
                <div class="hidden sm:flex items-center gap-2 {{ $enLigne ? 'bg-secondary-container/40' : 'bg-surface-container' }} px-3 py-1 rounded-full">
                    <span class="relative flex h-2.5 w-2.5">
                        @if ($enLigne)<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>@endif
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $enLigne ? 'bg-primary' : 'bg-outline' }}"></span>
                    </span>
                    <span class="font-label-md text-label-md {{ $enLigne ? 'text-on-secondary-container' : 'text-on-surface-variant' }}">{{ $enLigne ? 'En ligne' : 'Hors ligne' }}</span>
                </div>
            </div>
            <nav class="hidden lg:flex items-center gap-space-xs">
                @foreach ($liens as [$libelle, $url, $actif])
                    <a @if($actif) aria-current="page" @endif href="{{ $url }}"
                       class="{{ $actif ? 'transition-colors bg-primary-container text-on-primary-container font-semibold rounded-lg px-3 py-2' : 'text-on-surface-variant hover:text-on-surface px-3 py-2 rounded-lg font-label-lg text-label-lg transition-colors' }}">{{ $libelle }}</a>
                @endforeach
            </nav>
            <div class="flex items-center gap-space-md">
                <div class="hidden md:flex items-center gap-space-md bg-surface-container-low px-4 py-2 rounded-xl">
                    <div class="flex flex-col text-right">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Gains du jour</span>
                        <span class="font-currency-display text-currency-display text-primary leading-none whitespace-nowrap">{{ format_fcfa($gainsDuJour) }}</span>
                    </div>
                    <div class="h-6 w-px bg-outline-variant/60"></div>
                    <div class="flex items-center gap-1.5 text-on-surface">
                        <span class="material-symbols-outlined text-[18px] text-primary">task_alt</span>
                        <span class="font-label-md text-label-md whitespace-nowrap">{{ $livreesDuJour->count() }} {{ \Illuminate\Support\Str::plural('course', $livreesDuJour->count()) }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-space-sm pl-space-xs">
                    @include('partials.menu-utilisateur')
                    <div class="hidden xl:flex flex-col text-left">
                        <span class="font-label-md text-label-md text-on-surface leading-tight">{{ $utilisateur->name }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ collect([$profil?->vehicule ? ucfirst($profil->vehicule) : null, $profil?->immatriculation])->filter()->implode(' • ') }}</span>
                    </div>
                </div>
                <button type="button" class="lg:hidden w-9 h-9 rounded-xl flex items-center justify-center text-on-surface-variant hover:bg-surface-container" @click="menu = ! menu" aria-label="Menu">
                    <span class="material-symbols-outlined text-[22px]" x-text="menu ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>
        <nav class="lg:hidden bg-surface-container-lowest px-margin py-space-sm flex flex-col gap-space-xs" x-show="menu" x-cloak @click.outside="menu = false">
            @foreach ($liens as [$libelle, $url, $actif])
                <a href="{{ $url }}" class="px-3 py-2 rounded-lg font-label-lg text-label-lg {{ $actif ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant' }}">{{ $libelle }}</a>
            @endforeach
        </nav>
    </header>

    <main class="w-full pt-20 bg-surface min-h-screen">
        @yield('contenu')
    </main>

    <footer class="w-full bg-surface-container-low shadow-[0_-1px_8px_rgba(20,83,45,0.03)] py-8">
        <div class="max-w-7xl mx-auto px-margin md:px-margin-desktop flex flex-col sm:flex-row items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-xs text-on-surface-variant">
                <span class="material-symbols-outlined text-primary text-[20px]">verified</span>
                <span class="font-body-sm text-body-sm">PharmaConnect Logistique Santé Cameroun • Douala • Yaoundé • Bafoussam</span>
            </div>
            <div class="flex items-center gap-space-md text-on-surface-variant font-label-sm text-label-sm">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-primary">emergency</span>Urgence médicale: 8100 (Gratuit)</span>
                <span>© {{ now()->year }} PharmaConnect Express</span>
            </div>
        </div>
    </footer>

    @include('partials.flash')
    @yield('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titre', 'PharmaConnect') — PharmaConnect</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💊</text></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="min-h-screen bg-menthe-50">
<div class="flex min-h-screen flex-col">

    <header class="sticky top-0 z-40 border-b border-menthe-100 bg-white/90 backdrop-blur">
        <nav x-data="{ ouvert: false }" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ route('accueil') }}" class="flex items-center gap-2 text-xl font-black text-menthe-700">
                    <span class="text-2xl">💊</span> Pharma<span class="text-slate-900">Connect</span>
                </a>

                <div class="hidden items-center gap-1 md:flex">
                    @guest
                        <a href="{{ route('public.pharmacies') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Pharmacies</a>
                        <a href="{{ route('public.medicaments') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Médicaments</a>
                        <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-primary ml-2">Créer un compte</a>
                    @endguest

                    @auth
                        @if(auth()->user()->estClient())
                            <a href="{{ route('public.medicaments') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Rechercher</a>
                            <a href="{{ route('commandes.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Mes commandes</a>
                            <a href="{{ route('panier.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">🛒 Panier</a>
                        @elseif(auth()->user()->estPharmacie())
                            <a href="{{ route('pharmacie.medicaments') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Catalogue</a>
                            <a href="{{ route('pharmacie.commandes') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Commandes</a>
                            <a href="{{ route('pharmacie.statistiques') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Statistiques</a>
                        @elseif(auth()->user()->estLivreur())
                            <a href="{{ route('livreur.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Mes livraisons</a>
                        @elseif(auth()->user()->estAdmin())
                            <a href="{{ route('admin.utilisateurs') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-menthe-50">Utilisateurs</a>
                        @endif
                    @endauth
                </div>

                <div class="hidden items-center gap-3 md:flex">
                    @auth
                        <a href="{{ route('messagerie.index') }}" title="Messagerie" class="rounded-lg px-3 py-2 text-lg hover:bg-menthe-50">💬</a>
                        <a href="{{ route('chatbot.index') }}" title="Assistant santé" class="rounded-lg px-3 py-2 text-lg hover:bg-menthe-50">🤖</a>
                        <div x-data="{ menu: false }" class="relative">
                            <button @click="menu = ! menu" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-semibold hover:bg-menthe-50">
                                <span class="grid h-8 w-8 place-items-center rounded-full bg-menthe-600 text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                {{ Str::limit(auth()->user()->name, 14) }}
                            </button>
                            <div x-show="menu" @click.outside="menu = false" x-cloak class="absolute right-0 mt-2 w-52 rounded-xl border border-menthe-100 bg-white p-2 shadow-lg">
                                <a href="{{ route('profil.edit') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-menthe-50">Mon profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>

                <button class="md:hidden" @click="ouvert = ! ouvert" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>

            <div x-show="ouvert" x-cloak class="space-y-1 pb-4 md:hidden">
                @guest
                    <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2">Connexion</a>
                    <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2">Créer un compte</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2">Tableau de bord</a>
                    <a href="{{ route('messagerie.index') }}" class="block rounded-lg px-3 py-2">💬 Messagerie</a>
                    <a href="{{ route('chatbot.index') }}" class="block rounded-lg px-3 py-2">🤖 Assistant</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="block w-full px-3 py-2 text-left text-red-600">Déconnexion</button>
                    </form>
                @endauth
            </div>
        </nav>
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('succes'))
            <div class="mb-6 rounded-xl border border-menthe-200 bg-menthe-100 px-4 py-3 text-sm text-menthe-800">{{ session('succes') }}</div>
        @endif
        @if (session('erreur'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('erreur') }}</div>
        @endif

        @yield('contenu')
    </main>

    <footer class="border-t border-menthe-100 bg-white py-6 text-center text-sm text-slate-500">
        PharmaConnect — Médicaments à domicile au Cameroun 🇨🇲 · Prix en FCFA · Heure de Douala
    </footer>

    @yield('scripts')
</div>
</body>
</html>

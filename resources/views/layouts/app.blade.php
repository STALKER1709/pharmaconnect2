<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titre', 'Accueil') — PharmaConnect</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💊</text></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body>
<div class="page">

    <header class="barre-nav">
        <nav class="conteneur" x-data="{ ouvert: false }">
            <div class="barre-nav-interieur">
                <a href="{{ route('accueil') }}" class="logo">
                    <span class="logo-icone">💊</span> Pharma<span style="color:var(--encre);">Connect</span>
                </a>

                <div class="nav-liens">
                    @guest
                        <a href="{{ route('public.pharmacies') }}" class="nav-lien">Pharmacies</a>
                        <a href="{{ route('public.medicaments') }}" class="nav-lien">Médicaments</a>
                    @endguest
                    @auth
                        @if(auth()->user()->estClient())
                            <a href="{{ route('public.medicaments') }}" class="nav-lien">Rechercher</a>
                            <a href="{{ route('commandes.index') }}" class="nav-lien">Mes commandes</a>
                            <a href="{{ route('panier.index') }}" class="nav-lien">🛒 Panier</a>
                        @elseif(auth()->user()->estPharmacie())
                            <a href="{{ route('pharmacie.medicaments') }}" class="nav-lien">Catalogue</a>
                            <a href="{{ route('pharmacie.commandes') }}" class="nav-lien">Commandes</a>
                            <a href="{{ route('pharmacie.statistiques') }}" class="nav-lien">Statistiques</a>
                        @elseif(auth()->user()->estLivreur())
                            <a href="{{ route('livreur.dashboard') }}" class="nav-lien">Mes livraisons</a>
                        @elseif(auth()->user()->estAdmin())
                            <a href="{{ route('admin.utilisateurs') }}" class="nav-lien">Utilisateurs</a>
                        @endif
                    @endauth
                </div>

                <div class="nav-actions">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-secondaire">Connexion</a>
                        <a href="{{ route('register') }}" class="btn btn-primaire">Créer un compte</a>
                    @endguest
                    @auth
                        <a href="{{ route('messagerie.index') }}" title="Messagerie" class="nav-lien">💬</a>
                        <a href="{{ route('chatbot.index') }}" title="Assistant santé" class="nav-lien">🤖</a>
                        <div class="menu-user" x-data="{ menu: false }">
                            <button type="button" @click="menu = ! menu" class="nav-lien" style="gap:8px;">
                                <span class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                {{ Str::limit(auth()->user()->name, 14) }}
                            </button>
                            <div class="menu-user-panneau" x-show="menu" @click.outside="menu = false" x-cloak>
                                <a href="{{ route('profil.edit') }}" class="menu-user-lien">Mon profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="menu-user-lien danger">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>

                <button type="button" class="nav-toggle" @click="ouvert = ! ouvert" aria-label="Menu">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>

            <div class="nav-mobile" :class="ouvert ? 'ouvert' : ''" x-show="ouvert" x-cloak>
                @guest
                    <a href="{{ route('login') }}">Connexion</a>
                    <a href="{{ route('register') }}">Créer un compte</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}">Tableau de bord</a>
                    <a href="{{ route('messagerie.index') }}">💬 Messagerie</a>
                    <a href="{{ route('chatbot.index') }}">🤖 Assistant</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="bloc" style="color:var(--rouge-600);">Déconnexion</button>
                    </form>
                @endauth
            </div>
        </nav>
    </header>

    <main class="conteneur contenu">
        @if (session('succes'))
            <div class="flash flash-succes">{{ session('succes') }}</div>
        @endif
        @if (session('erreur'))
            <div class="flash flash-erreur">{{ session('erreur') }}</div>
        @endif

        @yield('contenu')
    </main>

    <footer class="pied">
        PharmaConnect — Médicaments à domicile au Cameroun 🇨🇲 · Prix en FCFA · Heure de Douala
    </footer>

    @yield('scripts')
</div>
</body>
</html>

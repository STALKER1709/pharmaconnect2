<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titre', 'PharmaConnect')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💊</text></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-ecran">
    <div class="auth-panneau">
        <a href="{{ route('accueil') }}" class="logo" style="color:#fff;"><span class="logo-icone">💊</span> PharmaConnect</a>
        <div>
            <h2 style="color:#fff; font-size:28px;">Vos médicaments livrés<br>à Douala, en quelques minutes.</h2>
            <p style="margin-top:12px; color:var(--vert-100); max-width:420px;">
                Pharmacies vérifiées, paiement MTN MoMo &amp; Orange Money, suivi de livraison en temps réel sur la carte.
            </p>
            <div class="auth-atouts">
                <div><span>🛵</span>Livraison rapide</div>
                <div><span>🛡️</span>Pharmacies agréées</div>
                <div><span>📱</span>MoMo &amp; OM</div>
            </div>
        </div>
        <p class="texte-petit" style="color:var(--vert-200);">Prix en FCFA · Fuseau Africa/Douala</p>
    </div>

    <div class="auth-formulaire">
        <div style="width:100%; max-width:440px;">
            @if (session('succes'))
                <div class="flash flash-succes">{{ session('succes') }}</div>
            @endif
            @if (session('erreur'))
                <div class="flash flash-erreur">{{ session('erreur') }}</div>
            @endif

            @yield('contenu')
        </div>
    </div>
</div>
</body>
</html>

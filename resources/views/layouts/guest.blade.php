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
<body class="min-h-screen bg-menthe-50">
<div class="flex min-h-screen">
    <div class="hidden flex-1 bg-gradient-to-br from-menthe-600 to-menthe-800 p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <a href="{{ route('accueil') }}" class="text-2xl font-black">💊 PharmaConnect</a>
        <div class="space-y-4">
            <h2 class="text-3xl font-black leading-tight">Vos médicaments livrés<br>à Douala, en quelques minutes.</h2>
            <p class="max-w-md text-menthe-100">
                Pharmacies vérifiées, paiement MTN MoMo &amp; Orange Money, suivi de livraison en temps réel sur la carte.
            </p>
            <div class="flex gap-6 text-sm text-menthe-100">
                <div><span class="block text-2xl font-black text-white">🛵</span>Livraison rapide</div>
                <div><span class="block text-2xl font-black text-white">🔒</span>Pharmacies agréées</div>
                <div><span class="block text-2xl font-black text-white">📱</span>MoMo &amp; OM</div>
            </div>
        </div>
        <p class="text-xs text-menthe-200">Prix en FCFA · Fuseau Africa/Douala</p>
    </div>

    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            @yield('contenu')
        </div>
    </div>
</div>
</body>
</html>

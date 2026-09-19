@extends('layouts.guest')

@section('titre', 'Connexion')

@section('contenu')
<div class="card p-8">
    <h1 class="text-2xl font-black">Connexion</h1>
    <p class="mt-1 text-sm text-slate-500">Bon retour parmi nous !</p>

    @if (session('statut') === 'en_attente')
        <div class="mt-4 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">
            ⏳ Votre compte (pharmacie ou livreur) est en attente de validation par l'administrateur.
        </div>
    @elseif (session('statut') === 'inscription_en_attente')
        <div class="mt-4 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">
            ✅ Inscription enregistrée ! Votre compte sera activé après validation par l'administrateur. Vous serez notifié par e-mail.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="label">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input @error('email') ring-red-400 @enderror">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="label">Mot de passe</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-menthe-700 hover:underline">Mot de passe oublié ?</a>
                @endif
            </div>
            <input id="password" name="password" type="password" required class="input @error('password') ring-red-400 @enderror">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-menthe-300 text-menthe-600 focus:ring-menthe-500">
            Se souvenir de moi
        </label>

        <button class="btn-primary w-full">Se connecter</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Pas encore de compte ? <a href="{{ route('register') }}" class="font-semibold text-menthe-700 hover:underline">Créer un compte</a>
    </p>

    <div class="mt-6 rounded-xl bg-menthe-50 p-4 text-xs text-slate-600">
        <p class="font-semibold">Comptes de démonstration (mot de passe : <code>password</code>) :</p>
        <p>👤 client@pharmaconnect.cm · 🏥 fondateur@pharmaconnect.cm · 🛵 livreur@pharmaconnect.cm · 👑 admin@pharmaconnect.cm</p>
    </div>
</div>
@endsection

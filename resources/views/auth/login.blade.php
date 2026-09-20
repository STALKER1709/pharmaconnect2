@extends('layouts.guest')

@section('titre', 'Connexion')

@section('contenu')
<div class="carte auth-carte">
    <h1 class="titre-page" style="font-size:24px;">Connexion</h1>
    <p class="sous-titre">Bon retour parmi nous !</p>

    @if (session('statut') === 'en_attente')
        <div class="alerte alerte-ambre mt-4">⏳ Votre compte (pharmacie ou livreur) est en attente de validation par l'administrateur.</div>
    @elseif (session('statut') === 'inscription_en_attente')
        <div class="alerte alerte-ambre mt-4">✅ Inscription enregistrée ! Votre compte sera activé après validation par l'administrateur. Vous serez notifié par e-mail.</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6" style="display:grid; gap:16px;">
        @csrf

        <div>
            <label for="email" class="champ-label">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="champ @error('email') champ-erreur @enderror">
            @error('email') <p class="erreur-texte">{{ $message }}</p> @enderror
        </div>

        <div>
            <div class="rangee-entre">
                <label for="password" class="champ-label">Mot de passe</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="texte-petit" style="color:var(--vert-700);">Mot de passe oublié ?</a>
                @endif
            </div>
            <input id="password" name="password" type="password" required class="champ @error('password') champ-erreur @enderror">
            @error('password') <p class="erreur-texte">{{ $message }}</p> @enderror
        </div>

        <label class="rangée texte-petit" style="gap:8px;">
            <input type="checkbox" name="remember" class="case-a-cocher">
            Se souvenir de moi
        </label>

        <button class="btn btn-primaire" style="width:100%;">Se connecter</button>
    </form>

    <p class="mt-6 texte-petit texte-doux" style="text-align:center;">
        Pas encore de compte ? <a href="{{ route('register') }}" style="color:var(--vert-700); font-weight:600;">Créer un compte</a>
    </p>

    <div class="alerte alerte-info mt-4" style="display:block;">
        <p style="font-weight:600;">Comptes de démonstration (mot de passe : <code>password</code>) :</p>
        <p>👤 client@pharmaconnect.cm · 🏥 fondateur@pharmaconnect.cm · 🛵 livreur@pharmaconnect.cm · 👑 admin@pharmaconnect.cm</p>
    </div>
</div>
@endsection

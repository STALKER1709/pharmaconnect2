@extends('layouts.guest')

@section('titre', 'Mot de passe oublié')

@section('contenu')
<div class="carte auth-carte">
    <h1 class="titre-page" style="font-size:24px;">Mot de passe oublié ?</h1>
    <p class="sous-titre">Indiquez votre e-mail — le lien de réinitialisation est écrit dans <code>storage/logs/laravel.log</code> (mode local).</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-6" style="display:grid; gap:16px;">
        @csrf
        <div>
            <label for="email" class="champ-label">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="champ">
            @error('email') <p class="erreur-texte">{{ $message }}</p> @enderror
        </div>
        <button class="btn btn-primaire" style="width:100%;">Envoyer le lien</button>
    </form>

    <p class="mt-6" style="text-align:center;">
        <a href="{{ route('login') }}" style="color:var(--vert-700); font-weight:600;">← Retour à la connexion</a>
    </p>
</div>
@endsection

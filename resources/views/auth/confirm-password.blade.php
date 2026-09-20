@extends('layouts.guest')

@section('titre', 'Confirmez votre mot de passe')

@section('contenu')
<div class="carte auth-carte">
    <h1 class="titre-page" style="font-size:24px;">Confirmation requise</h1>
    <p class="sous-titre">Saisissez à nouveau votre mot de passe pour continuer.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-6" style="display:grid; gap:16px;">
        @csrf
        <div>
            <label for="password" class="champ-label">Mot de passe</label>
            <input id="password" name="password" type="password" required class="champ">
            @error('password') <p class="erreur-texte">{{ $message }}</p> @enderror
        </div>
        <button class="btn btn-primaire" style="width:100%;">Confirmer</button>
    </form>
</div>
@endsection

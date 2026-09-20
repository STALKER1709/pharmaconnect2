@extends('layouts.guest')

@section('titre', 'Nouveau mot de passe')

@section('contenu')
<div class="carte auth-carte">
    <h1 class="titre-page" style="font-size:24px;">Nouveau mot de passe</h1>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6" style="display:grid; gap:16px;">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="champ-label">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="champ">
            @error('email') <p class="erreur-texte">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="champ-label">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" required class="champ">
            @error('password') <p class="erreur-texte">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="champ-label">Confirmer</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="champ">
        </div>

        <button class="btn btn-primaire" style="width:100%;">Réinitialiser</button>
    </form>
</div>
@endsection

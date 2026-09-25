@extends('layouts.guest')

@section('titre', 'Nouveau mot de passe')

@section('contenu')
    @include('auth.partials.entete', ['icone' => 'lock_reset', 'titre' => 'Nouveau mot de passe', 'sousTitre' => 'Choisissez un mot de passe robuste pour sécuriser votre compte.'])

    <form method="POST" action="{{ route('password.store') }}" class="flex flex-col gap-space-md">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <x-champ label="Adresse e-mail" name="email" type="email" icone="mail" :value="$request->email" required autofocus/>
        <x-champ label="Nouveau mot de passe" name="password" type="password" icone="lock" required autocomplete="new-password"/>
        <x-champ label="Confirmer le mot de passe" name="password_confirmation" type="password" icone="lock" required autocomplete="new-password"/>
        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm transition-colors"><span class="material-symbols-outlined text-[20px]">check</span>Réinitialiser</button>
    </form>
@endsection

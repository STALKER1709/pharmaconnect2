@extends('layouts.guest')

@section('titre', 'Confirmez votre mot de passe')

@section('contenu')
    @include('auth.partials.entete', ['icone' => 'shield_lock', 'titre' => 'Confirmation requise', 'sousTitre' => 'Zone sécurisée : saisissez à nouveau votre mot de passe pour continuer.'])

    <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-space-md">
        @csrf
        <x-champ label="Mot de passe" name="password" type="password" icone="lock" required autocomplete="current-password"/>
        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm transition-colors">Confirmer</button>
    </form>
@endsection

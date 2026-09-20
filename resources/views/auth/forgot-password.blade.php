@extends('layouts.guest')

@section('titre', 'Mot de passe oublié')

@section('contenu')
<div class="card p-8">
    <h1 class="text-2xl font-black">Mot de passe oublié ?</h1>
    <p class="mt-1 text-sm text-slate-500">Indiquez votre e-mail — le lien de réinitialisation est écrit dans <code>storage/logs/laravel.log</code> (mode local).</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="email" class="label">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <button class="btn-primary w-full">Envoyer le lien</button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="text-menthe-700 hover:underline">← Retour à la connexion</a>
    </p>
</div>
@endsection

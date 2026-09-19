@extends('layouts.guest')

@section('titre', 'Nouveau mot de passe')

@section('contenu')
<div class="card p-8">
    <h1 class="text-2xl font-black">Nouveau mot de passe</h1>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="label">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="label">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" required class="input">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirmer</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="input">
        </div>

        <button class="btn-primary w-full">Réinitialiser</button>
    </form>
</div>
@endsection

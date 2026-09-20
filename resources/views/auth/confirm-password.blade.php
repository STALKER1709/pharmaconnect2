@extends('layouts.guest')

@section('titre', 'Confirmez votre mot de passe')

@section('contenu')
<div class="card p-8">
    <h1 class="text-2xl font-black">Confirmation requise</h1>
    <p class="mt-1 text-sm text-slate-500">Saisissez à nouveau votre mot de passe pour continuer.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="password" class="label">Mot de passe</label>
            <input id="password" name="password" type="password" required class="input">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <button class="btn-primary w-full">Confirmer</button>
    </form>
</div>
@endsection

@extends('layouts.guest')

@section('titre', 'Créer un compte')

@section('contenu')
<div class="card p-8"
     x-data="{ role: '{{ old('role', 'client') }}' }">
    <h1 class="text-2xl font-black">Créer un compte</h1>
    <p class="mt-1 text-sm text-slate-500">Rejoignez PharmaConnect — gratuit.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        {{-- Choix du rôle --}}
        <div class="grid grid-cols-3 gap-2">
            @foreach(['client' => '👤 Client', 'pharmacie' => '🏥 Pharmacie', 'livreur' => '🛵 Livreur'] as $valeur => $libelle)
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="{{ $valeur }}" class="peer sr-only" @checked(old('role', 'client') === $valeur) x-model="role">
                    <div class="rounded-xl border border-menthe-100 px-2 py-3 text-center text-sm font-semibold text-slate-600 peer-checked:border-menthe-500 peer-checked:bg-menthe-50 peer-checked:text-menthe-800">
                        {{ $libelle }}
                    </div>
                </label>
            @endforeach
        </div>
        <p class="text-xs text-slate-400">Les comptes pharmacie et livreur sont activés après validation par l'administrateur.</p>

        <div>
            <label for="name" class="label">Nom complet</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="input">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="email" class="label">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="telephone" class="label">Téléphone (Cameroun)</label>
                <input id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="690123456" required class="input">
                @error('telephone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Champs pharmacie --}}
        <div x-show="role === 'pharmacie'" x-cloak class="space-y-4 rounded-xl bg-menthe-50 p-4">
            <div>
                <label for="nom_pharmacie" class="label">Nom de la pharmacie</label>
                <input id="nom_pharmacie" name="nom_pharmacie" value="{{ old('nom_pharmacie') }}" class="input">
                @error('nom_pharmacie') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="adresse_pharmacie" class="label">Adresse de la pharmacie</label>
                <input id="adresse_pharmacie" name="adresse_pharmacie" value="{{ old('adresse_pharmacie') }}" class="input">
                @error('adresse_pharmacie') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Champs livreur --}}
        <div x-show="role === 'livreur'" x-cloak class="space-y-4 rounded-xl bg-menthe-50 p-4">
            <div>
                <label for="vehicule" class="label">Véhicule</label>
                <select id="vehicule" name="vehicule" class="input">
                    <option value="moto" @selected(old('vehicule') === 'moto')>Moto</option>
                    <option value="voiture" @selected(old('vehicule') === 'voiture')>Voiture</option>
                    <option value="velo" @selected(old('vehicule') === 'velo')>Vélo</option>
                </select>
                @error('vehicule') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="immatriculation" class="label">Immatriculation (optionnel)</label>
                <input id="immatriculation" name="immatriculation" value="{{ old('immatriculation') }}" class="input">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="label">Mot de passe</label>
                <input id="password" name="password" type="password" required class="input">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="label">Confirmer</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="input">
            </div>
        </div>

        <button class="btn-primary w-full">Créer mon compte</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Déjà inscrit ? <a href="{{ route('login') }}" class="font-semibold text-menthe-700 hover:underline">Se connecter</a>
    </p>
</div>
@endsection

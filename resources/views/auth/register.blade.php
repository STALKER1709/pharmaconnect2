@extends('layouts.guest')

@section('titre', 'Créer un compte')

@section('contenu')
<div class="carte auth-carte"
     x-data="{ role: '{{ old('role', 'client') }}' }">
    <h1 class="titre-page" style="font-size:24px;">Créer un compte</h1>
    <p class="sous-titre">Rejoignez PharmaConnect — gratuit.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6" style="display:grid; gap:16px;">
        @csrf

        {{-- Choix du rôle --}}
        <div>
            <div class="role-grille">
                @foreach(['client' => '👤 Client', 'pharmacie' => '🏥 Pharmacie', 'livreur' => '🛵 Livreur'] as $valeur => $libelle)
                    <label class="role-option">
                        <input type="radio" name="role" value="{{ $valeur }}" @checked(old('role', 'client') === $valeur) x-model="role">
                        <span>{{ $libelle }}</span>
                    </label>
                @endforeach
            </div>
            <p class="texte-petit texte-doux mt-2">Les comptes pharmacie et livreur sont activés après validation par l'administrateur.</p>
        </div>

        <div>
            <label for="name" class="champ-label">Nom complet</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="champ">
            @error('name') <p class="erreur-texte">{{ $message }}</p> @enderror
        </div>

        <div class="role-grille-2">
            <div>
                <label for="email" class="champ-label">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="champ">
                @error('email') <p class="erreur-texte">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="telephone" class="champ-label">Téléphone (Cameroun)</label>
                <input id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="690123456" required class="champ">
                @error('telephone') <p class="erreur-texte">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Champs pharmacie --}}
        <div x-show="role === 'pharmacie'" x-cloak class="bloc-role">
            <div>
                <label for="nom_pharmacie" class="champ-label">Nom de la pharmacie</label>
                <input id="nom_pharmacie" name="nom_pharmacie" value="{{ old('nom_pharmacie') }}" class="champ">
                @error('nom_pharmacie') <p class="erreur-texte">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="adresse_pharmacie" class="champ-label">Adresse de la pharmacie</label>
                <input id="adresse_pharmacie" name="adresse_pharmacie" value="{{ old('adresse_pharmacie') }}" class="champ">
                @error('adresse_pharmacie') <p class="erreur-texte">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Champs livreur --}}
        <div x-show="role === 'livreur'" x-cloak class="bloc-role">
            <div>
                <label for="vehicule" class="champ-label">Véhicule</label>
                <select id="vehicule" name="vehicule" class="champ">
                    <option value="moto" @selected(old('vehicule') === 'moto')>Moto</option>
                    <option value="voiture" @selected(old('vehicule') === 'voiture')>Voiture</option>
                    <option value="velo" @selected(old('vehicule') === 'velo')>Vélo</option>
                </select>
                @error('vehicule') <p class="erreur-texte">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="immatriculation" class="champ-label">Immatriculation (optionnel)</label>
                <input id="immatriculation" name="immatriculation" value="{{ old('immatriculation') }}" class="champ">
            </div>
        </div>

        <div class="role-grille-2">
            <div>
                <label for="password" class="champ-label">Mot de passe</label>
                <input id="password" name="password" type="password" required class="champ">
                @error('password') <p class="erreur-texte">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="champ-label">Confirmer</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="champ">
            </div>
        </div>

        <button class="btn btn-primaire" style="width:100%;">Créer mon compte</button>
    </form>

    <p class="mt-6 texte-petit texte-doux" style="text-align:center;">
        Déjà inscrit ? <a href="{{ route('login') }}" style="color:var(--vert-700); font-weight:600;">Se connecter</a>
    </p>
</div>
@endsection

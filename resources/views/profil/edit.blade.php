@extends('layouts.app')

@section('titre', 'Mon profil')

@section('contenu')
<div style="max-width:720px; margin:0 auto;">
    <h1 class="titre-page mb-6">Mon profil</h1>

    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="carte carte-corps mb-6" style="display:grid; gap:16px;">
        @csrf
        @method('PUT')

        <div class="role-grille-2">
            <div>
                <label class="champ-label">Nom complet</label>
                <input name="name" value="{{ old('name', $user->name) }}" required class="champ">
            </div>
            <div>
                <label class="champ-label">Téléphone</label>
                <input name="telephone" value="{{ old('telephone', $user->telephone) }}" class="champ" placeholder="690123456">
                @error('telephone') <p class="erreur-texte">{{ $message }}</p> @enderror
            </div>
        </div>

        @if($user->estClient() && $user->client)
            <h2 class="carte-titre" style="font-size:15px; border-top:1px solid var(--bord); padding-top:16px;">📍 Adresse par défaut</h2>
            <div class="role-grille-2">
                <div>
                    <label class="champ-label">Adresse</label>
                    <input name="adresse" value="{{ old('adresse', $user->client->adresse) }}" class="champ">
                </div>
                <div>
                    <label class="champ-label">Quartier</label>
                    <input name="quartier" value="{{ old('quartier', $user->client->quartier) }}" class="champ">
                </div>
                <input type="hidden" name="ville" value="Douala">
            </div>
        @endif

        @if($user->estPharmacie() && $user->pharmacie)
            <h2 class="carte-titre" style="font-size:15px; border-top:1px solid var(--bord); padding-top:16px;">🏥 Ma pharmacie</h2>
            <div>
                <label class="champ-label">Description</label>
                <textarea name="description" rows="2" class="champ">{{ old('description', $user->pharmacie->description) }}</textarea>
            </div>
            <div class="role-grille-2">
                <div>
                    <label class="champ-label">Frais de livraison (FCFA)</label>
                    <input type="number" name="frais_livraison" min="0" value="{{ old('frais_livraison', $user->pharmacie->frais_livraison) }}" class="champ">
                </div>
                <div>
                    <label class="champ-label">Photo (facultatif)</label>
                    <input type="file" name="photo" accept="image/*" class="champ">
                </div>
            </div>
        @endif

        @if($user->estLivreur() && $user->livreur)
            <h2 class="carte-titre" style="font-size:15px; border-top:1px solid var(--bord); padding-top:16px;">🛵 Mes informations livreur</h2>
            <div class="role-grille-2">
                <div>
                    <label class="champ-label">Véhicule</label>
                    <select name="vehicule" class="champ">
                        @foreach(['moto' => 'Moto', 'voiture' => 'Voiture', 'velo' => 'Vélo'] as $v => $l)
                            <option value="{{ $v }}" @selected(old('vehicule', $user->livreur->vehicule) === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="champ-label">Immatriculation</label>
                    <input name="immatriculation" value="{{ old('immatriculation', $user->livreur->immatriculation) }}" class="champ">
                </div>
            </div>
        @endif

        <button class="btn btn-primaire" style="justify-self:start;">Enregistrer</button>
    </form>

    {{-- Changement de mot de passe --}}
    <form action="{{ route('password.update') }}" method="POST" class="carte carte-corps" style="display:grid; gap:16px;">
        @csrf
        @method('PUT')
        <h2 class="carte-titre" style="font-size:15px;">🔒 Changer de mot de passe</h2>
        <div>
            <label class="champ-label">Mot de passe actuel</label>
            <input type="password" name="current_password" required class="champ">
            @error('current_password') <p class="erreur-texte">{{ $message }}</p> @enderror
        </div>
        <div class="role-grille-2">
            <div>
                <label class="champ-label">Nouveau mot de passe</label>
                <input type="password" name="password" required class="champ">
                @error('password') <p class="erreur-texte">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="champ-label">Confirmer</label>
                <input type="password" name="password_confirmation" required class="champ">
            </div>
        </div>
        <button class="btn btn-secondaire" style="justify-self:start;">Mettre à jour le mot de passe</button>
    </form>
</div>
@endsection

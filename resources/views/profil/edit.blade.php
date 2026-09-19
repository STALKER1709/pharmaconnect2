@extends('layouts.app')

@section('titre', 'Mon profil')

@section('contenu')
<div class="mx-auto max-w-2xl space-y-6">
    <h1 class="text-2xl font-black">Mon profil</h1>

    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="card space-y-4 p-6">
        @csrf
        @method('PUT')

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Nom complet</label>
                <input name="name" value="{{ old('name', $user->name) }}" required class="input">
            </div>
            <div>
                <label class="label">Téléphone</label>
                <input name="telephone" value="{{ old('telephone', $user->telephone) }}" class="input" placeholder="690123456">
                @error('telephone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        @if($user->estClient() && $user->client)
            <h2 class="border-t border-menthe-50 pt-4 font-bold">📍 Adresse par défaut</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Adresse</label>
                    <input name="adresse" value="{{ old('adresse', $user->client->adresse) }}" class="input">
                </div>
                <div>
                    <label class="label">Quartier</label>
                    <input name="quartier" value="{{ old('quartier', $user->client->quartier) }}" class="input">
                </div>
                <input type="hidden" name="ville" value="Douala">
            </div>
        @endif

        @if($user->estPharmacie() && $user->pharmacie)
            <h2 class="border-t border-menthe-50 pt-4 font-bold">🏥 Ma pharmacie</h2>
            <div>
                <label class="label">Description</label>
                <textarea name="description" rows="2" class="input">{{ old('description', $user->pharmacie->description) }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Frais de livraison (FCFA)</label>
                    <input type="number" name="frais_livraison" min="0" value="{{ old('frais_livraison', $user->pharmacie->frais_livraison) }}" class="input">
                </div>
                <div>
                    <label class="label">Photo (facultatif)</label>
                    <input type="file" name="photo" accept="image/*" class="input">
                </div>
            </div>
        @endif

        @if($user->estLivreur() && $user->livreur)
            <h2 class="border-t border-menthe-50 pt-4 font-bold">🛵 Mes informations livreur</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Véhicule</label>
                    <select name="vehicule" class="input">
                        @foreach(['moto' => 'Moto', 'voiture' => 'Voiture', 'velo' => 'Vélo'] as $v => $l)
                            <option value="{{ $v }}" @selected(old('vehicule', $user->livreur->vehicule) === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Immatriculation</label>
                    <input name="immatriculation" value="{{ old('immatriculation', $user->livreur->immatriculation) }}" class="input">
                </div>
            </div>
        @endif

        <button class="btn-primary">Enregistrer</button>
    </form>

    {{-- Changement de mot de passe --}}
    <form action="{{ route('password.update') }}" method="POST" class="card space-y-4 p-6">
        @csrf
        @method('PUT')
        <h2 class="font-bold">🔒 Changer de mot de passe</h2>
        <div>
            <label class="label">Mot de passe actuel</label>
            <input type="password" name="current_password" required class="input">
            @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Nouveau mot de passe</label>
                <input type="password" name="password" required class="input">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Confirmer</label>
                <input type="password" name="password_confirmation" required class="input">
            </div>
        </div>
        <button class="btn-secondary">Mettre à jour le mot de passe</button>
    </form>
</div>
@endsection

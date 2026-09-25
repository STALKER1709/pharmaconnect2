@extends('layouts.guest')

@section('titre', 'Créer un compte')

@section('contenu')
<div x-data="{ role: '{{ old('role', request('role', 'client')) }}' }">
    @include('auth.partials.entete', ['icone' => 'person_add', 'titre' => 'Créer un compte', 'sousTitre' => 'Rejoignez PharmaConnect gratuitement.'])

    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-space-md">
        @csrf
        <div>
            <span class="block font-label-md text-label-md text-on-surface mb-1.5">Je suis</span>
            <div class="grid grid-cols-3 gap-2">
                @foreach (['client' => ['person', 'Patient'], 'pharmacie' => ['local_pharmacy', 'Pharmacie'], 'livreur' => ['two_wheeler', 'Coursier']] as $valeur => [$icone, $libelle])
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="{{ $valeur }}" x-model="role" class="sr-only" @checked(old('role', request('role', 'client')) === $valeur)>
                        <span class="flex flex-col items-center gap-1 p-3 rounded-xl transition-all font-label-md text-label-md"
                              :class="role === '{{ $valeur }}' ? 'bg-[#dcfce9] text-[#14532d] ring-2 ring-primary shadow-sm' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container'">
                            <span class="material-symbols-outlined text-[24px]">{{ $icone }}</span>
                            {{ $libelle }}
                        </span>
                    </label>
                @endforeach
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1.5" x-show="role !== 'client'" x-cloak>Les comptes pharmacie et coursier sont activés après validation par l'administrateur.</p>
        </div>

        <x-champ label="Nom complet" name="name" icone="person" required autofocus autocomplete="name"/>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
            <x-champ label="E-mail" name="email" type="email" icone="mail" required autocomplete="username"/>
            <x-champ label="Téléphone (Cameroun)" name="telephone" type="tel" icone="call" placeholder="690123456" required/>
        </div>

        <div x-show="role === 'pharmacie'" x-cloak class="p-4 rounded-xl bg-surface-container-low/60 flex flex-col gap-space-md">
            <p class="font-label-md text-label-md text-primary flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">local_pharmacy</span>Votre officine</p>
            <x-champ label="Nom de la pharmacie" name="nom_pharmacie" icone="storefront"/>
            <x-champ label="Adresse de la pharmacie" name="adresse_pharmacie" icone="pin_drop" placeholder="Quartier, rue, repère"/>
        </div>

        <div x-show="role === 'livreur'" x-cloak class="p-4 rounded-xl bg-surface-container-low/60 grid grid-cols-1 sm:grid-cols-2 gap-space-md">
            <x-champ label="Véhicule" name="vehicule" type="select" icone="two_wheeler">
                @foreach (['moto' => 'Moto', 'voiture' => 'Voiture', 'velo' => 'Vélo'] as $valeur => $libelle)
                    <option value="{{ $valeur }}" @selected(old('vehicule') === $valeur)>{{ $libelle }}</option>
                @endforeach
            </x-champ>
            <x-champ label="Immatriculation (optionnel)" name="immatriculation" icone="badge"/>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
            <x-champ label="Mot de passe" name="password" type="password" icone="lock" required autocomplete="new-password"/>
            <x-champ label="Confirmer" name="password_confirmation" type="password" icone="lock" required autocomplete="new-password"/>
        </div>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm transition-colors">
            <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
            Créer mon compte
        </button>
    </form>

    <p class="mt-space-lg font-body-md text-body-md text-on-surface-variant text-center">
        Déjà inscrit ? <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Se connecter</a>
    </p>
</div>
@endsection

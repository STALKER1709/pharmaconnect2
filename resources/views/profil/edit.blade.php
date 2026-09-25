@extends(layout_espace())

@section('titre', 'Mon profil')

@php
    $carte = 'bg-surface-container-lowest rounded-2xl p-space-md sm:p-space-lg shadow-sm';
    $bouton = 'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm transition-colors';
    $espacePro = $user->estPharmacie() || $user->estAdmin();
    $role = match (true) {
        $user->estPharmacie() => ['local_pharmacy', 'Compte officine'],
        $user->estLivreur() => ['two_wheeler', 'Compte coursier'],
        $user->estAdmin() => ['admin_panel_settings', 'Administrateur'],
        default => ['person', 'Compte patient'],
    };
@endphp

@section('contenu')
<div class="w-full {{ $espacePro ? 'py-space-lg' : 'max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-lg' }} flex flex-col gap-space-lg">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md {{ $espacePro ? 'bg-surface-container-lowest p-space-lg rounded-xl shadow-sm' : '' }}">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-primary-container text-on-primary-container flex items-center justify-center font-headline-sm text-headline-sm">{{ initiales($user->name) }}</div>
            <div>
                <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-surface tracking-tight">Mon profil</h1>
                <p class="font-body-md text-body-md text-on-surface-variant flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px] text-primary">{{ $role[0] }}</span>{{ $role[1] }} · {{ $user->email }}</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm font-semibold self-start md:self-auto {{ $user->statut === 'actif' ? 'bg-[#dcfce9] text-[#14532d]' : 'bg-tertiary-fixed text-on-tertiary-fixed' }}">
            <span class="w-2 h-2 rounded-full {{ $user->statut === 'actif' ? 'bg-primary' : 'bg-tertiary' }}"></span>
            {{ ['actif' => 'Compte actif', 'en_attente' => 'En attente de validation', 'suspendu' => 'Compte suspendu'][$user->statut] ?? $user->statut }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-start">
        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-7 {{ $carte }} flex flex-col gap-space-md">
            @csrf
            @method('PUT')
            <div class="flex items-center gap-2 pb-space-sm border-b border-surface-container-low">
                <span class="material-symbols-outlined text-primary text-[22px]">badge</span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Informations personnelles</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                <x-champ label="Nom complet" name="name" icone="person" :value="$user->name" required/>
                <x-champ label="Téléphone" name="telephone" type="tel" icone="call" :value="$user->telephone" placeholder="690123456"/>
            </div>

            @if ($user->estClient() && $user->client)
                <div class="flex items-center gap-2 pt-space-sm pb-space-sm border-b border-surface-container-low">
                    <span class="material-symbols-outlined text-primary text-[22px]">home_pin</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Adresse de livraison par défaut</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <x-champ label="Adresse & repères" name="adresse" icone="pin_drop" :value="$user->client->adresse" placeholder="Rue des Palmiers, portail blanc"/>
                    <x-champ label="Quartier" name="quartier" icone="location_city" :value="$user->client->quartier" placeholder="Bonapriso"/>
                </div>
                <input type="hidden" name="ville" value="{{ $user->client->ville ?? 'Douala' }}">
            @endif

            @if ($user->estPharmacie() && $user->pharmacie)
                <div class="flex items-center gap-2 pt-space-sm pb-space-sm border-b border-surface-container-low">
                    <span class="material-symbols-outlined text-primary text-[22px]">local_pharmacy</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Mon officine — {{ $user->pharmacie->nom }}</h2>
                </div>
                <x-champ label="Présentation de l'officine" name="description" type="textarea" :value="$user->pharmacie->description"/>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <x-champ label="Frais de livraison (FCFA)" name="frais_livraison" type="number" min="0" icone="local_shipping" :value="$user->pharmacie->frais_livraison"/>
                    <x-champ label="Photo de la vitrine" name="photo" type="file" accept="image/*" class="file:mr-3 file:px-3 file:py-1 file:rounded-lg file:border-0 file:bg-[#dcfce9] file:text-[#14532d] file:font-semibold"/>
                </div>
            @endif

            @if ($user->estLivreur() && $user->livreur)
                <div class="flex items-center gap-2 pt-space-sm pb-space-sm border-b border-surface-container-low">
                    <span class="material-symbols-outlined text-primary text-[22px]">two_wheeler</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Mon véhicule</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <x-champ label="Véhicule" name="vehicule" type="select" icone="two_wheeler">
                        @foreach (['moto' => 'Moto', 'voiture' => 'Voiture', 'velo' => 'Vélo'] as $valeur => $libelle)
                            <option value="{{ $valeur }}" @selected(old('vehicule', $user->livreur->vehicule) === $valeur)>{{ $libelle }}</option>
                        @endforeach
                    </x-champ>
                    <x-champ label="Immatriculation" name="immatriculation" icone="badge" :value="$user->livreur->immatriculation"/>
                </div>
            @endif

            <div class="pt-space-sm">
                <button type="submit" class="{{ $bouton }}"><span class="material-symbols-outlined text-[18px]">save</span>Enregistrer</button>
            </div>
        </form>

        <div class="lg:col-span-5 flex flex-col gap-space-lg">
            <form action="{{ route('password.update') }}" method="POST" class="{{ $carte }} flex flex-col gap-space-md">
                @csrf
                @method('PUT')
                <div class="flex items-center gap-2 pb-space-sm border-b border-surface-container-low">
                    <span class="material-symbols-outlined text-primary text-[22px]">lock</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Mot de passe</h2>
                </div>
                <x-champ label="Mot de passe actuel" name="current_password" type="password" icone="lock" sac="updatePassword" required autocomplete="current-password"/>
                <x-champ label="Nouveau mot de passe" name="password" type="password" icone="lock_reset" sac="updatePassword" required autocomplete="new-password"/>
                <x-champ label="Confirmer" name="password_confirmation" type="password" icone="lock_reset" sac="updatePassword" required autocomplete="new-password"/>
                <div>
                    <button type="submit" class="{{ $bouton }}"><span class="material-symbols-outlined text-[18px]">key</span>Mettre à jour</button>
                </div>
            </form>
            <div class="{{ $carte }} flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#dcfce9] text-[#14532d] flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[22px]">health_and_safety</span></div>
                <div>
                    <h3 class="font-label-lg text-label-lg text-on-surface">Données médicales protégées</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Vos informations ne sont partagées qu'avec l'officine et le coursier en charge de vos commandes.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

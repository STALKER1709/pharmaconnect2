@extends('layouts.guest')

@section('titre', 'Connexion')

@section('contenu')
    @include('auth.partials.entete', ['icone' => 'login', 'titre' => 'Connexion', 'sousTitre' => 'Bon retour parmi nous ! Accédez à votre espace PharmaConnect.'])

    @if (session('statut') === 'en_attente')
        <div class="mb-space-md p-3 rounded-xl bg-tertiary-fixed text-on-tertiary-fixed font-body-sm text-body-sm flex items-start gap-2"><span class="material-symbols-outlined text-[18px]">hourglass_top</span>Votre compte (pharmacie ou livreur) est en attente de validation par l'administrateur.</div>
    @elseif (session('statut') === 'inscription_en_attente')
        <div class="mb-space-md p-3 rounded-xl bg-[#dcfce9] text-[#14532d] font-body-sm text-body-sm flex items-start gap-2"><span class="material-symbols-outlined text-[18px]">check_circle</span>Inscription enregistrée ! Votre compte sera activé après validation par l'administrateur. Vous serez notifié par e-mail.</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-space-md">
        @csrf
        <x-champ label="Adresse e-mail" name="email" type="email" icone="mail" required autofocus autocomplete="username" placeholder="vous@exemple.cm"/>
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="font-label-md text-label-md text-on-surface">Mot de passe</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="font-label-sm text-label-sm text-primary hover:underline">Mot de passe oublié ?</a>
                @endif
            </div>
            <x-champ name="password" type="password" icone="lock" required autocomplete="current-password"/>
        </div>
        <label class="flex items-center gap-2 font-body-md text-body-md text-on-surface cursor-pointer">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-primary accent-[#16a34a] focus:ring-0">
            Se souvenir de moi
        </label>
        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm transition-colors">
            <span class="material-symbols-outlined text-[20px]">lock_open</span>
            Se connecter
        </button>
    </form>

    <p class="mt-space-lg font-body-md text-body-md text-on-surface-variant text-center">
        Pas encore de compte ? <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Créer un compte</a>
    </p>

    <div class="mt-space-md p-3.5 rounded-xl bg-surface-container-low font-body-sm text-body-sm text-on-surface-variant">
        <p class="font-label-md text-label-md text-on-surface mb-1 flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-primary">info</span>Comptes de démonstration (mot de passe : <code>password</code>)</p>
        <p>client@pharmaconnect.cm · fondateur@pharmaconnect.cm · livreur@pharmaconnect.cm · admin@pharmaconnect.cm</p>
    </div>
@endsection

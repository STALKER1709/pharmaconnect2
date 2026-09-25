@extends('layouts.guest')

@section('titre', 'Mot de passe oublié')

@section('contenu')
    @include('auth.partials.entete', ['icone' => 'key', 'titre' => 'Mot de passe oublié ?', 'sousTitre' => 'Indiquez votre e-mail : nous vous envoyons un lien de réinitialisation.'])

    @if (session('status'))
        <div class="mb-space-md p-3 rounded-xl bg-[#dcfce9] text-[#14532d] font-body-sm text-body-sm flex items-start gap-2"><span class="material-symbols-outlined text-[18px]">mark_email_read</span>{{ __(session('status')) }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-space-md">
        @csrf
        <x-champ label="Adresse e-mail" name="email" type="email" icone="mail" required autofocus/>
        <p class="font-body-sm text-body-sm text-on-surface-variant">En local (MAIL_MAILER=log), le lien est écrit dans <code>storage/logs/laravel.log</code>.</p>
        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm transition-colors"><span class="material-symbols-outlined text-[20px]">send</span>Envoyer le lien</button>
    </form>
    <p class="mt-space-lg text-center"><a href="{{ route('login') }}" class="inline-flex items-center gap-1 font-label-lg text-label-lg text-primary hover:underline"><span class="material-symbols-outlined text-[18px]">arrow_back</span>Retour à la connexion</a></p>
@endsection

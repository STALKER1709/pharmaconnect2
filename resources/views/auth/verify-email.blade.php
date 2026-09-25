@extends('layouts.guest')

@section('titre', 'Vérifiez votre e-mail')

@section('contenu')
    @include('auth.partials.entete', ['icone' => 'mark_email_unread', 'titre' => 'Vérifiez votre e-mail', 'sousTitre' => 'Un lien de vérification vous a été envoyé.'])

    <p class="font-body-md text-body-md text-on-surface-variant">En local (MAIL_MAILER=log), le lien est écrit dans <code>storage/logs/laravel.log</code>.</p>
    @if (session('status') === 'verification-link-sent' || session('statut') === 'verification-link-sent')
        <div class="mt-space-md p-3 rounded-xl bg-[#dcfce9] text-[#14532d] font-body-sm text-body-sm flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">check_circle</span>Nouveau lien envoyé !</div>
    @endif
    <form method="POST" action="{{ route('verification.send') }}" class="mt-space-lg">
        @csrf
        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm transition-colors"><span class="material-symbols-outlined text-[20px]">send</span>Renvoyer le lien</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="mt-space-md text-center">
        @csrf
        <button type="submit" class="font-label-md text-label-md text-on-surface-variant hover:text-tertiary">Se déconnecter</button>
    </form>
@endsection

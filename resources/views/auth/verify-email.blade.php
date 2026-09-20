@extends('layouts.guest')

@section('titre', 'Vérifiez votre e-mail')

@section('contenu')
<div class="carte auth-carte" style="text-align:center;">
    <div style="font-size:36px;">📧</div>
    <h1 class="titre-page mt-2" style="font-size:24px;">Vérifiez votre e-mail</h1>
    <p class="sous-titre">
        Un lien de vérification vous a été envoyé. En local (MAIL_MAILER=log), il est écrit dans
        <code>storage/logs/laravel.log</code>.
    </p>

    @if (session('statut') === 'verification-link-sent')
        <div class="alerte alerte-info mt-4">Nouveau lien envoyé !</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
        @csrf
        <button class="btn btn-secondaire" style="width:100%;">Renvoyer le lien</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button class="texte-petit texte-doux">Se déconnecter</button>
    </form>
</div>
@endsection

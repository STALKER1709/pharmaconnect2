@extends('layouts.guest')

@section('titre', 'Vérifiez votre e-mail')

@section('contenu')
<div class="card p-8 text-center">
    <div class="text-4xl">📧</div>
    <h1 class="mt-3 text-2xl font-black">Vérifiez votre e-mail</h1>
    <p class="mt-2 text-sm text-slate-500">
        Un lien de vérification vous a été envoyé. En local (MAIL_MAILER=log), il est écrit dans
        <code>storage/logs/laravel.log</code>.
    </p>

    @if (session('statut') === 'verification-link-sent')
        <div class="mt-4 rounded-xl bg-menthe-50 p-3 text-sm text-menthe-800">Nouveau lien envoyé !</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
        @csrf
        <button class="btn-secondary w-full">Renvoyer le lien</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button class="text-sm text-slate-500 hover:text-red-600">Se déconnecter</button>
    </form>
</div>
@endsection

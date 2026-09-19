@extends('layouts.app')

@section('titre', 'Tableau de bord')

@section('contenu')
<div class="card p-10 text-center">
    <div class="text-5xl">💊</div>
    <h1 class="mt-4 text-2xl font-black">Bienvenue sur PharmaConnect</h1>
    <p class="mt-2 text-sm text-slate-500">Redirection vers votre espace…</p>
    <a href="{{ route('accueil') }}" class="btn-primary mt-4">Aller à l'accueil</a>
</div>
@endsection

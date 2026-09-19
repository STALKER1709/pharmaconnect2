@extends('layouts.app')

@section('titre', 'Mon espace')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">Bonjour, {{ auth()->user()->name }} 👋</h1>

    <dl class="grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Commandes</dt>
            <dd class="text-3xl font-black text-menthe-700">{{ $nbCommandes }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Pharmacies vedettes</dt>
            <dd class="mt-1 space-y-1 text-sm">
                @forelse($pharmaciesProches as $p)
                    <a href="{{ route('public.pharmacie', $p) }}" class="block hover:text-menthe-700">🏥 {{ $p->nom }}</a>
                @empty
                    <span class="text-slate-400">—</span>
                @endforelse
            </dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Actions rapides</dt>
            <dd class="mt-2 space-y-2">
                <a href="{{ route('public.medicaments') }}" class="btn-primary w-full text-xs">🔍 Rechercher un médicament</a>
                <a href="{{ route('messagerie.index') }}" class="btn-secondary w-full text-xs">💬 Messagerie</a>
                <a href="{{ route('chatbot.index') }}" class="btn-secondary w-full text-xs">🤖 Assistant santé</a>
            </dd>
        </div>
    </dl>

    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold">Dernières commandes</h2>
            <a href="{{ route('commandes.index') }}" class="text-sm font-semibold text-menthe-700 hover:underline">Tout voir →</a>
        </div>

        @forelse($commandes as $commande)
            <a href="{{ route('commandes.show', $commande) }}" class="card flex flex-wrap items-center justify-between gap-3 p-4 transition hover:shadow-md">
                <div>
                    <div class="font-bold">{{ $commande->numero }} — {{ $commande->pharmacie?->nom }}</div>
                    <div class="text-xs text-slate-500">{{ $commande->lignes->count() }} article(s) · {{ $commande->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-black">{{ $commande->totalFormatte() }}</span>
                    <span class="badge {{ $commande->statut->couleur() }}">{{ $commande->statut->label() }}</span>
                </div>
            </a>
        @empty
            <p class="card p-6 text-sm text-slate-500">Aucune commande. <a href="{{ route('public.medicaments') }}" class="font-semibold text-menthe-700 hover:underline">Trouvez votre médicament →</a></p>
        @endforelse
    </section>
</div>
@endsection

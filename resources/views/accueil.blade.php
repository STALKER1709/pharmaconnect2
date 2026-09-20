@extends('layouts.app')

@section('titre', 'Accueil')

@section('contenu')
<div class="space-y-16">

    {{-- Héro --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-menthe-700 via-menthe-600 to-emerald-500 px-6 py-16 text-white sm:px-12">
        <div class="absolute -right-16 -top-16 text-[220px] opacity-10 select-none">💊</div>
        <div class="relative max-w-2xl space-y-6">
            <span class="badge bg-white/15 text-white">🇨🇲 Douala · Cameroun</span>
            <h1 class="text-4xl font-black leading-tight sm:text-5xl">
                Vos médicaments livrés chez vous, payés par Mobile Money.
            </h1>
            <p class="text-lg text-menthe-50">
                Comparez les stocks des pharmacies de Douala, commandez en 2 minutes,
                payez par MTN MoMo ou Orange Money et suivez votre livreur en temps réel.
            </p>

            <form action="{{ route('accueil') }}" method="GET" class="flex max-w-xl gap-2 rounded-2xl bg-white p-2 shadow-lg">
                <input type="search" name="q" value="{{ $recherche }}"
                       placeholder="Paracétamol, pharmacie, quartier…"
                       class="w-full rounded-xl border-0 px-4 py-2.5 text-slate-800 placeholder-slate-400 focus:ring-0">
                <button class="btn-primary shrink-0">Rechercher 🔍</button>
            </form>

            <div class="flex flex-wrap gap-6 pt-2 text-sm">
                <div><span class="block text-2xl font-black">{{ $nbPharmacies }}</span> pharmacies partenaires</div>
                <div><span class="block text-2xl font-black">{{ $nbMedicaments }}</span> références</div>
                <div><span class="block text-2xl font-black">{{ $nbCommandes }}</span> commandes</div>
            </div>
        </div>
    </section>

    {{-- Résultats de recherche --}}
    @if($recherche !== '')
    <section class="space-y-4">
        <h2 class="text-xl font-bold">Résultats pour « {{ $recherche }} »</h2>
        @if($medicaments->isEmpty() && $pharmacies->isEmpty())
            <p class="card p-6 text-sm text-slate-500">Aucun résultat. Essayez un autre mot-clé.</p>
        @endif
        @if($medicaments->isNotEmpty())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($medicaments as $medicament)
                <a href="{{ route('public.medicament', $medicament) }}" class="card p-4 transition hover:shadow-md">
                    <div class="text-xs font-semibold uppercase tracking-wide text-menthe-600">{{ $medicament->categorie?->nom ?? 'Médicament' }}</div>
                    <div class="mt-1 font-bold">{{ $medicament->nom }}</div>
                    <div class="mt-1 text-xs text-slate-500">{{ $medicament->forme }} · {{ $medicament->fabricant }}</div>
                </a>
            @endforeach
        </div>
        @endif
        @if($pharmacies->isNotEmpty())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($pharmacies as $pharmacie)
                <a href="{{ route('public.pharmacie', $pharmacie) }}" class="card p-4 transition hover:shadow-md">
                    <div class="font-bold">🏥 {{ $pharmacie->nom }}</div>
                    <div class="text-xs text-slate-500">{{ $pharmacie->quartier }}, {{ $pharmacie->ville }}</div>
                    <div class="mt-1 text-xs">⭐ {{ number_format($pharmacie->note_moyenne, 1) }} · {{ $pharmacie->statutOuverture() }}</div>
                </a>
            @endforeach
        </div>
        @endif
    </section>
    @endif

    {{-- Comment ça marche --}}
    <section class="space-y-8">
        <h2 class="text-center text-2xl font-black">Comment ça marche ?</h2>
        <div class="grid gap-6 md:grid-cols-4">
            @foreach([
                ['🔍', '1. Cherchez', 'Trouvez votre médicament ou la pharmacie la plus proche.'],
                ['🛒', '2. Commandez', 'Ajoutez au panier, partagez votre adresse (GPS ou carte).'],
                ['📱', '3. Payez', 'MTN MoMo ou Orange Money — paiement inclus à la commande.'],
                ['🛵', '4. Suivez', 'Position du livreur en temps réel, confirmation de réception.'],
            ] as $etape)
                <div class="card p-6 text-center">
                    <div class="text-4xl">{{ $etape[0] }}</div>
                    <h3 class="mt-3 font-bold">{{ $etape[1] }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $etape[2] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Pharmacies vedettes --}}
    @if($pharmacies->isNotEmpty())
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-black">Pharmacies vedettes</h2>
            <a href="{{ route('public.pharmacies') }}" class="text-sm font-semibold text-menthe-700 hover:underline">Tout voir →</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($pharmacies as $pharmacie)
                <a href="{{ route('public.pharmacie', $pharmacie) }}" class="card group p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold group-hover:text-menthe-700">{{ $pharmacie->nom }}</h3>
                            <p class="text-sm text-slate-500">{{ $pharmacie->quartier }}, {{ $pharmacie->ville }}</p>
                        </div>
                        <span class="badge {{ $pharmacie->estOuverte() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $pharmacie->statutOuverture() }}
                        </span>
                    </div>
                    <div class="mt-3 flex items-center gap-3 text-sm">
                        <span>⭐ {{ number_format($pharmacie->note_moyenne, 1) }}</span>
                        <span class="text-slate-400">·</span>
                        <span>🛵 {{ \App\Support\Fcfa::montant($pharmacie->frais_livraison) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Médicaments --}}
    @if($medicaments->isNotEmpty() && $recherche === '')
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-black">Médicaments recherchés</h2>
            <a href="{{ route('public.medicaments') }}" class="text-sm font-semibold text-menthe-700 hover:underline">Tout voir →</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($medicaments->take(8) as $medicament)
                <a href="{{ route('public.medicament', $medicament) }}" class="card p-4 transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="text-xs font-semibold uppercase tracking-wide text-menthe-600">{{ $medicament->categorie?->nom }}</div>
                    <h3 class="mt-1 font-bold">{{ $medicament->nom }}</h3>
                    <div class="mt-1 text-xs text-slate-500">{{ $medicament->forme }} @if($medicament->ordonnance_obligatoire) · <span class="text-amber-600">ordonnance</span>@endif</div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- CTA par rôle --}}
    <section class="grid gap-6 md:grid-cols-3">
        <div class="card p-8">
            <h3 class="text-lg font-black">👤 Je suis client</h3>
            <p class="mt-2 text-sm text-slate-500">Créez votre compte gratuit et faites-vous livrer en quelques minutes.</p>
            <a href="{{ route('register') }}" class="btn-primary mt-4 w-full">Créer mon compte</a>
        </div>
        <div class="card p-8">
            <h3 class="text-lg font-black">🏥 Je suis une pharmacie</h3>
            <p class="mt-2 text-sm text-slate-500">Vendez en ligne, gérez vos stocks et recevez vos paiements Mobile Money.</p>
            <a href="{{ route('register') }}" class="btn-secondary mt-4 w-full">Inscrire ma pharmacie</a>
        </div>
        <div class="card p-8">
            <h3 class="text-lg font-black">🛵 Je suis livreur</h3>
            <p class="mt-2 text-sm text-slate-500">Rejoignez le réseau et gagnez des revenus à chaque livraison.</p>
            <a href="{{ route('register') }}" class="btn-secondary mt-4 w-full">Devenir livreur</a>
        </div>
    </section>
</div>
@endsection

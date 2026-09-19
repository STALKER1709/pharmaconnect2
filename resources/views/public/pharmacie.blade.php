@extends('layouts.app')

@section('titre', $pharmacie->nom)

@section('contenu')
<div class="space-y-8">
    <div class="card p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black">🏥 {{ $pharmacie->nom }}</h1>
                <p class="text-sm text-slate-500">{{ $pharmacie->adresse }}, {{ $pharmacie->quartier }}, {{ $pharmacie->ville }}</p>
                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm">
                    <span class="badge {{ $pharmacie->estOuverte() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $pharmacie->statutOuverture() }}</span>
                    <span>⭐ {{ $noteMoyenne }} / 5 ({{ $pharmacie->nb_avis }} avis)</span>
                    <span>🛵 Frais de livraison : {{ \App\Support\Fcfa::montant($pharmacie->frais_livraison) }}</span>
                    <span>📞 {{ $pharmacie->user->telephone }}</span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="tel:{{ $pharmacie->user->telephone }}" class="btn-secondary">📞 Appeler</a>
                @auth
                    <a href="{{ route('messagerie.demarrer', $pharmacie->user) }}" class="btn-primary">💬 Message</a>
                @endauth
            </div>
        </div>
        @if($pharmacie->description)
            <p class="mt-4 text-sm text-slate-600">{{ $pharmacie->description }}</p>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Horaires --}}
        <div class="card p-5">
            <h2 class="mb-3 font-bold">Horaires d'ouverture</h2>
            <ul class="space-y-1 text-sm">
                @foreach([1,2,3,4,5,6,0] as $jour)
                    @php $h = $pharmacie->horaireDuJour($jour); @endphp
                    <li class="flex justify-between {{ $jour === now()->dayOfWeek ? 'font-bold text-menthe-800' : 'text-slate-600' }}">
                        <span>{{ $jours[$jour] }}</span>
                        <span>{{ $h && $h->ouvert ? substr($h->heure_ouverture,0,5).' — '.substr($h->heure_fermeture,0,5) : 'Fermé' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Carte --}}
        <div class="card overflow-hidden lg:col-span-2">
            <div id="carte-pharmacie" class="h-64 w-full"></div>
        </div>
    </div>

    {{-- Catalogue --}}
    <section class="space-y-4">
        <h2 class="text-xl font-bold">Médicaments en stock ({{ $stocks->total() }})</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($stocks as $stock)
                <div class="card p-4">
                    <div class="text-xs uppercase tracking-wide text-menthe-600">{{ $stock->medicament->categorie?->nom ?? 'Médicament' }}</div>
                    <a href="{{ route('public.medicament', $stock->medicament) }}" class="mt-1 block font-bold hover:text-menthe-700">{{ $stock->medicament->nom }}</a>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="font-black">{{ \App\Support\Fcfa::montant($stock->prix) }}</span>
                        <span class="text-xs text-slate-400">{{ $stock->quantite > 0 ? $stock->quantite.' en stock' : 'Rupture' }}</span>
                    </div>
                    @auth
                    @if(auth()->user()->estClient() && $stock->estEnStock())
                        <form action="{{ route('panier.ajouter', $stock) }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="quantite" value="1">
                            <button class="btn-primary w-full text-xs">Ajouter au panier</button>
                        </form>
                    @endif
                    @endauth
                </div>
            @empty
                <p class="card col-span-full p-6 text-sm text-slate-500">Aucun médicament en stock actuellement.</p>
            @endforelse
        </div>
        {{ $stocks->links() }}
    </section>

    {{-- Avis --}}
    @if($pharmacie->avis->isNotEmpty())
    <section class="space-y-3">
        <h2 class="text-xl font-bold">Derniers avis</h2>
        @foreach($pharmacie->avis as $avis)
            <div class="card p-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-semibold">{{ $avis->client?->user?->name ?? 'Client' }}</span>
                    <span class="text-amber-500">{{ $avis->etoiles() }}</span>
                </div>
                @if($avis->commentaire) <p class="mt-1 text-sm text-slate-600">{{ $avis->commentaire }}</p> @endif
            </div>
        @endforeach
    </section>
    @endif
</div>

@push('scripts')
<script type="module">
    const carte = PharmaConnect.carte('carte-pharmacie');
    PharmaConnect.pin(carte, {{ $pharmacie->latitude ?? 4.0511 }}, {{ $pharmacie->longitude ?? 9.7679 }}, '{{ $pharmacie->nom }}')
        .openPopup();
    carte.setView([{{ $pharmacie->latitude ?? 4.0511 }}, {{ $pharmacie->longitude ?? 9.7679 }}], 15);
</script>
@endpush
@endsection

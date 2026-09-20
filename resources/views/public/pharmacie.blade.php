@extends('layouts.app')

@section('titre', $pharmacie->nom)

@section('contenu')
<div class="mb-6">
    <div class="carte carte-corps">
        <div class="rangee-entre">
            <div>
                <h1 class="titre-page">🏥 {{ $pharmacie->nom }}</h1>
                <p class="sous-titre">{{ $pharmacie->adresse }}, {{ $pharmacie->quartier }}, {{ $pharmacie->ville }}</p>
                <div class="rangée mt-4">
                    <span class="badge {{ $pharmacie->estOuverte() ? 'badge-vert' : 'badge-gris' }}">{{ $pharmacie->statutOuverture() }}</span>
                    <span class="texte-petit">⭐ {{ $noteMoyenne }} / 5 ({{ $pharmacie->nb_avis }} avis)</span>
                    <span class="texte-petit">🛵 Frais de livraison : {{ \App\Support\Fcfa::montant($pharmacie->frais_livraison) }}</span>
                    <span class="texte-petit">📞 {{ $pharmacie->user->telephone }}</span>
                </div>
            </div>
            <div class="rangée">
                <a href="tel:{{ $pharmacie->user->telephone }}" class="btn btn-secondaire">📞 Appeler</a>
                @auth
                    <a href="{{ route('messagerie.demarrer', $pharmacie->user) }}" class="btn btn-primaire">💬 Message</a>
                @endauth
            </div>
        </div>
        @if($pharmacie->description)
            <p class="mt-4">{{ $pharmacie->description }}</p>
        @endif
    </div>
</div>

<div class="grille grille-contenu-aside">
    <div class="carte carte-corps">
        <h2 class="carte-titre mb-4">Horaires d'ouverture</h2>
        <ul style="list-style:none; margin:0; padding:0; display:grid; gap:6px; font-size:14px;">
            @foreach([1,2,3,4,5,6,0] as $jour)
                @php $h = $pharmacie->horaireDuJour($jour); @endphp
                <li class="rangee-entre {{ $jour === now()->dayOfWeek ? '' : 'texte-doux' }}" style="@if($jour === now()->dayOfWeek) font-weight:700; color:var(--vert-800); @endif">
                    <span>{{ $jours[$jour] }}</span>
                    <span>{{ $h && $h->ouvert ? substr($h->heure_ouverture,0,5).' — '.substr($h->heure_fermeture,0,5) : 'Fermé' }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="carte" style="overflow:hidden;">
        <div id="carte-pharmacie" class="carte-carte" style="height:256px;"></div>
    </div>
</div>

{{-- Catalogue --}}
<section class="mt-6">
    <h2 class="titre-section mb-4">Médicaments en stock ({{ $stocks->total() }})</h2>
    <div class="grille grille-4">
        @forelse($stocks as $stock)
            <div class="carte carte-corps medic-carte">
                <div>
                    <div class="medic-visuel">
                        <span class="medic-badge-haut badge badge-gris">{{ $stock->medicament->categorie?->nom ?? 'Médicament' }}</span>
                        💊
                    </div>
                    <a href="{{ route('public.medicament', $stock->medicament) }}" class="medic-nom">{{ $stock->medicament->nom }}</a>
                    <div class="medic-pied">
                        <span class="prix">{{ \App\Support\Fcfa::montant($stock->prix) }}</span>
                        <span class="texte-petit {{ $stock->quantite > 0 ? '' : 'texte-doux' }}">{{ $stock->quantite > 0 ? $stock->quantite.' en stock' : 'Rupture' }}</span>
                    </div>
                </div>
                @auth
                @if(auth()->user()->estClient() && $stock->estEnStock())
                    <form action="{{ route('panier.ajouter', $stock) }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="quantite" value="1">
                        <button class="btn btn-primaire btn-petit" style="width:100%;">Ajouter au panier</button>
                    </form>
                @endif
                @endauth
            </div>
        @empty
            <div class="carte vide" style="grid-column:1/-1;"><div class="vide-icone">📦</div>Aucun médicament en stock actuellement.</div>
        @endforelse
    </div>
    {{ $stocks->links() }}
</section>

{{-- Avis --}}
@if($pharmacie->avis->isNotEmpty())
<section class="mt-6">
    <h2 class="titre-section mb-4">Derniers avis</h2>
    <div style="display:grid; gap:12px;">
        @foreach($pharmacie->avis as $avis)
            <div class="carte carte-corps">
                <div class="rangee-entre texte-petit">
                    <span style="font-weight:600;">{{ $avis->client?->user?->name ?? 'Client' }}</span>
                    <span style="color:#f59e0b;">{{ $avis->etoiles() }}</span>
                </div>
                @if($avis->commentaire) <p class="mt-2">{{ $avis->commentaire }}</p> @endif
            </div>
        @endforeach
    </div>
</section>
@endif
@endsection

@push('scripts')
<script type="module">
    const carte = PharmaConnect.carte('carte-pharmacie');
    PharmaConnect.pin(carte, {{ $pharmacie->latitude ?? 4.0511 }}, {{ $pharmacie->longitude ?? 9.7679 }}, '{{ $pharmacie->nom }}')
        .openPopup();
    carte.setView([{{ $pharmacie->latitude ?? 4.0511 }}, {{ $pharmacie->longitude ?? 9.7679 }}], 15);
</script>
@endpush

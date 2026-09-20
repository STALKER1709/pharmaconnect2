@extends('layouts.app')

@section('titre', $medicament->nom)

@section('contenu')
<div class="grille grille-contenu-aside">
    <div style="display:grid; gap:24px;">
        <div class="carte carte-corps">
            <div class="soustitre-bloc">💊 {{ $medicament->categorie?->nom ?? 'Médicament' }}</div>
            <h1 class="titre-page mt-2">{{ $medicament->nom }}</h1>
            <div class="rangée mt-4">
                @if($medicament->ordonnance_obligatoire)
                    <span class="badge badge-ambre">Ordonnance obligatoire</span>
                @else
                    <span class="badge badge-vert">Sans ordonnance</span>
                @endif
                @if($medicament->forme) <span class="badge badge-gris">{{ $medicament->forme }}</span> @endif
                @if($medicament->fabricant) <span class="badge badge-gris">{{ $medicament->fabricant }}</span> @endif
                @if($noteMoyenne > 0) <span class="badge badge-ambre">⭐ {{ $noteMoyenne }}/5</span> @endif
            </div>

            @if($medicament->description)
                <p class="mt-4">{{ $medicament->description }}</p>
            @endif
            @if($medicament->posologie)
                <div class="alerte alerte-info mt-4">
                    <span style="font-weight:600;">Posologie :</span> {{ $medicament->posologie }}
                </div>
            @endif
        </div>

        {{-- Pharmacies qui l'ont en stock --}}
        <section>
            <h2 class="titre-section mb-4">Disponible dans {{ $stocks->count() }} pharmacie(s) — trié par prix</h2>
            <div style="display:grid; gap:12px;">
                @forelse($stocks as $pharmacie)
                    <div class="carte carte-corps" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
                        <div>
                            <a href="{{ route('public.pharmacie', $pharmacie) }}" class="pharmacie-nom" style="color:var(--vert-700);">🏥 {{ $pharmacie->nom }}</a>
                            <p class="texte-petit texte-doux">{{ $pharmacie->quartier }} · ⭐ {{ number_format($pharmacie->note_moyenne, 1) }}</p>
                            <p class="texte-petit texte-doux">Stock : {{ $pharmacie->pivot->quantite }} · Péremption : {{ $pharmacie->pivot->date_peremption ? \Illuminate\Support\Carbon::parse($pharmacie->pivot->date_peremption)->format('m/Y') : '—' }}</p>
                        </div>
                        <div class="texte-droit">
                            <div class="prix">{{ \App\Support\Fcfa::montant($pharmacie->pivot->prix) }}</div>
                            @auth
                                @if(auth()->user()->estClient())
                                    <form action="{{ route('panier.ajouter', ['stock' => $pharmacie->pivot->pharmacie_id ?? $pharmacie->id]) }}" method="POST" class="mt-2">
                                        @csrf
                                        <input type="hidden" name="quantite" value="1">
                                        <button class="btn btn-primaire btn-petit">Ajouter au panier</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="carte vide"><div class="vide-icone">📦</div>Aucune pharmacie ne propose ce médicament actuellement.</div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Conseils santé --}}
    <aside>
        <div class="carte carte-corps" style="background:var(--vert-50);">
            <h3 class="carte-titre">🤖 Un doute sur ce médicament ?</h3>
            <p class="mt-2 texte-petit">Demandez conseil à notre assistant santé.</p>
            @auth
                <a href="{{ route('chatbot.index') }}" class="btn btn-secondaire mt-4" style="width:100%;">Demander au chatbot</a>
            @endauth
        </div>
    </aside>
</div>
@endsection

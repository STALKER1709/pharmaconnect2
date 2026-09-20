@extends('layouts.app')

@section('titre', 'Commander')

@section('contenu')
<h1 class="titre-page">Finaliser la commande</h1>
<p class="sous-titre">Pharmacie : <span style="font-weight:600; color:var(--vert-700);">{{ $pharmacie->nom }}</span> — paiement Mobile Money inclus.</p>

<div class="grille grille-contenu-aside mt-6">
    <form action="{{ route('commande.store') }}" method="POST" class="carte carte-corps"
          style="display:grid; gap:20px;"
          x-data="{
              lat: {{ $client?->latitude ?? 'null' }},
              lng: {{ $client?->longitude ?? 'null' }},
              geolocaliser() {
                  PharmaConnect.geolocaliser(
                      (lat, lng) => { this.lat = lat; this.lng = lng;
                          this.$refs.lat.value = lat; this.$refs.lng.value = lng; },
                      (err) => alert('Géolocalisation impossible : ' + err)
                  );
              }
          }">
        @csrf

        {{-- Adresse de livraison --}}
        <section>
            <h2 class="carte-titre" style="font-size:16px;">1. Adresse de livraison</h2>
            <div class="mt-4" style="display:grid; gap:12px;">
                <div>
                    <label class="champ-label">Adresse complète *</label>
                    <input name="adresse_livraison" value="{{ old('adresse_livraison', $client?->adresse) }}" required class="champ" placeholder="Rue, immeuble, point de repère">
                    @error('adresse_livraison') <p class="erreur-texte">{{ $message }}</p> @enderror
                </div>
                <div class="role-grille-2">
                    <div>
                        <label class="champ-label">Ville</label>
                        <input name="ville_livraison" value="{{ old('ville_livraison', 'Douala') }}" required class="champ">
                    </div>
                    <div class="rangée" style="align-items:flex-end;">
                        <input ref="lat" type="hidden" name="latitude" value="{{ old('latitude', $client?->latitude) }}">
                        <input ref="lng" type="hidden" name="longitude" value="{{ old('longitude', $client?->longitude) }}">
                        <button type="button" @click="geolocaliser" class="btn btn-secondaire" style="width:100%;">📍 Partager ma position GPS</button>
                    </div>
                </div>
                <div>
                    <label class="champ-label">Notes pour le livreur (optionnel)</label>
                    <textarea name="notes" rows="2" class="champ" placeholder="Ex. : portail bleu, 3e étage…">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>

        {{-- Paiement --}}
        <section x-data="{ operateur: '{{ old('operateur', 'mtn_momo') }}' }">
            <h2 class="carte-titre" style="font-size:16px;">2. Paiement Mobile Money</h2>

            <div class="role-grille mt-4">
                <label class="operateur-option operateur-momo">
                    <input type="radio" name="operateur" value="mtn_momo" x-model="operateur" @checked(old('operateur', 'mtn_momo') === 'mtn_momo')>
                    <span><em>📱</em><strong>MTN MoMo</strong></span>
                </label>
                <label class="operateur-option operateur-om">
                    <input type="radio" name="operateur" value="orange_money" x-model="operateur" @checked(old('operateur', 'mtn_momo') === 'orange_money')>
                    <span><em>🟠</em><strong>Orange Money</strong></span>
                </label>
            </div>

            <div class="mt-4" x-show="operateur" x-cloak>
                <label class="champ-label">Numéro Mobile Money *</label>
                <input name="numero_mobile_money" value="{{ old('numero_mobile_money') }}" required class="champ" placeholder="Ex. 690123456">
                <p class="texte-petit texte-doux mt-2">Mode simulation locale : aucun débit réel. Formats MTN : 65/67/68 — Orange : 69.</p>
                @error('numero_mobile_money') <p class="erreur-texte">{{ $message }}</p> @enderror
            </div>
        </section>

        <button class="btn btn-primaire" style="width:100%; font-size:16px; padding:14px;">
            Payer {{ \App\Support\Fcfa::montant($sousTotal + $fraisLivraison) }} et commander 🚀
        </button>
    </form>

    {{-- Récapitulatif --}}
    <aside class="carte carte-corps" style="align-self:start;">
        <h2 class="carte-titre">Votre commande</h2>
        @foreach($lignes as $ligne)
            <div class="rangee-entre mt-4 texte-petit">
                <span>{{ $ligne['stock']->medicament->nom }} × {{ $ligne['quantite'] }}</span>
                <span>{{ \App\Support\Fcfa::montant($ligne['sous_total']) }}</span>
            </div>
        @endforeach
        <div class="rangee-entre mt-2 texte-petit"><span>Livraison</span><span>{{ \App\Support\Fcfa::montant($fraisLivraison) }}</span></div>
        <hr class="separateur">
        <div class="rangee-entre"><span style="font-weight:700;">Total</span><span class="prix" style="font-size:22px;">{{ \App\Support\Fcfa::montant($sousTotal + $fraisLivraison) }}</span></div>
    </aside>
</div>
@endsection

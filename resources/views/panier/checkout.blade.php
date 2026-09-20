@extends('layouts.app')

@section('titre', 'Commander')

@section('contenu')
<h1 class="text-2xl font-black">Finaliser la commande</h1>
<p class="mt-1 text-sm text-slate-500">Pharmacie : <span class="font-semibold">{{ $pharmacie->nom }}</span> — paiement Mobile Money inclus.</p>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <form action="{{ route('commande.store') }}" method="POST" class="card space-y-5 p-6 lg:col-span-2"
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
        <section class="space-y-3">
            <h2 class="font-bold">1. Adresse de livraison</h2>
            <div>
                <label class="label">Adresse complète *</label>
                <input name="adresse_livraison" value="{{ old('adresse_livraison', $client?->adresse) }}" required class="input" placeholder="Rue, immeuble, point de repère">
                @error('adresse_livraison') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="label">Ville</label>
                    <input name="ville_livraison" value="{{ old('ville_livraison', 'Douala') }}" required class="input">
                </div>
                <div class="flex items-end gap-2">
                    <input ref="lat" type="hidden" name="latitude" value="{{ old('latitude', $client?->latitude) }}">
                    <input ref="lng" type="hidden" name="longitude" value="{{ old('longitude', $client?->longitude) }}">
                    <button type="button" @click="geolocaliser" class="btn-secondary w-full">📍 Partager ma position GPS</button>
                </div>
            </div>
            <div>
                <label class="label">Notes pour le livreur (optionnel)</label>
                <textarea name="notes" rows="2" class="input" placeholder="Ex. : portail bleu, 3e étage…">{{ old('notes') }}</textarea>
            </div>
        </section>

        {{-- Paiement --}}
        <section class="space-y-3" x-data="{ operateur: '{{ old('operateur', 'mtn_momo') }}' }">
            <h2 class="font-bold">2. Paiement Mobile Money</h2>

            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="operateur" value="mtn_momo" class="peer sr-only" x-model="operateur" @checked(old('operateur', 'mtn_momo') === 'mtn_momo')>
                    <div class="rounded-xl border-2 border-transparent p-4 text-center peer-checked:border-amber-500 peer-checked:bg-amber-50">
                        <div class="text-2xl">📱</div>
                        <div class="mt-1 text-sm font-bold text-amber-700">MTN MoMo</div>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="operateur" value="orange_money" class="peer sr-only" x-model="operateur" @checked(old('operateur', 'mtn_momo') === 'orange_money')>
                    <div class="rounded-xl border-2 border-transparent p-4 text-center peer-checked:border-orange-500 peer-checked:bg-orange-50">
                        <div class="text-2xl">🟠</div>
                        <div class="mt-1 text-sm font-bold text-orange-700">Orange Money</div>
                    </div>
                </label>
            </div>

            <div x-show="operateur" x-cloak>
                <label class="label">Numéro Mobile Money *</label>
                <input name="numero_mobile_money" value="{{ old('numero_mobile_money') }}" required class="input" placeholder="Ex. 690123456">
                <p class="mt-1 text-xs text-slate-400">Mode simulation locale : aucun débit réel. Formats MTN : 65/67/68 — Orange : 69.</p>
                @error('numero_mobile_money') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </section>

        <button class="btn-primary w-full text-base">
            Payer {{ \App\Support\Fcfa::montant($sousTotal + $fraisLivraison) }} et commander 🚀
        </button>
    </form>

    {{-- Récapitulatif --}}
    <aside class="card h-fit space-y-3 p-5">
        <h2 class="font-bold">Votre commande</h2>
        @foreach($lignes as $ligne)
            <div class="flex justify-between text-sm">
                <span>{{ $ligne['stock']->medicament->nom }} × {{ $ligne['quantite'] }}</span>
                <span>{{ \App\Support\Fcfa::montant($ligne['sous_total']) }}</span>
            </div>
        @endforeach
        <div class="flex justify-between text-sm"><span>Livraison</span><span>{{ \App\Support\Fcfa::montant($fraisLivraison) }}</span></div>
        <div class="flex justify-between border-t border-menthe-100 pt-3 text-lg font-black">
            <span>Total</span><span>{{ \App\Support\Fcfa::montant($sousTotal + $fraisLivraison) }}</span>
        </div>
    </aside>
</div>
@endsection

@extends('layouts.app')

@section('titre', 'Mon panier')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">🛒 Mon panier</h1>

    @if($lignes->isEmpty())
        <div class="card p-10 text-center">
            <div class="text-5xl">🛒</div>
            <p class="mt-4 text-slate-500">Votre panier est vide.</p>
            <a href="{{ route('public.medicaments') }}" class="btn-primary mt-4">Rechercher un médicament</a>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-3 lg:col-span-2">
                <div class="card p-4 text-sm text-slate-600">
                    🏥 Pharmacie : <span class="font-semibold">{{ $pharmacie?->nom }}</span>
                    — les commandes sont préparées par une seule pharmacie.
                </div>

                @foreach($lignes as $ligne)
                    <div class="card flex flex-wrap items-center justify-between gap-3 p-4">
                        <div>
                            <a href="{{ route('public.medicament', $ligne['stock']->medicament) }}" class="font-bold hover:text-menthe-700">{{ $ligne['stock']->medicament->nom }}</a>
                            <p class="text-xs text-slate-500">{{ \App\Support\Fcfa::montant($ligne['stock']->prix) }} l'unité</p>
                        </div>

                        <form action="{{ route('panier.modifier') }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="stock_id" value="{{ $ligne['stock']->id }}">
                            <input type="number" name="quantite" value="{{ $ligne['quantite'] }}" min="1" max="{{ $ligne['stock']->quantite }}"
                                   class="input w-20 text-center">
                            <button class="btn-secondary text-xs">Mettre à jour</button>
                        </form>

                        <div class="flex items-center gap-3">
                            <span class="font-black">{{ \App\Support\Fcfa::montant($ligne['sous_total']) }}</span>
                            <form action="{{ route('panier.supprimer') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="stock_id" value="{{ $ligne['stock']->id }}">
                                <button class="btn-danger text-xs">Retirer</button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <form action="{{ route('panier.vider') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn-danger text-sm">Vider le panier</button>
                </form>
            </div>

            <aside class="card h-fit space-y-3 p-5">
                <h2 class="font-bold">Récapitulatif</h2>
                <div class="flex justify-between text-sm">
                    <span>Sous-total</span><span>{{ \App\Support\Fcfa::montant($sousTotal) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Livraison</span><span>{{ \App\Support\Fcfa::montant($fraisLivraison) }}</span>
                </div>
                <div class="flex justify-between border-t border-menthe-100 pt-3 text-lg font-black">
                    <span>Total</span><span>{{ \App\Support\Fcfa::montant($total) }}</span>
                </div>
                <a href="{{ route('commande.create') }}" class="btn-primary w-full">Passer la commande →</a>
            </aside>
        </div>
    @endif
</div>
@endsection

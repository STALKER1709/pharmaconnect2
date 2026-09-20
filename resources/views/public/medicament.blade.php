@extends('layouts.app')

@section('titre', $medicament->nom)

@section('contenu')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="card p-6">
            <div class="text-xs font-semibold uppercase tracking-wide text-menthe-600">{{ $medicament->categorie?->nom ?? 'Médicament' }}</div>
            <h1 class="mt-1 text-2xl font-black">{{ $medicament->nom }}</h1>
            <div class="mt-2 flex flex-wrap gap-2">
                @if($medicament->ordonnance_obligatoire)
                    <span class="badge bg-amber-100 text-amber-800">Ordonnance obligatoire</span>
                @else
                    <span class="badge bg-emerald-100 text-emerald-800">Sans ordonnance</span>
                @endif
                @if($medicament->forme) <span class="badge bg-slate-100 text-slate-700">{{ $medicament->forme }}</span> @endif
                @if($medicament->fabricant) <span class="badge bg-slate-100 text-slate-700">{{ $medicament->fabricant }}</span> @endif
                @if($noteMoyenne > 0) <span class="badge bg-amber-50 text-amber-700">⭐ {{ $noteMoyenne }}/5</span> @endif
            </div>

            @if($medicament->description)
                <p class="mt-4 text-sm text-slate-600">{{ $medicament->description }}</p>
            @endif
            @if($medicament->posologie)
                <div class="mt-3 rounded-xl bg-menthe-50 p-4 text-sm">
                    <span class="font-semibold">Posologie :</span> {{ $medicament->posologie }}
                </div>
            @endif
        </div>

        {{-- Pharmacies qui l'ont en stock --}}
        <section class="space-y-3">
            <h2 class="text-xl font-bold">Disponible dans {{ $stocks->count() }} pharmacie(s) — trié par prix</h2>
            @forelse($stocks as $pharmacie)
                <div class="card flex flex-wrap items-center justify-between gap-3 p-4">
                    <div>
                        <a href="{{ route('public.pharmacie', $pharmacie) }}" class="font-bold hover:text-menthe-700">🏥 {{ $pharmacie->nom }}</a>
                        <p class="text-xs text-slate-500">{{ $pharmacie->quartier }} · ⭐ {{ number_format($pharmacie->note_moyenne, 1) }}</p>
                        <p class="text-xs text-slate-400">Stock : {{ $pharmacie->pivot->quantite }} · Péremption : {{ $pharmacie->pivot->date_peremption ? \Illuminate\Support\Carbon::parse($pharmacie->pivot->date_peremption)->format('m/Y') : '—' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-black">{{ \App\Support\Fcfa::montant($pharmacie->pivot->prix) }}</div>
                        @auth
                            @if(auth()->user()->estClient())
                                <form action="{{ route('panier.ajouter', ['stock' => $pharmacie->pivot->pharmacie_id ?? $pharmacie->id]) }}" method="POST" class="mt-1">
                                    @csrf
                                    <input type="hidden" name="quantite" value="1">
                                    <button class="btn-primary text-xs">Ajouter au panier</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            @empty
                <p class="card p-6 text-sm text-slate-500">Aucune pharmacie ne propose ce médicament actuellement.</p>
            @endforelse
        </section>
    </div>

    {{-- Conseils santé --}}
    <aside class="space-y-4">
        <div class="card bg-menthe-50 p-5">
            <h3 class="font-bold">🤖 Un doute sur ce médicament ?</h3>
            <p class="mt-1 text-sm text-slate-600">Demandez conseil à notre assistant santé.</p>
            @auth
                <a href="{{ route('chatbot.index') }}" class="btn-secondary mt-3 w-full">Demander au chatbot</a>
            @endauth
        </div>
    </aside>
</div>
@endsection

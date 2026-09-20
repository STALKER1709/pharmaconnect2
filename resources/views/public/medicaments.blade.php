@extends('layouts.app')

@section('titre', 'Médicaments')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">Rechercher un médicament</h1>

    <form action="{{ route('public.medicaments') }}" method="GET" class="card flex flex-wrap items-end gap-3 p-4">
        <div class="flex-1 min-w-52">
            <label class="label">Nom, fabricant ou référence</label>
            <input type="search" name="q" value="{{ $q }}" class="input" placeholder="Paracétamol, Sanofi…">
        </div>
        <div class="w-48">
            <label class="label">Catégorie</label>
            <select name="categorie" class="input">
                <option value="">Toutes</option>
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->id }}" @selected($categorieId == $categorie->id)>{{ $categorie->icone }} {{ $categorie->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-44">
            <label class="label">Ordonnance</label>
            <select name="ordonnance" class="input">
                <option value="">Tous</option>
                <option value="1" @selected($ordonnance === '1')>Avec ordonnance</option>
                <option value="0" @selected($ordonnance === '0')>Sans ordonnance</option>
            </select>
        </div>
        <button class="btn-primary">Filtrer</button>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($medicaments as $medicament)
            <a href="{{ route('public.medicament', $medicament) }}" class="card p-4 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="text-xs font-semibold uppercase tracking-wide text-menthe-600">{{ $medicament->categorie?->nom ?? 'Médicament' }}</div>
                <h2 class="mt-1 font-bold">{{ $medicament->nom }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $medicament->forme }} · {{ $medicament->fabricant }}</p>
                @if($medicament->ordonnance_obligatoire)
                    <span class="badge mt-2 bg-amber-100 text-amber-800">Ordonnance requise</span>
                @endif
            </a>
        @empty
            <p class="card col-span-full p-6 text-sm text-slate-500">Aucun médicament trouvé pour ces critères.</p>
        @endforelse
    </div>

    {{ $medicaments->links() }}
</div>
@endsection

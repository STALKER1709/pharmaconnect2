@extends('layouts.app')

@section('titre', 'Mes commandes')

@section('contenu')
<div class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-lg flex flex-col gap-space-lg">
    <nav class="flex items-center gap-2 font-label-md text-label-md text-on-surface-variant">
        <a class="hover:text-primary transition-colors" href="{{ route('accueil') }}">Accueil</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a class="hover:text-primary transition-colors" href="{{ route('dashboard') }}">Mon espace</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface font-semibold">Mes commandes</span>
    </nav>
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm">
        <div>
            <h1 class="font-headline-xl-mobile text-headline-xl-mobile md:font-headline-xl md:text-headline-xl text-on-surface tracking-tight">Mes commandes</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-1">Suivez vos livraisons en temps réel et retrouvez l'historique de vos achats en officine.</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container text-on-surface font-label-sm text-label-sm self-start md:self-auto">
            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
            {{ $commandes->total() }} {{ \Illuminate\Support\Str::plural('commande', $commandes->total()) }}
        </div>
    </div>

    <section class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse ($commandes as $commande)
                @include('client.partials.ligne-commande')
            @empty
                <div class="p-space-xl text-center">
                    <span class="material-symbols-outlined text-[40px] text-outline">shopping_bag</span>
                    <p class="mt-2 font-headline-sm text-headline-sm text-on-surface">Aucune commande pour l'instant</p>
                    <a href="{{ route('public.medicaments') }}" class="mt-3 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg">Rechercher un médicament</a>
                </div>
            @endforelse
        </div>
    </section>

    @if ($commandes->hasPages())
        <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="font-body-md text-body-md text-on-surface-variant">Affichage de <span class="font-semibold text-on-surface">{{ $commandes->firstItem() }} - {{ $commandes->lastItem() }}</span> sur <span class="font-semibold text-on-surface">{{ $commandes->total() }}</span> commandes</div>
            {{ $commandes->links('partials.pagination') }}
        </div>
    @endif
</div>
@endsection

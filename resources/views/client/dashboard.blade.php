@extends('layouts.app')

@section('titre', 'Mon espace')

@php
    $ombre = 'shadow-[0_4px_20px_-2px_rgba(22,163,74,0.06),0_2px_6px_-1px_rgba(0,0,0,0.04)]';
    $enCours = $commandes->first(fn ($c) => in_array($c->statut->value, ['confirmee', 'prete', 'assignee', 'en_livraison'], true));
@endphp

@section('contenu')
<div class="w-full max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-lg flex flex-col gap-space-lg">
    <nav class="flex items-center gap-2 font-label-md text-label-md text-on-surface-variant">
        <a class="hover:text-primary transition-colors" href="{{ route('accueil') }}">Accueil</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface font-semibold">Mon espace</span>
    </nav>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm">
        <div>
            <h1 class="font-headline-xl-mobile text-headline-xl-mobile md:font-headline-xl md:text-headline-xl text-on-surface tracking-tight">Bonjour, {{ \Illuminate\Support\Str::before(auth()->user()->name.' ', ' ') }} 👋</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-1">Retrouvez vos commandes, vos échanges avec les officines et vos conseils santé.</p>
        </div>
        <a href="{{ route('public.medicaments') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm self-start md:self-auto">
            <span class="material-symbols-outlined text-[20px]">search</span>
            Rechercher un médicament
        </a>
    </div>

    @if ($enCours)
        <a href="{{ route('suivi.show', $enCours) }}" class="bg-[#dcfce9] rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#bbf7d0] transition-colors">
            <div class="flex items-center gap-3">
                <div class="relative w-12 h-12 rounded-xl bg-primary text-on-primary flex items-center justify-center flex-shrink-0">
                    <span class="absolute -inset-1 rounded-xl bg-primary/25 animate-pulse-ring"></span>
                    <span class="material-symbols-outlined text-[24px]">two_wheeler</span>
                </div>
                <div>
                    <p class="font-headline-sm text-headline-sm text-[#14532d]">Commande #{{ $enCours->numero }} — {{ $enCours->statut->label() }}</p>
                    <p class="font-body-md text-body-md text-[#14532d]/90">{{ $enCours->pharmacie?->nom }} → {{ \Illuminate\Support\Str::limit($enCours->adresse_livraison, 50) }}</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 font-label-lg text-label-lg text-[#14532d]">Suivre en direct <span class="material-symbols-outlined text-[18px]">arrow_forward</span></span>
        </a>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('commandes.index') }}" class="bg-surface-container-lowest rounded-2xl p-5 border border-[#e2e8f0] {{ $ombre }} flex items-start gap-3.5 transition-transform hover:-translate-y-0.5">
            <div class="w-12 h-12 rounded-xl bg-[#dcfce7] text-[#15803d] flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[26px]">receipt_long</span></div>
            <div>
                <p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Mes commandes</p>
                <p class="font-headline-lg text-headline-lg text-on-surface">{{ $nbCommandes }}</p>
            </div>
        </a>
        <a href="{{ route('messagerie.index') }}" class="bg-surface-container-lowest rounded-2xl p-5 border border-[#e2e8f0] {{ $ombre }} flex items-start gap-3.5 transition-transform hover:-translate-y-0.5">
            <div class="w-12 h-12 rounded-xl bg-[#e0f2fe] text-[#0369a1] flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[26px]">forum</span></div>
            <div>
                <h3 class="font-headline-sm text-headline-sm text-slate-900">Messagerie sécurisée</h3>
                <p class="mt-1 font-body-sm text-body-sm text-slate-600">Échangez avec votre pharmacien ou votre coursier.</p>
            </div>
        </a>
        <a href="{{ route('chatbot.index') }}" class="bg-surface-container-lowest rounded-2xl p-5 border border-[#e2e8f0] {{ $ombre }} flex items-start gap-3.5 transition-transform hover:-translate-y-0.5">
            <div class="w-12 h-12 rounded-xl bg-[#fef3c7] text-[#b45309] flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[26px]">smart_toy</span></div>
            <div>
                <h3 class="font-headline-sm text-headline-sm text-slate-900">PharmaBot</h3>
                <p class="mt-1 font-body-sm text-body-sm text-slate-600">Conseils posologiques et orientation 24/7.</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-start">
        <section class="lg:col-span-8 bg-surface-container-lowest rounded-2xl border border-[#e2e8f0] {{ $ombre }} overflow-hidden">
            <div class="flex items-center justify-between p-space-md border-b border-slate-100">
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Dernières commandes</h2>
                <a href="{{ route('commandes.index') }}" class="inline-flex items-center gap-1 font-label-lg text-label-lg text-primary hover:text-[#15803d]">Tout voir <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($commandes as $commande)
                    @include('client.partials.ligne-commande')
                @empty
                    <div class="p-space-xl text-center">
                        <span class="material-symbols-outlined text-[40px] text-outline">shopping_bag</span>
                        <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Aucune commande pour le moment.</p>
                        <a href="{{ route('public.medicaments') }}" class="mt-2 inline-flex items-center gap-1 font-label-lg text-label-lg text-primary hover:underline">Trouvez votre médicament <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="lg:col-span-4 flex flex-col gap-space-md">
            <div class="bg-surface-container-lowest rounded-2xl p-5 border border-[#e2e8f0] {{ $ombre }}">
                <div class="flex items-center gap-2 mb-space-sm">
                    <span class="material-symbols-outlined text-primary">local_pharmacy</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Pharmacies recommandées</h2>
                </div>
                <div class="flex flex-col gap-2">
                    @forelse ($pharmaciesProches as $p)
                        <a href="{{ route('public.pharmacie', $p) }}" class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center justify-between gap-2 transition-colors">
                            <div class="min-w-0">
                                <p class="font-label-lg text-label-lg text-on-surface truncate">{{ $p->nom }}</p>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ collect([$p->quartier, $p->ville])->filter()->implode(', ') }}</p>
                            </div>
                            <span class="font-label-sm text-label-sm text-amber-500 font-semibold whitespace-nowrap">★ {{ number_format((float) $p->note_moyenne, 1) }}</span>
                        </a>
                    @empty
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Aucune pharmacie partenaire pour le moment.</p>
                    @endforelse
                </div>
            </div>
            @if ($suggestions->isNotEmpty())
                <div class="bg-surface-container-lowest rounded-2xl p-5 border border-[#e2e8f0] {{ $ombre }}">
                    <div class="flex items-center gap-2 mb-space-sm">
                        <span class="material-symbols-outlined text-primary">medication</span>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Médicaments courants</h2>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($suggestions as $m)
                            <a href="{{ route('public.medicament', $m) }}" class="px-3 py-1.5 rounded-full bg-white border border-slate-200 hover:border-[#16a34a] hover:text-[#16a34a] font-label-sm text-label-sm transition-all">{{ $m->nom }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('titre', 'Pharmacies de garde & officines partenaires')

@section('contenu')
<div class="flex flex-col w-full">
    <section class="w-full pt-space-md pb-space-lg">
        <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop">
            <nav class="flex items-center gap-2 mb-space-sm font-label-md text-label-md text-on-surface-variant">
                <a class="hover:text-primary transition-colors" href="{{ route('accueil') }}">Accueil</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-on-surface font-semibold">Pharmacies de garde</span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm mb-space-lg">
                <div>
                    <div class="inline-flex items-center gap-1.5 font-label-md text-label-md font-semibold text-primary uppercase tracking-wider mb-1">
                        <span class="material-symbols-outlined text-[16px]">local_pharmacy</span>
                        <span>Réseau conventionné</span>
                    </div>
                    <h1 class="font-headline-xl-mobile text-headline-xl-mobile md:font-headline-xl md:text-headline-xl text-on-surface tracking-tight">Pharmacies partenaires à Douala</h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant mt-1">Commandez auprès des officines de référence certifiées, avec dispensation sous la responsabilité du titulaire.</p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container text-on-surface font-label-sm text-label-sm self-start md:self-auto">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span>{{ $pharmacies->total() }} {{ \Illuminate\Support\Str::plural('officine', $pharmacies->total()) }} {{ $pharmacies->total() > 1 ? 'connectées' : 'connectée' }}</span>
                </div>
            </div>
            <form method="GET" action="{{ route('public.pharmacies') }}" class="bg-surface-container-lowest rounded-2xl shadow-sm p-3 md:p-4">
                <div class="flex items-center gap-3 bg-[#f0fdf6] rounded-xl px-4 py-3">
                    <span class="material-symbols-outlined text-primary text-[24px]">search</span>
                    <input name="q" value="{{ $q }}" class="w-full bg-transparent border-none outline-none focus:ring-0 p-0 font-body-lg text-body-lg text-on-surface placeholder:text-outline" placeholder="Rechercher une officine par nom, quartier ou ville…" type="text"/>
                    @if ($q !== '')
                        <a href="{{ route('public.pharmacies') }}" class="p-1 rounded-full text-on-surface-variant hover:text-on-surface" title="Effacer"><span class="material-symbols-outlined text-[20px]">cancel</span></a>
                    @endif
                    <button type="submit" class="hidden sm:inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-colors shadow-sm">Rechercher</button>
                </div>
                <div class="flex items-center gap-2 mt-3 overflow-x-auto pb-1 text-nowrap">
                    <span class="font-label-sm text-label-sm text-on-surface-variant pr-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">location_on</span> Quartiers :</span>
                    <a href="{{ route('public.pharmacies') }}" class="px-3 py-1 rounded-full font-label-sm text-label-sm transition-colors {{ $q === '' ? 'bg-[#dcfce9] text-[#14532d] font-semibold' : 'bg-surface-container text-on-surface hover:bg-[#dcfce9] hover:text-[#14532d]' }}">Tous</a>
                    @foreach ($quartiers as $quartier)
                        <a href="{{ route('public.pharmacies', ['q' => $quartier]) }}" class="px-3 py-1 rounded-full font-label-sm text-label-sm transition-colors {{ mb_strtolower($q) === mb_strtolower($quartier) ? 'bg-[#dcfce9] text-[#14532d] font-semibold' : 'bg-surface-container text-on-surface hover:bg-[#dcfce9] hover:text-[#14532d]' }}">{{ $quartier }}</a>
                    @endforeach
                </div>
            </form>
        </div>
    </section>

    <section class="w-full pb-space-xl">
        <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop flex flex-col gap-space-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($pharmacies as $pharmacie)
                    <x-carte-pharmacie :pharmacie="$pharmacie" :mise-en-avant="$pharmacie->estOuverte()"/>
                @empty
                    <div class="col-span-full bg-surface-container-lowest rounded-2xl p-10 shadow-sm text-center">
                        <span class="material-symbols-outlined text-[40px] text-outline">search_off</span>
                        <p class="mt-2 font-headline-sm text-headline-sm text-on-surface">Aucune officine trouvée</p>
                        <p class="mt-1 font-body-md text-body-md text-on-surface-variant">Essayez un autre quartier ou une autre ville.</p>
                    </div>
                @endforelse
            </div>

            @if ($pharmacies->hasPages())
                <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="font-body-md text-body-md text-on-surface-variant">Affichage de <span class="font-semibold text-on-surface">{{ $pharmacies->firstItem() }} - {{ $pharmacies->lastItem() }}</span> sur <span class="font-semibold text-on-surface">{{ $pharmacies->total() }}</span> officines</div>
                    {{ $pharmacies->links('partials.pagination') }}
                </div>
            @endif

            <div class="bg-[#dcfce9] rounded-2xl p-5 shadow-sm flex flex-col md:flex-row items-center gap-4 text-center md:text-left">
                <div class="w-12 h-12 rounded-xl bg-primary text-on-primary flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[28px]">emergency</span></div>
                <div class="flex-1">
                    <h4 class="font-headline-sm text-headline-sm text-[#14532d]">Urgence de nuit ?</h4>
                    <p class="font-body-md text-body-md text-[#14532d]/90 mt-0.5">Les officines ouvertes sont mises en avant. En cas d'urgence vitale, composez le 119 (SAMU) ; pour un conseil, interrogez PharmaBot 24/7.</p>
                </div>
                <a href="{{ route('chatbot.index') }}" class="px-4 py-2 rounded-xl bg-surface-container-lowest text-[#14532d] font-label-lg text-label-lg shadow-sm flex-shrink-0">Demander à PharmaBot</a>
            </div>
        </div>
    </section>
</div>
@endsection

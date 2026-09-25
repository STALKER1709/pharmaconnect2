@extends('layouts.admin')

@section('titre', $user->name)

@php
    $carte = 'bg-surface-container-lowest rounded-xl shadow-sm p-space-md flex flex-col gap-space-sm';
    $statuts = [
        'actif' => ['Actif', 'bg-secondary-container/40 text-on-secondary-container', 'bg-primary'],
        'en_attente' => ['En attente', 'bg-tertiary-fixed text-on-tertiary-fixed', 'bg-tertiary'],
        'suspendu' => ['Suspendu', 'bg-error-container text-on-error-container', 'bg-error'],
    ];
    [$libelle, $couleurs, $point] = $statuts[$user->statut] ?? [$user->statut, 'bg-surface-container text-on-surface', 'bg-outline'];
    $document = $user->pharmacie?->document ?? $user->livreur?->document;
@endphp

@section('contenu')
<div class="flex flex-col w-full gap-space-lg max-w-4xl">
    <nav class="flex items-center gap-2 font-label-md text-label-md text-on-surface-variant pt-space-sm">
        <a href="{{ route('admin.utilisateurs') }}" class="hover:text-primary flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">arrow_back</span>Validation &amp; Gestion des Comptes</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-on-surface font-semibold">{{ $user->name }}</span>
    </nav>

    <div class="{{ $carte }} sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-space-sm">
            <div class="w-14 h-14 rounded-full bg-secondary-container text-primary flex items-center justify-center font-headline-sm text-headline-sm">{{ initiales($user->pharmacie?->nom ?? $user->name) }}</div>
            <div>
                <h1 class="font-headline-md text-headline-md text-on-surface">{{ $user->pharmacie?->nom ?? $user->name }}</h1>
                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $user->email }} · {{ $user->telephone ?? '—' }} · inscrit le {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-space-sm py-1 rounded-full font-label-md text-label-md font-semibold self-start {{ $couleurs }}"><span class="w-2 h-2 rounded-full {{ $point }}"></span>{{ $libelle }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
        @if ($user->estPharmacie() && $user->pharmacie)
            <div class="{{ $carte }}">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-primary">local_pharmacy</span><h2 class="font-headline-sm text-headline-sm text-on-surface">Officine</h2></div>
                <dl class="grid grid-cols-3 gap-y-2 font-body-sm text-body-sm">
                    <dt class="text-on-surface-variant">Titulaire</dt><dd class="col-span-2 text-on-surface">{{ $user->name }}</dd>
                    <dt class="text-on-surface-variant">Adresse</dt><dd class="col-span-2 text-on-surface">{{ collect([$user->pharmacie->adresse, $user->pharmacie->quartier, $user->pharmacie->ville])->filter()->implode(', ') }}</dd>
                    <dt class="text-on-surface-variant">GPS</dt><dd class="col-span-2 text-on-surface">{{ $user->pharmacie->latitude ?? '—' }}, {{ $user->pharmacie->longitude ?? '—' }}</dd>
                    <dt class="text-on-surface-variant">Livraison</dt><dd class="col-span-2 text-on-surface">{{ $user->pharmacie->on_livraison ? format_fcfa($user->pharmacie->frais_livraison) : 'Retrait uniquement' }}</dd>
                    <dt class="text-on-surface-variant">Horaires</dt><dd class="col-span-2 text-on-surface">{{ $user->pharmacie->horaires->where('ouvert', true)->count() }} jours d'ouverture / semaine</dd>
                </dl>
                @if ($user->pharmacie->statut === 'actif')
                    <a href="{{ route('public.pharmacie', $user->pharmacie) }}" class="self-start inline-flex items-center gap-1 font-label-md text-label-md text-primary hover:underline">Voir la fiche publique <span class="material-symbols-outlined text-[16px]">open_in_new</span></a>
                @endif
            </div>
        @endif
        @if ($user->estLivreur() && $user->livreur)
            <div class="{{ $carte }}">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-primary">two_wheeler</span><h2 class="font-headline-sm text-headline-sm text-on-surface">Coursier santé</h2></div>
                <dl class="grid grid-cols-3 gap-y-2 font-body-sm text-body-sm">
                    <dt class="text-on-surface-variant">Véhicule</dt><dd class="col-span-2 text-on-surface">{{ $user->livreur->vehiculeLabel() }} — {{ $user->livreur->immatriculation ?? '—' }}</dd>
                    <dt class="text-on-surface-variant">Ville</dt><dd class="col-span-2 text-on-surface">{{ $user->livreur->ville }}</dd>
                    <dt class="text-on-surface-variant">Disponibilité</dt><dd class="col-span-2 text-on-surface">{{ ['disponible' => 'En ligne', 'en_course' => 'En course', 'hors_ligne' => 'Hors ligne'][$user->livreur->disponibilite] ?? $user->livreur->disponibilite }}</dd>
                    <dt class="text-on-surface-variant">Note</dt><dd class="col-span-2 text-on-surface">★ {{ number_format((float) $user->livreur->note_moyenne, 1) }} ({{ $user->livreur->nb_avis }} avis)</dd>
                </dl>
            </div>
        @endif
        <div class="{{ $carte }}">
            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-primary">folder_open</span><h2 class="font-headline-sm text-headline-sm text-on-surface">Pièces justificatives</h2></div>
            @if ($document)
                <a href="{{ asset('storage/'.$document) }}" target="_blank" class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center gap-2 font-label-md text-label-md text-primary"><span class="material-symbols-outlined">visibility</span>Consulter le document déposé</a>
            @else
                <p class="font-body-sm text-body-sm text-on-surface-variant">Aucun document déposé.</p>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-space-sm">
        @if ($user->statut === 'en_attente')
            <form action="{{ route('admin.utilisateurs.valider', $user) }}" method="POST">
                @csrf
                <button class="flex items-center gap-1 px-space-md py-2 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-label-lg text-label-lg"><span class="material-symbols-outlined text-[18px]">check_circle</span>Valider ce compte</button>
            </form>
        @endif
        @if ($user->statut === 'suspendu')
            <form action="{{ route('admin.utilisateurs.reactiver', $user) }}" method="POST">
                @csrf
                <button class="flex items-center gap-1 px-space-md py-2 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-label-lg text-label-lg"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Réactiver</button>
            </form>
        @elseif (! $user->estAdmin())
            <form action="{{ route('admin.utilisateurs.suspendre', $user) }}" method="POST" onsubmit="return confirm('Suspendre ce compte ?')">
                @csrf
                <button class="flex items-center gap-1 px-space-md py-2 rounded-xl bg-error-container text-on-error-container hover:bg-error hover:text-on-error font-label-lg text-label-lg"><span class="material-symbols-outlined text-[18px]">block</span>Suspendre</button>
            </form>
        @endif
        @unless ($user->estAdmin())
            <form action="{{ route('admin.utilisateurs.destroy', $user) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce compte ?')">
                @csrf
                @method('DELETE')
                <button class="flex items-center gap-1 px-space-md py-2 rounded-xl bg-white border border-[#fecaca] text-[#dc2626] hover:bg-[#fef2f2] font-label-lg text-label-lg"><span class="material-symbols-outlined text-[18px]">delete</span>Supprimer</button>
            </form>
        @endunless
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('titre', 'Validation & Gestion des Comptes')

@php
    $total = $nbPharmaciesAttente + $nbLivreursAttente;
    $conformite = ($nbActifs + $nbSuspendus) > 0 ? round($nbActifs * 100 / ($nbActifs + $nbSuspendus), 1) : 100;
    $filtreUrl = fn (array $params) => route('admin.utilisateurs', array_filter(array_merge(request()->except(['page', 'demandes']), $params), fn ($v) => $v !== null));
    $pastilleOnglet = fn (bool $actif) => $actif ? 'px-space-sm py-1 rounded-lg bg-surface-container-lowest text-primary shadow-xs font-label-sm text-label-sm font-semibold' : 'px-space-sm py-1 rounded-lg text-on-surface-variant hover:text-on-surface font-label-sm text-label-sm';
    $couleursAvatar = ['bg-secondary-container text-primary', 'bg-surface-container-high text-primary', 'bg-primary-fixed text-on-primary-fixed'];
@endphp

@section('contenu')
<div class="flex flex-col w-full gap-space-lg">
    <!-- En-tête -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-space-xs max-w-3xl">
            <div class="flex items-center gap-space-xs text-primary">
                <span class="material-symbols-outlined text-[20px] icon-fill">verified_user</span>
                <span class="font-label-sm text-label-sm uppercase tracking-wider font-semibold">Plateforme Régulée • Ordre National des Pharmaciens</span>
            </div>
            <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-surface tracking-tight">Validation &amp; Gestion des Comptes</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Vérification des agréments ONPC / MINSANTE des officines et accréditation des coursiers partenaires — Douala &amp; Région du Littoral</p>
        </div>
        <div class="flex items-center gap-space-sm flex-wrap">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-space-xs px-space-md py-space-sm rounded-xl bg-surface-container-lowest text-primary shadow-sm hover:bg-surface-container-low transition-all">
                <span class="material-symbols-outlined text-[18px]">monitoring</span>
                <span class="font-label-lg text-label-lg whitespace-nowrap">Statistiques nationales</span>
            </a>
        </div>
    </div>

    <!-- Indicateurs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <a href="{{ $filtreUrl(['type' => 'pharmacie']) }}" class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Officines en attente</span>
                    <div class="font-headline-xl text-headline-xl text-tertiary mt-space-xs">{{ $nbPharmaciesAttente }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed">
                    <span class="material-symbols-outlined text-[26px]">local_pharmacy</span>
                </div>
            </div>
            <div class="mt-space-md flex items-center gap-space-xs text-tertiary font-label-sm text-label-sm">
                @if ($nbPharmaciesAttente > 0)<span class="w-2 h-2 rounded-full bg-tertiary animate-ping"></span>@endif
                <span>Pièces justificatives à vérifier</span>
            </div>
        </a>
        <a href="{{ $filtreUrl(['type' => 'livreur']) }}" class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Coursiers santé</span>
                    <div class="font-headline-xl text-headline-xl text-on-surface mt-space-xs">{{ $nbLivreursAttente }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[26px]">two_wheeler</span>
                </div>
            </div>
            <div class="mt-space-md flex items-center gap-space-xs text-on-surface-variant font-label-sm text-label-sm">
                <span class="material-symbols-outlined text-[16px] text-primary">id_card</span>
                <span>Permis &amp; pièce d'identité à vérifier</span>
            </div>
        </a>
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Comptes certifiés</span>
                    <div class="font-headline-xl text-headline-xl text-primary mt-space-xs">{{ $nbActifs }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-secondary-container/40 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[26px]">how_to_reg</span>
                </div>
            </div>
            <div class="mt-space-md flex items-center gap-space-xs text-primary font-label-sm text-label-sm font-semibold">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+{{ $nbActifsMois }} {{ $nbActifsMois > 1 ? 'inscrits' : 'inscrit' }} ce mois-ci</span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Conformité ONPC / MINSANTE</span>
                    <div class="font-headline-xl text-headline-xl text-primary mt-space-xs">{{ str_replace('.', ',', $conformite) }}%</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-secondary-container/40 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[26px]">verified</span>
                </div>
            </div>
            <div class="mt-space-md flex items-center gap-space-xs text-on-surface-variant font-label-sm text-label-sm">
                <span class="material-symbols-outlined text-[16px] text-primary">policy</span>
                <span>Comptes actifs / comptes contrôlés</span>
            </div>
        </div>
    </div>

    <!-- Bandeau réglementaire -->
    <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm flex flex-col lg:flex-row items-center gap-space-lg">
        <div class="relative w-full lg:w-48 h-32 rounded-xl overflow-hidden flex-shrink-0 bg-secondary-container/30 flex items-center justify-center">
            <span class="material-symbols-outlined text-[64px] text-primary/70">local_pharmacy</span>
            <div class="absolute inset-0 bg-primary/10"></div>
            <div class="absolute bottom-2 left-2 bg-surface-container-lowest/90 px-space-xs py-0.5 rounded text-[10px] font-bold text-primary flex items-center gap-1">
                <span class="material-symbols-outlined text-[12px]">verified</span> ONPC Littoral
            </div>
        </div>
        <div class="flex-1 flex flex-col gap-space-xs">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container/30 text-on-secondary-container font-label-sm text-label-sm">Protocole Sanitaire {{ now()->year }}</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">• Procédure simplifiée pour le Littoral</span>
            </div>
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Campagne d'harmonisation des licences d'exploitation</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Toute officine souhaitant dispenser les médicaments sous contrôle strict doit obligatoirement charger sa quittance de cotisation ordinale et l'autorisation d'exercice signée par l'inspecteur régional de la santé.</p>
        </div>
    </div>

    <!-- Demandes en attente -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-md flex flex-col gap-space-md">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-sm">
            <div class="flex items-center gap-space-sm">
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Demandes d'inscription en attente</h2>
                @if ($total > 0)
                    <span class="px-space-sm py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-md text-label-md font-bold whitespace-nowrap">{{ $total }} à traiter</span>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-space-xs">
                <div class="flex bg-surface-container-low p-1 rounded-xl">
                    <a href="{{ $filtreUrl(['type' => null]) }}" class="{{ $pastilleOnglet(! $type) }}">Tous ({{ $total }})</a>
                    <a href="{{ $filtreUrl(['type' => 'pharmacie']) }}" class="{{ $pastilleOnglet($type === 'pharmacie') }}">Pharmacies ({{ $nbPharmaciesAttente }})</a>
                    <a href="{{ $filtreUrl(['type' => 'livreur']) }}" class="{{ $pastilleOnglet($type === 'livreur') }}">Livreurs ({{ $nbLivreursAttente }})</a>
                </div>
                <form method="GET" action="{{ route('admin.utilisateurs') }}" class="flex items-center bg-surface-container-low px-space-sm py-1.5 rounded-xl">
                    @if ($type)<input type="hidden" name="type" value="{{ $type }}">@endif
                    @if ($statut)<input type="hidden" name="statut" value="{{ $statut }}">@endif
                    <span class="material-symbols-outlined text-[16px] text-outline mr-space-xs">filter_list</span>
                    <input name="q" value="{{ $q }}" class="bg-transparent border-0 p-0 focus:ring-0 text-body-sm font-body-sm text-on-surface placeholder:text-outline focus:outline-none w-36" placeholder="Filtrer la liste..." type="text"/>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                        <th class="py-space-sm px-space-md rounded-l-xl">Type &amp; Statut</th>
                        <th class="py-space-sm px-space-md">Raison Sociale / Demandeur</th>
                        <th class="py-space-sm px-space-md">Pièces Justificatives</th>
                        <th class="py-space-sm px-space-md">Contact &amp; Ville</th>
                        <th class="py-space-sm px-space-md">Dépôt</th>
                        <th class="py-space-sm px-space-md text-right rounded-r-xl">Décision Régulatoire</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low font-body-sm text-body-sm">
                    @forelse ($demandes as $user)
                        @php
                            $estPharmacie = $user->estPharmacie();
                            $profil = $estPharmacie ? $user->pharmacie : $user->livreur;
                            $document = $profil?->document;
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="py-space-md px-space-md">
                                @if ($estPharmacie)
                                    <span class="inline-flex items-center gap-1 px-space-sm py-0.5 rounded-full bg-secondary-container/40 text-on-secondary-container font-label-sm text-label-sm font-semibold"><span class="material-symbols-outlined text-[14px]">local_pharmacy</span>Pharmacie</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-space-sm py-0.5 rounded-full bg-surface-container-highest text-on-surface font-label-sm text-label-sm font-semibold whitespace-nowrap"><span class="material-symbols-outlined text-[14px]">motorcycle</span>Coursier Santé</span>
                                @endif
                            </td>
                            <td class="py-space-md px-space-md">
                                <a href="{{ route('admin.utilisateurs.show', $user) }}" class="flex flex-col hover:text-primary">
                                    <span class="font-label-lg text-label-lg font-semibold text-on-surface">{{ $estPharmacie ? ($profil?->nom ?? $user->name) : $user->name }}</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $estPharmacie ? $user->name.' (Titulaire)' : ucfirst($profil?->vehicule ?? 'Moto').($profil?->immatriculation ? ' · '.$profil->immatriculation : '') }}</span>
                                </a>
                            </td>
                            <td class="py-space-md px-space-md">
                                <div class="flex items-center gap-space-xs">
                                    <span class="font-label-sm text-label-sm text-on-surface bg-surface-container-high px-space-xs py-0.5 rounded whitespace-nowrap">{{ $document ? ($estPharmacie ? 'Agrément ONPC' : 'Permis / CNI') : 'Aucune pièce' }}</span>
                                    @if ($document)
                                        <a href="{{ asset('storage/'.$document) }}" target="_blank" class="flex items-center gap-1 text-primary hover:underline font-label-sm text-label-sm" title="Vérifier la pièce justificative">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            <span>Voir pièce</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="py-space-md px-space-md">
                                <div class="flex flex-col">
                                    <span class="text-on-surface font-medium whitespace-nowrap">{{ $user->telephone ?? $user->email }}</span>
                                    <span class="text-on-surface-variant text-label-sm font-label-sm">{{ $estPharmacie ? collect([$profil?->quartier, $profil?->adresse])->filter()->unique()->implode(' - ') : ($profil?->ville ?? 'Douala') }}</span>
                                </div>
                            </td>
                            <td class="py-space-md px-space-md text-on-surface-variant font-label-sm text-label-sm whitespace-nowrap">{{ ucfirst($user->created_at->translatedFormat('d M Y, H:i')) }}</td>
                            <td class="py-space-md px-space-md text-right">
                                <div class="flex items-center justify-end gap-space-xs">
                                    <form method="POST" action="{{ route('admin.utilisateurs.valider', $user) }}">
                                        @csrf
                                        <button class="flex items-center gap-1 px-space-sm py-1.5 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-label-md text-label-md transition-all" type="submit">
                                            <span class="material-symbols-outlined text-[16px]">check_circle</span><span>Valider</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.utilisateurs.suspendre', $user) }}" onsubmit="return confirm('Refuser cette demande ? Le compte sera suspendu.')">
                                        @csrf
                                        <button class="flex items-center gap-1 px-space-sm py-1.5 rounded-xl bg-error-container text-on-error-container hover:bg-error hover:text-on-error font-label-md text-label-md transition-all" type="submit">
                                            <span class="material-symbols-outlined text-[16px]">cancel</span><span>Refuser</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-space-lg text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[28px] text-primary block mb-1">task_alt</span>
                            Aucune demande d'inscription en attente.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between pt-space-xs gap-3 flex-wrap">
            <span class="font-body-sm text-body-sm text-on-surface-variant">Affichage de {{ $demandes->count() }} {{ \Illuminate\Support\Str::plural('demande', $demandes->count()) }} sur {{ $demandes->total() }} {{ $demandes->total() > 1 ? 'enregistrées' : 'enregistrée' }}</span>
            @include('admin.partials.pagination-simple', ['paginator' => $demandes])
        </div>
    </div>

    <!-- Répertoire -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-md flex flex-col gap-space-md" id="repertoire">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-sm">
            <div class="flex flex-col">
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Répertoire des Partenaires Accrédités (Littoral)</h2>
                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $nbActifs + $nbSuspendus }} comptes contrôlés (officines, coursiers et patients)</span>
            </div>
            <div class="flex flex-wrap items-center gap-space-xs">
                <div class="flex bg-surface-container-low p-1 rounded-xl">
                    <a href="{{ $filtreUrl(['statut' => null]) }}#repertoire" class="{{ $pastilleOnglet(! $statut) }}">Tous ({{ $nbActifs + $nbSuspendus }})</a>
                    <a href="{{ $filtreUrl(['statut' => 'actif']) }}#repertoire" class="{{ $pastilleOnglet($statut === 'actif') }}">Actifs ({{ $nbActifs }})</a>
                    <a href="{{ $filtreUrl(['statut' => 'suspendu']) }}#repertoire" class="{{ $pastilleOnglet($statut === 'suspendu') }}">Suspendus ({{ $nbSuspendus }})</a>
                </div>
                <form method="GET" action="{{ route('admin.utilisateurs') }}#repertoire" class="flex items-center bg-surface-container-low px-space-sm py-1.5 rounded-xl">
                    @if ($type)<input type="hidden" name="type" value="{{ $type }}">@endif
                    @if ($statut)<input type="hidden" name="statut" value="{{ $statut }}">@endif
                    <span class="material-symbols-outlined text-[16px] text-outline mr-space-xs">search</span>
                    <input name="q" value="{{ $q }}" class="bg-transparent border-0 p-0 focus:ring-0 text-body-sm font-body-sm text-on-surface placeholder:text-outline focus:outline-none w-48" placeholder="Rechercher par nom, quartier..." type="search"/>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                        <th class="py-space-sm px-space-md rounded-l-xl">Partenaire</th>
                        <th class="py-space-sm px-space-md">Rôle &amp; Spécificité</th>
                        <th class="py-space-sm px-space-md">Implantation</th>
                        <th class="py-space-sm px-space-md">Inscription</th>
                        <th class="py-space-sm px-space-md">Statut</th>
                        <th class="py-space-sm px-space-md">Contact</th>
                        <th class="py-space-sm px-space-md text-right rounded-r-xl">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low font-body-sm text-body-sm">
                    @forelse ($repertoire as $user)
                        @php
                            $suspendu = $user->statut === 'suspendu';
                            $nom = $user->estPharmacie() ? ($user->pharmacie?->nom ?? $user->name) : $user->name;
                            $role = match ($user->role instanceof \BackedEnum ? $user->role->value : $user->role) {
                                'pharmacie' => ['Pharmacie Titulaire', 'bg-primary'],
                                'livreur' => ['Coursier santé · '.ucfirst($user->livreur?->vehicule ?? 'moto'), 'bg-secondary'],
                                default => ['Patient', 'bg-outline'],
                            };
                            $implantation = $user->estPharmacie() ? collect([$user->pharmacie?->ville, $user->pharmacie?->quartier])->filter()->implode(' - ') : ($user->livreur?->ville ?? $user->client?->quartier ?? 'Douala');
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition-colors {{ $suspendu ? 'bg-error-container/10' : '' }}">
                            <td class="py-space-md px-space-md">
                                <a href="{{ route('admin.utilisateurs.show', $user) }}" class="flex items-center gap-space-sm">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-label-md flex-shrink-0 {{ $suspendu ? 'bg-error-container text-on-error-container' : $couleursAvatar[$loop->index % 3] }}">{{ initiales($nom) }}</div>
                                    <div class="flex flex-col">
                                        <span class="font-label-lg text-label-lg font-semibold text-on-surface hover:text-primary">{{ $nom }}</span>
                                        <span class="font-body-sm text-body-sm {{ $suspendu ? 'text-error font-medium' : 'text-on-surface-variant' }}">{{ $suspendu ? 'Compte suspendu' : $user->email }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="py-space-md px-space-md"><span class="inline-flex items-center gap-1.5 text-on-surface"><span class="w-2 h-2 rounded-full {{ $role[1] }}"></span>{{ $role[0] }}</span></td>
                            <td class="py-space-md px-space-md text-on-surface">{{ $implantation ?: 'Douala' }}</td>
                            <td class="py-space-md px-space-md text-on-surface-variant font-label-sm text-label-sm whitespace-nowrap">{{ ucfirst($user->created_at->translatedFormat('d M Y')) }}</td>
                            <td class="py-space-md px-space-md">
                                @if ($suspendu)
                                    <span class="inline-flex items-center gap-1.5 px-space-sm py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-semibold"><span class="w-2 h-2 rounded-full bg-error"></span>Suspendu</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-space-sm py-0.5 rounded-full bg-secondary-container/40 text-on-secondary-container font-label-sm text-label-sm font-semibold"><span class="w-2 h-2 rounded-full bg-primary"></span>Actif</span>
                                @endif
                            </td>
                            <td class="py-space-md px-space-md text-on-surface-variant font-label-sm text-label-sm whitespace-nowrap">{{ $user->telephone ?? '—' }}</td>
                            <td class="py-space-md px-space-md text-right">
                                <div class="flex items-center justify-end gap-space-xs">
                                    @if ($suspendu)
                                        <form method="POST" action="{{ route('admin.utilisateurs.reactiver', $user) }}">
                                            @csrf
                                            <button class="text-primary hover:bg-secondary-container/40 px-space-sm py-1 rounded-lg font-label-md text-label-md transition-colors font-semibold" type="submit">Réactiver</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.utilisateurs.suspendre', $user) }}" onsubmit="return confirm('Suspendre ce compte ?')">
                                            @csrf
                                            <button class="text-error hover:bg-error-container/40 px-space-sm py-1 rounded-lg font-label-md text-label-md transition-colors" type="submit">Suspendre</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.utilisateurs.show', $user) }}" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors" title="Fiche détaillée">
                                        <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-space-lg text-center text-on-surface-variant">Aucun compte ne correspond à ces critères.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-space-sm pt-space-xs">
            <div class="flex items-center gap-space-xs text-on-surface-variant font-body-sm text-body-sm">
                <span class="material-symbols-outlined text-[18px] text-primary">security</span>
                <span>Registre officiel PharmaConnect — décisions tracées et notifiées aux intéressés.</span>
            </div>
            @include('admin.partials.pagination-simple', ['paginator' => $repertoire])
        </div>
    </div>
</div>
@endsection

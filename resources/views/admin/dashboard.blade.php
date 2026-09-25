@extends('layouts.admin')

@section('titre', 'Statistiques nationales')

@push('scripts')
<script type="module">
    PharmaConnect.graphiqueCA(document.getElementById('graph-ca'), @json($caParJour->pluck('jour')), @json($caParJour->pluck('total')));
</script>
@endpush

@section('contenu')
<div class="flex flex-col w-full gap-space-lg">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-space-xs max-w-3xl">
            <div class="flex items-center gap-space-xs text-primary">
                <span class="material-symbols-outlined text-[20px] icon-fill">verified_user</span>
                <span class="font-label-sm text-label-sm uppercase tracking-wider font-semibold">Plateforme Régulée • Ordre National des Pharmaciens</span>
            </div>
            <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-surface tracking-tight">Statistiques de la plateforme</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Activité des officines, coursiers et patients — Douala &amp; Région du Littoral</p>
        </div>
        <a href="{{ route('admin.utilisateurs') }}" class="flex items-center gap-space-xs px-space-md py-space-sm rounded-xl bg-primary text-on-primary shadow-sm hover:bg-primary-container transition-all self-start">
            <span class="material-symbols-outlined text-[18px]">group</span>
            <span class="font-label-lg text-label-lg">Valider les comptes @if($enAttente->isNotEmpty())({{ $enAttente->count() }})@endif</span>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        @foreach ([
            ['Patients inscrits', $nbClients, 'person', 'bg-surface-container-high text-primary', 'text-on-surface'],
            ['Officines actives', $nbPharmacies, 'local_pharmacy', 'bg-secondary-container/40 text-primary', 'text-primary'],
            ['Coursiers actifs', $nbLivreurs, 'two_wheeler', 'bg-surface-container-high text-primary', 'text-on-surface'],
            ['Commandes', $nbCommandes, 'receipt_long', 'bg-tertiary-fixed text-on-tertiary-fixed', 'text-tertiary'],
        ] as [$libelle, $valeur, $icone, $teinte, $couleur])
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">{{ $libelle }}</span>
                    <div class="font-headline-xl text-headline-xl {{ $couleur }} mt-space-xs">{{ $valeur }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $teinte }} flex items-center justify-center"><span class="material-symbols-outlined text-[26px]">{{ $icone }}</span></div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
        <div class="lg:col-span-8 bg-surface-container-lowest rounded-xl shadow-sm p-space-md flex flex-col gap-space-md">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Volume d'affaires — 7 derniers jours</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Commandes validées toutes officines (FCFA)</p>
                </div>
                <div class="flex gap-2">
                    <span class="px-space-sm py-1 rounded-full bg-secondary-container/30 text-on-secondary-container font-label-sm text-label-sm">Mois : {{ format_fcfa($caMois) }}</span>
                    <span class="px-space-sm py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">Total livré : {{ format_fcfa($caTotal) }}</span>
                </div>
            </div>
            <div class="h-72"><canvas id="graph-ca"></canvas></div>
        </div>
        <div class="lg:col-span-4 bg-surface-container-lowest rounded-xl shadow-sm p-space-md flex flex-col gap-space-sm">
            <div class="flex items-center justify-between">
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Comptes en attente</h2>
                @if ($enAttente->isNotEmpty())<span class="px-space-sm py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-md text-label-md font-bold">{{ $enAttente->count() }}</span>@endif
            </div>
            @forelse ($enAttente->take(6) as $user)
                <a href="{{ route('admin.utilisateurs.show', $user) }}" class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center gap-3 transition-colors">
                    <span class="material-symbols-outlined text-primary">{{ $user->estPharmacie() ? 'local_pharmacy' : 'two_wheeler' }}</span>
                    <div class="min-w-0">
                        <p class="font-label-lg text-label-lg text-on-surface truncate">{{ $user->pharmacie?->nom ?? $user->name }}</p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Inscrit {{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </a>
            @empty
                <div class="p-4 rounded-xl bg-surface-container-low text-center font-body-sm text-body-sm text-on-surface-variant"><span class="material-symbols-outlined text-primary block mb-1">task_alt</span>Aucune demande en attente.</div>
            @endforelse
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-md flex flex-col gap-space-md">
        <h2 class="font-headline-sm text-headline-sm text-on-surface">Commandes récentes</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                        <th class="py-space-sm px-space-md rounded-l-xl">Commande</th>
                        <th class="py-space-sm px-space-md">Patient</th>
                        <th class="py-space-sm px-space-md">Officine</th>
                        <th class="py-space-sm px-space-md">Statut</th>
                        <th class="py-space-sm px-space-md text-right rounded-r-xl">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low font-body-sm text-body-sm">
                    @forelse ($commandesRecentes as $commande)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="py-space-md px-space-md"><span class="font-label-lg text-label-lg text-on-surface">#{{ $commande->numero }}</span><p class="text-on-surface-variant">{{ $commande->created_at->format('d/m/Y H:i') }}</p></td>
                            <td class="py-space-md px-space-md text-on-surface">{{ $commande->client?->user?->name }}</td>
                            <td class="py-space-md px-space-md text-on-surface">{{ $commande->pharmacie?->nom }}</td>
                            <td class="py-space-md px-space-md"><x-badge-statut :statut="$commande->statut"/></td>
                            <td class="py-space-md px-space-md text-right font-label-lg text-label-lg text-on-surface whitespace-nowrap">{{ format_fcfa($commande->total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-space-lg text-center text-on-surface-variant">Aucune commande.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

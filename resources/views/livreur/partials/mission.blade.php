{{-- Carte « Mission en cours » — maquette tableau_de_bord_coursier (carte, étapes, actions) --}}
@php
    use App\Enums\LivraisonStatut;

    $commande = $livraison->commande;
    $pharmacie = $commande?->pharmacie;
    $client = $commande?->client?->user;
    $statut = $livraison->statut;
    $telClient = $client?->telephone ? preg_replace('/[^0-9+]/', '', $client->telephone) : null;
    $etapeCourante = match ($statut) {
        LivraisonStatut::Disponible, LivraisonStatut::Assignee, LivraisonStatut::Acceptee => 1,
        LivraisonStatut::EnRoute => 2,
        LivraisonStatut::Arrivee => 3,
        default => 4,
    };
    $etapes = [
        [1, 'Officine', $pharmacie?->nom, $livraison->acceptee_at ? 'Accepté à '.$livraison->acceptee_at->format('H\hi') : 'À récupérer', 'local_pharmacy'],
        [2, 'En transit', 'Vers '.\Illuminate\Support\Str::limit($commande?->adresse_livraison, 22), $livraison->en_route_at ? 'Départ '.$livraison->en_route_at->format('H\hi') : 'À venir', 'motorcycle'],
        [3, 'Arrivé client', \Illuminate\Support\Str::limit($commande?->adresse_livraison, 22), $livraison->arrivee_at ? 'Arrivé à '.$livraison->arrivee_at->format('H\hi') : 'À venir', 'location_on'],
        [4, 'Remise sac', 'Remise en main propre', 'Clôture course', 'inventory'],
    ];
    $icones = \App\Support\IconesCarte::toutes();
@endphp
<div class="bg-surface-container-lowest rounded-2xl shadow-sm p-6 flex flex-col gap-space-md relative overflow-hidden"
     x-data="PharmaConnect.suivi({
         commandeId: {{ $commande?->id ?? 0 }},
         statut: '{{ $statut->value }}',
         statutLabel: '{{ $statut->label() }}',
         statutLivraison: '{{ $statut->value }}',
         estLivreur: true,
         latitude: {{ $pharmacie?->latitude ?? 4.0511 }},
         longitude: {{ $pharmacie?->longitude ?? 9.7679 }},
         pharmacie: @js($pharmacie?->latitude ? ['lat' => (float) $pharmacie->latitude, 'lng' => (float) $pharmacie->longitude, 'nom' => $pharmacie->nom] : null),
         arrivee: @js($livraison->latitude_arrivee ? ['lat' => (float) $livraison->latitude_arrivee, 'lng' => (float) $livraison->longitude_arrivee] : null),
         icones: @js($icones),
         csrf: '{{ csrf_token() }}',
         urls: {
             position: '{{ $commande ? route('suivi.position', $commande) : '' }}',
             partager: '#',
             signalerPosition: '{{ route('livreur.livraisons.position', $livraison) }}',
         },
     })"
     @beforeunload.window="destroy()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="bg-secondary-container/50 text-on-secondary-container font-label-sm text-label-sm px-3 py-1 rounded-full font-semibold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[15px] text-primary">priority_high</span>
                MISSION EN COURS · <span x-text="statutLabel">{{ $statut->label() }}</span>
            </span>
            <span class="font-label-md text-label-md text-on-surface-variant bg-surface-container px-2.5 py-0.5 rounded-md font-mono">#{{ $commande?->numero }}</span>
        </div>
        <div class="flex items-center gap-1 bg-surface-container-low px-3 py-1.5 rounded-xl text-right">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Rémunération :</span>
            <span class="font-label-lg text-label-lg font-bold text-primary whitespace-nowrap">+{{ format_fcfa($commande?->frais_livraison) }}</span>
            <span class="text-[11px] text-on-surface-variant hidden md:inline">(Frais de livraison)</span>
        </div>
    </div>

    <div class="w-full bg-surface-container-low rounded-xl p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 relative">
            @foreach ($etapes as [$numero, $titre, $ligne1, $ligne2, $icone])
                @if ($numero < $etapeCourante)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[16px]">check</span></div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-sm text-label-sm font-semibold text-primary">{{ $numero }}. {{ $titre }}</span>
                            <span class="text-[11px] text-on-surface font-medium truncate">{{ $ligne1 }}</span>
                            <span class="text-[10px] text-on-surface-variant">{{ $ligne2 }}</span>
                        </div>
                    </div>
                @elseif ($numero === $etapeCourante)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center shrink-0 relative">
                            <span class="animate-ping absolute inset-0 rounded-full bg-primary/40"></span>
                            <span class="material-symbols-outlined text-[16px]">{{ $icone }}</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-sm text-label-sm font-bold text-primary">{{ $numero }}. {{ $titre }}</span>
                            <span class="text-[11px] text-on-surface font-medium truncate">{{ $ligne1 }}</span>
                            <span class="text-[10px] text-primary font-semibold">{{ $ligne2 }}</span>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3 opacity-60">
                        <div class="w-8 h-8 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[16px]">{{ $icone }}</span></div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-sm text-label-sm font-medium text-on-surface-variant">{{ $numero }}. {{ $titre }}</span>
                            <span class="text-[11px] text-on-surface-variant truncate">{{ $ligne1 }}</span>
                            <span class="text-[10px] text-on-surface-variant">{{ $ligne2 }}</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="relative w-full h-80 rounded-xl overflow-hidden bg-surface-container-high shadow-inner">
        @include('partials.carte-douala')
        <div id="carte-suivi" class="absolute inset-0 z-10 [&.leaflet-container]:bg-transparent"></div>
        <div class="absolute inset-0 pointer-events-none p-4 flex flex-col justify-between z-20">
            <div class="flex items-center justify-between w-full flex-wrap gap-2">
                <div class="bg-surface/95 backdrop-blur-md px-3.5 py-1.5 rounded-xl shadow-md flex items-center gap-3">
                    <div class="flex items-center gap-1.5 text-primary">
                        <span class="material-symbols-outlined text-[18px]">navigation</span>
                        <span class="font-label-md text-label-md font-bold">{{ $livraison->distance_km ? number_format($livraison->distance_km, 1, ',', ' ').' km' : 'Trajet' }}</span>
                    </div>
                    <span class="text-outline-variant">•</span>
                    <span class="font-label-sm text-label-sm text-on-surface">{{ $livraison->duree_estimee_min ? '~'.$livraison->duree_estimee_min.' min' : 'GPS actif' }}</span>
                    <span class="text-outline-variant">•</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Position <span x-text="depuisMaj"></span></span>
                </div>
                <div class="bg-surface/95 backdrop-blur-md px-3 py-1.5 rounded-xl shadow-md flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[18px]">ac_unit</span>
                    <span class="font-label-sm text-label-sm text-on-surface font-medium">Sacoche isotherme</span>
                    <span class="text-[10px] bg-secondary-container text-on-secondary-container px-1.5 rounded font-semibold">Optimal</span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="bg-surface/90 backdrop-blur-sm px-2.5 py-1 rounded-md text-[11px] text-on-surface-variant font-mono">{{ $pharmacie?->quartier }} → {{ \Illuminate\Support\Str::limit($commande?->ville_livraison, 30) }}</div>
                <div class="bg-surface/90 backdrop-blur-sm px-2.5 py-1 rounded-md text-[11px] text-primary font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">sensors</span>
                    Partage GPS automatique
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-3 pt-1">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            {{-- 1. Accepter / Démarrer --}}
            @if (in_array($statut, [LivraisonStatut::Disponible, LivraisonStatut::Assignee], true))
                <form method="POST" action="{{ route('livreur.livraisons.accepter', $livraison) }}" class="flex">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg transition-transform hover:-translate-y-0.5 shadow-md"><span class="material-symbols-outlined text-[18px]">task_alt</span>Accepter la course</button>
                </form>
            @elseif ($statut === LivraisonStatut::Acceptee)
                <form method="POST" action="{{ route('livreur.livraisons.demarrer', $livraison) }}" class="flex">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg transition-transform hover:-translate-y-0.5 shadow-md"><span class="material-symbols-outlined text-[18px] animate-bounce">two_wheeler</span>Démarrer la course</button>
                </form>
            @else
                <button class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-surface-container text-on-surface-variant font-label-md text-label-md cursor-not-allowed" disabled>
                    <span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>
                    {{ $livraison->en_route_at ? 'Démarré à '.$livraison->en_route_at->format('H\hi') : 'Course démarrée' }}
                </button>
            @endif

            {{-- 2. Arrivé sur place --}}
            @if ($statut === LivraisonStatut::EnRoute)
                <form method="POST" action="{{ route('livreur.livraisons.arrivee', $livraison) }}" class="flex">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg transition-transform hover:-translate-y-0.5 active:translate-y-0 shadow-md"><span class="material-symbols-outlined text-[18px] animate-bounce">location_on</span>Arrivé sur place</button>
                </form>
            @elseif ($statut === LivraisonStatut::Arrivee)
                <button class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-surface-container text-primary font-label-lg text-label-lg cursor-not-allowed" disabled><span class="material-symbols-outlined text-[18px]">done_all</span>Confirmé sur place</button>
            @else
                <button class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-surface-container-lowest text-on-surface-variant font-label-lg text-label-lg shadow-sm opacity-60 cursor-not-allowed" disabled><span class="material-symbols-outlined text-[18px]">location_on</span>Arrivé sur place</button>
            @endif

            {{-- 3. Marquer livrée --}}
            @if ($statut === LivraisonStatut::Arrivee)
                <form method="POST" action="{{ route('livreur.livraisons.livrer', $livraison) }}" class="flex">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg shadow-md"><span class="material-symbols-outlined text-[18px]">verified</span>Marquer livrée</button>
                </form>
            @else
                <button class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-surface-container-lowest text-primary font-label-lg text-label-lg shadow-sm opacity-60 cursor-not-allowed" disabled><span class="material-symbols-outlined text-[18px]">verified</span>Marquer livrée</button>
            @endif
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
            <div class="flex flex-wrap items-center gap-2">
                @if ($telClient)
                    <a class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors font-label-md text-label-md" href="tel:{{ $telClient }}">
                        <span class="material-symbols-outlined text-primary text-[16px]">call</span>
                        Appeler le client ({{ $client->telephone }})
                    </a>
                @endif
                @if ($client)
                    <form method="POST" action="{{ route('messagerie.demarrer', $client) }}">
                        @csrf
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors font-label-md text-label-md">
                            <span class="material-symbols-outlined text-primary text-[16px]">chat</span>
                            Message
                        </button>
                    </form>
                @endif
            </div>
            @if ($pharmacie?->user)
                <form method="POST" action="{{ route('messagerie.demarrer', $pharmacie->user) }}">
                    @csrf
                    <button class="inline-flex items-center gap-1 text-tertiary hover:underline font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[16px]">report_problem</span>
                        Signaler un embouteillage / incident
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <span class="font-label-md text-label-md font-semibold text-on-surface">Détails de l'acheminement médical</span>
            <span class="text-primary font-label-sm text-label-sm font-semibold">Pochette scellée intacte</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-body-md text-on-surface">
            <div class="flex items-start gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-surface-container flex items-center justify-center shrink-0 text-primary mt-0.5"><span class="material-symbols-outlined text-[15px]">apartment</span></div>
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Lieu de retrait</span>
                    <span class="font-semibold text-on-surface">{{ $pharmacie?->nom }}@if($pharmacie?->quartier) ({{ $pharmacie->quartier }})@endif</span>
                    <span class="text-body-sm text-on-surface-variant">{{ $pharmacie?->adresse }}</span>
                </div>
            </div>
            <div class="flex items-start gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-surface-container flex items-center justify-center shrink-0 text-tertiary-container mt-0.5"><span class="material-symbols-outlined text-[15px]">pin_drop</span></div>
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Adresse de remise patient</span>
                    <span class="font-semibold text-on-surface">{{ $client?->name }}</span>
                    <span class="text-body-sm text-on-surface-variant">{{ $commande?->adresse_livraison }}</span>
                    @if ($commande?->notes)
                        <span class="text-body-sm text-on-surface-variant mt-1 whitespace-pre-line">{{ $commande->notes }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2 bg-surface-container-lowest p-3 rounded-lg">
            <span class="material-symbols-outlined text-primary text-[20px]">medical_services</span>
            <div class="flex flex-col">
                <span class="font-label-sm text-label-sm text-on-surface font-semibold">Sacoche sécurisée #SEC-{{ str_pad($commande?->id ?? 0, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="text-body-sm text-on-surface-variant">{{ $commande?->lignes->map(fn ($l) => $l->quantite.'× '.$l->nom_medicament)->implode(' · ') }}</span>
            </div>
        </div>
    </div>
</div>

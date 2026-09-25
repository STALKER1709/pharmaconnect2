@extends('layouts.app')

@section('titre', 'Suivi '.$commande->numero)

@php
    use App\Enums\CommandeStatut;

    $statut = $commande->statut;
    $livreur = $commande->livreur;
    $paiement = $commande->paiement;
    $ordre = [CommandeStatut::Confirmee, CommandeStatut::Prete, CommandeStatut::Assignee, CommandeStatut::EnLivraison, CommandeStatut::Livree];
    $indexCourant = array_search($statut, $ordre, true);
    if ($statut === CommandeStatut::EnAttente) { $indexCourant = -1; }
    $etapes = [
        ['Confirmée', 'Validée', $commande->confirmee_at, 'check'],
        ['Prête', 'Officine', $commande->prete_at, 'check'],
        ['Assignée', 'Coursier pris', $commande->assignee_at, 'check'],
        ['En livraison', 'En route', $commande->en_livraison_at, 'two_wheeler'],
        ['Livrée', 'Remise', $commande->livree_at, 'task_alt'],
    ];
    $progression = $indexCourant === false || $indexCourant < 0 ? 4 : min(100, (int) round(($indexCourant + 0.9) / 5 * 100));
    $estClient = auth()->user()->estClient();
    $peutConfirmer = $estClient && auth()->user()->can('confirmerReception', $commande);
    $telLivreur = $livreur?->user?->telephone ? preg_replace('/[^0-9+]/', '', $livreur->user->telephone) : null;
    $icones = \App\Support\IconesCarte::toutes();
    $lignesNotes = collect(preg_split('/\R/', (string) $commande->notes))->filter();
    $destinataire = $lignesNotes->first(fn ($l) => str_starts_with($l, 'Destinataire'));
    $instructions = $lignesNotes->reject(fn ($l) => str_starts_with($l, 'Destinataire'))->implode(' ');
@endphp

@section('contenu')
<div class="flex flex-col w-full"
     x-data="PharmaConnect.suivi({
         commandeId: {{ $commande->id }},
         statut: '{{ $statut->value }}',
         statutLabel: '{{ $statut->label() }}',
         statutLivraison: '{{ $commande->livraison?->statut->value ?? '' }}',
         estLivreur: {{ auth()->user()->estLivreur() ? 'true' : 'false' }},
         latitude: {{ $commande->pharmacie->latitude ?? 4.0511 }},
         longitude: {{ $commande->pharmacie->longitude ?? 9.7679 }},
         pharmacie: @js($commande->pharmacie->latitude ? ['lat' => (float) $commande->pharmacie->latitude, 'lng' => (float) $commande->pharmacie->longitude, 'nom' => $commande->pharmacie->nom] : null),
         arrivee: @js($commande->latitude_livraison ? ['lat' => (float) $commande->latitude_livraison, 'lng' => (float) $commande->longitude_livraison] : null),
         icones: @js($icones),
         csrf: '{{ csrf_token() }}',
         urls: {
             position: '{{ route('suivi.position', $commande) }}',
             partager: '{{ route('suivi.partager-position', $commande) }}',
             signalerPosition: '',
         },
     })"
     @beforeunload.window="destroy()">
    <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop w-full py-space-md">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-sm mb-space-md">
            <nav aria-label="Fil d'Ariane" class="flex items-center gap-2 font-label-md text-label-md text-on-surface-variant flex-wrap">
                <a class="hover:text-primary transition-colors" href="{{ route('accueil') }}">Accueil</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="{{ route('commandes.index') }}">Mes commandes</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <a class="text-on-surface font-semibold hover:text-primary" href="{{ route('commandes.show', $commande) }}">#{{ $commande->numero }}</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-semibold">Suivi en direct</span>
            </nav>
            <div class="inline-flex items-center gap-2 self-start md:self-auto px-3 py-1 rounded-full bg-surface-container-lowest delivery-shadow">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Position actualisée <span x-text="depuisMaj">en attente du coursier</span></span>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-space-sm mb-space-lg">
            <div>
                <h1 class="font-headline-xl-mobile text-headline-xl-mobile md:font-headline-xl md:text-headline-xl text-on-surface tracking-tight">Suivi de votre livraison en temps réel</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1 flex items-center gap-2 flex-wrap">
                    <span class="font-semibold text-on-surface">Commande #{{ $commande->numero }}</span>
                    <span>—</span>
                    <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-primary">local_pharmacy</span>{{ $commande->pharmacie->nom }}@if($commande->pharmacie->quartier) ({{ $commande->pharmacie->quartier }})@endif</span>
                    <span>→</span>
                    <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-tertiary">home_pin</span>{{ \Illuminate\Support\Str::limit($commande->adresse_livraison, 40) }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-[#dcfce9] text-[#14532d] font-label-md text-label-md flex items-center gap-1.5 font-semibold">
                    <span class="material-symbols-outlined text-[18px]">verified</span> <span x-text="statutLabel">{{ $statut->label() }}</span>
                </span>
            </div>
        </div>

        <!-- Étapes -->
        <div class="w-full bg-surface-container-lowest rounded-2xl p-space-md mb-space-xl delivery-shadow">
            @if (in_array($statut, [CommandeStatut::Annulee, CommandeStatut::Refusee], true))
                <div class="flex items-center gap-3 text-error">
                    <span class="material-symbols-outlined text-[28px]">cancel</span>
                    <div>
                        <p class="font-label-lg text-label-lg">Commande {{ mb_strtolower($statut->label()) }}</p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Aucune livraison n'est en cours pour cette commande.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-5 gap-2 relative">
                    @foreach ($etapes as $i => [$titre, $legende, $date, $icone])
                        @if ($indexCourant !== false && $i < $indexCourant || ($i === $indexCourant && $statut === CommandeStatut::Livree))
                            <div class="flex flex-col items-center text-center group">
                                <div class="w-10 h-10 rounded-full bg-[#dcfce9] text-primary flex items-center justify-center mb-2 delivery-shadow">
                                    <span class="material-symbols-outlined text-[20px] font-bold">check</span>
                                </div>
                                <span class="font-label-md text-label-md text-on-surface font-semibold">{{ $titre }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $legende }}@if($date) · {{ $date->format('H:i') }}@endif</span>
                            </div>
                        @elseif ($i === $indexCourant)
                            <div class="flex flex-col items-center text-center relative">
                                <div class="relative w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center mb-2 delivery-shadow">
                                    <span class="absolute -inset-1.5 rounded-full bg-primary/25 animate-pulse-ring"></span>
                                    <span class="material-symbols-outlined text-[20px]">{{ $icone === 'check' ? 'pending' : $icone }}</span>
                                </div>
                                <span class="font-label-md text-label-md text-primary font-bold">{{ $titre }}</span>
                                <span class="font-label-sm text-label-sm text-primary font-semibold">{{ $i === 3 ? 'Arrivée ~8-12 min' : ($date ? $legende.' · '.$date->format('H:i') : 'En cours') }}</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center text-center opacity-60">
                                <div class="w-10 h-10 rounded-full bg-surface-container text-outline flex items-center justify-center mb-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-outline"></span>
                                </div>
                                <span class="font-label-md text-label-md text-on-surface font-medium">{{ $titre }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $i === 4 ? 'Attente remise' : 'À venir' }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="w-full bg-surface-container h-1.5 rounded-full mt-4 overflow-hidden relative">
                    <div class="bg-primary h-full rounded-full transition-all duration-700" style="width: {{ $progression }}%;"></div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-start">
            <!-- Carte -->
            <div class="lg:col-span-7 flex flex-col gap-space-md">
                <div class="relative w-full h-[420px] md:h-[580px] bg-[#eef3ec] rounded-2xl overflow-hidden delivery-shadow select-none">
                    @include('partials.carte-douala')
                    <div id="carte-suivi" class="absolute inset-0 z-10 [&.leaflet-container]:bg-transparent"></div>
                    <div class="absolute top-4 right-4 flex flex-col gap-2 z-20">
                        <div class="flex flex-col bg-surface-container-lowest rounded-xl overflow-hidden delivery-shadow">
                            <button type="button" aria-label="Zoomer" @click="zoomer(1)" class="w-9 h-9 flex items-center justify-center text-on-surface hover:bg-surface-container transition-colors">
                                <span class="material-symbols-outlined text-[20px]">add</span>
                            </button>
                            <div class="w-full h-px bg-surface-container"></div>
                            <button type="button" aria-label="Dézoomer" @click="zoomer(-1)" class="w-9 h-9 flex items-center justify-center text-on-surface hover:bg-surface-container transition-colors">
                                <span class="material-symbols-outlined text-[20px]">remove</span>
                            </button>
                        </div>
                        <button type="button" @click="recentrer()" class="w-9 h-9 bg-surface-container-lowest text-primary rounded-xl delivery-shadow flex items-center justify-center hover:bg-[#dcfce9] transition-colors" title="Recentrer sur le coursier">
                            <span class="material-symbols-outlined text-[20px]">my_location</span>
                        </button>
                        <button type="button" @click="pleinEcran()" class="w-9 h-9 bg-surface-container-lowest text-on-surface-variant rounded-xl delivery-shadow flex items-center justify-center hover:bg-surface-container transition-colors" title="Plein écran">
                            <span class="material-symbols-outlined text-[20px]">fullscreen</span>
                        </button>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 sm:right-auto bg-surface-container-lowest/95 backdrop-blur-md p-3.5 rounded-2xl delivery-shadow max-w-md z-20">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#dcfce9] text-primary flex-shrink-0 flex items-center justify-center font-headline-sm">
                                <span class="material-symbols-outlined text-[22px]">timer</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-label-lg text-label-lg text-on-surface font-bold">
                                        @if ($statut === CommandeStatut::Livree)
                                            Colis remis {{ $commande->livree_at?->format('à H:i') }}
                                        @elseif ($commande->livraison?->duree_estimee_min)
                                            Arrivée estimée : {{ $commande->livraison->duree_estimee_min }} min
                                        @else
                                            Arrivée estimée : 25-35 min
                                        @endif
                                    </span>
                                    @if ($commande->livraison?->distance_km)
                                        <span class="font-label-sm text-label-sm text-primary font-semibold">{{ number_format($commande->livraison->distance_km, 1, ',', ' ') }} km</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-on-surface-variant font-body-sm text-body-sm truncate">
                                    <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">two_wheeler</span> <span x-text="statutLabel">{{ $statut->label() }}</span></span>
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-1 text-primary font-medium"><span class="material-symbols-outlined text-[14px]">ac_unit</span> Sac thermorégulé</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl p-space-md delivery-shadow flex items-start gap-space-md">
                    <div class="w-12 h-12 rounded-xl bg-[#dcfce9] text-primary flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[26px]">file_open</span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-headline-sm text-headline-sm text-on-surface">Protocole Chaîne du Froid &amp; Sécurité Officinale</h3>
                            <span class="px-2 py-0.5 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm font-semibold">Conforme ONPC</span>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant">Vos médicaments sont scellés sous pochette étanche avec témoin d'effraction. Le sac isotherme garantit une température de conservation stable entre 15°C et 25°C durant l'intégralité du trajet urbain.</p>
                    </div>
                </div>
            </div>

            <!-- Colonne droite -->
            <div class="lg:col-span-5 flex flex-col gap-space-md">
                <div class="bg-surface-container-lowest rounded-2xl p-space-md delivery-shadow">
                    <div class="flex items-start justify-between gap-2 pb-3 border-b border-surface-container">
                        <div>
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Référence</span>
                            <p class="font-headline-sm text-headline-sm text-on-surface">Commande #{{ $commande->numero }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Montant réglé</span>
                            <p class="font-currency-display text-currency-display text-primary whitespace-nowrap">{{ format_fcfa($commande->total) }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] text-primary">payment</span>
                            <div>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Mode de règlement</p>
                                <p class="font-label-md text-label-md text-on-surface">{{ $paiement?->operateur?->label() ?? 'Mobile Money' }}{{ $paiement?->statut?->value === 'reussi' ? ' ✓' : '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] text-primary">schedule</span>
                            <div>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Commandée le</p>
                                <p class="font-label-md text-label-md text-on-surface">{{ $commande->created_at->format('d/m à H\hi') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl p-space-md delivery-shadow">
                    <div class="flex items-center justify-between mb-space-sm">
                        <span class="font-label-md text-label-md text-on-surface-variant font-semibold">Votre coursier dédié</span>
                        @if ($livreur)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm font-semibold">
                                <span class="material-symbols-outlined text-[14px]">verified</span> Vérifié
                            </span>
                        @endif
                    </div>
                    @if ($livreur)
                        <div class="flex items-center gap-3.5 mb-space-md">
                            <div class="relative w-14 h-14 rounded-full overflow-hidden flex-shrink-0 bg-primary-container text-on-primary-container flex items-center justify-center font-headline-sm text-headline-sm">
                                {{ initiales($livreur->user?->name) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-headline-sm text-headline-sm text-on-surface truncate">{{ $livreur->user?->name }}</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1 mt-0.5">
                                    <span class="text-amber-500 font-bold">{{ number_format((float) $livreur->note_moyenne, 1) }} ★</span>
                                    <span>({{ $livreur->nb_avis }} avis)</span>
                                </p>
                                <p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5 truncate">
                                    {{ ucfirst($livreur->vehicule ?? 'Moto') }}@if($livreur->immatriculation) · <span class="font-medium text-on-surface">{{ $livreur->immatriculation }}</span>@endif
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2.5">
                            <a class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors font-semibold {{ $telLivreur ? '' : 'pointer-events-none opacity-50' }}" href="{{ $telLivreur ? 'tel:'.$telLivreur : '#' }}">
                                <span class="material-symbols-outlined text-[18px]">call</span>
                                <span>Appeler</span>
                            </a>
                            <form method="POST" action="{{ route('messagerie.demarrer', $livreur->user) }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-surface-container-lowest text-primary font-label-md text-label-md hover:bg-[#f0fdf6] transition-colors border border-primary/30 font-semibold">
                                    <span class="material-symbols-outlined text-[18px]">chat</span>
                                    <span>Message</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-container-low">
                            <span class="material-symbols-outlined text-[24px] text-outline">hourglass_top</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Un coursier certifié sera assigné dès que l'officine aura préparé votre sac scellé.</p>
                        </div>
                    @endif
                </div>

                <div class="bg-surface-container-lowest rounded-2xl p-space-md delivery-shadow">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-surface-container text-on-surface flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-[22px]">location_on</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-label-md text-label-md text-on-surface font-semibold">Adresse de livraison</span>
                                @if ($destinataire)<span class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ \Illuminate\Support\Str::before($destinataire, ' —') }}</span>@endif
                            </div>
                            <p class="font-body-md text-body-md text-on-surface mt-1 font-medium">{{ $commande->adresse_livraison }}</p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $commande->ville_livraison }}</p>
                            @if ($instructions)
                                <div class="mt-2.5 p-2 rounded-xl bg-[#f0fdf6] text-[#14532d] flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[16px] mt-0.5 flex-shrink-0">info</span>
                                    <p class="font-body-sm text-body-sm"><span class="font-semibold">Indication d'accès :</span> {{ $instructions }}</p>
                                </div>
                            @endif
                            @if ($estClient)
                                <button type="button" @click="partagerMaPosition()" class="mt-2 inline-flex items-center gap-1 font-label-sm text-label-sm text-primary hover:underline">
                                    <span class="material-symbols-outlined text-[16px]">my_location</span> Affiner ma position GPS
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl p-space-md delivery-shadow">
                    <div class="flex items-center justify-between mb-space-sm">
                        <span class="font-label-md text-label-md text-on-surface-variant font-semibold">Contenu du sac scellé ({{ $commande->lignes->sum('quantite') }} {{ \Illuminate\Support\Str::plural('article', $commande->lignes->sum('quantite')) }})</span>
                        <span class="font-label-sm text-label-sm text-primary font-mono font-semibold">Scellé #SEC-{{ str_pad($commande->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="space-y-2 font-body-sm text-body-sm">
                        @foreach ($commande->lignes as $ligne)
                            <div class="flex items-center justify-between py-1 {{ $loop->last ? '' : 'border-b border-surface-container' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="material-symbols-outlined text-[16px] text-primary">medication</span>
                                    <span class="text-on-surface font-medium truncate">{{ $ligne->nom_medicament }}</span>
                                </div>
                                <span class="text-on-surface-variant font-semibold whitespace-nowrap">x {{ $ligne->quantite }} {{ \Illuminate\Support\Str::plural('boîte', $ligne->quantite) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-space-sm">
                    @if ($peutConfirmer)
                        <form method="POST" action="{{ route('commandes.confirmer-reception', $commande) }}" onsubmit="return confirm('Avez-vous bien reçu le colis avec le scellé de sécurité intact ?')">
                            @csrf
                            <button type="submit" class="w-full py-3.5 px-space-md rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all delivery-shadow flex flex-col items-center justify-center text-center">
                                <span class="flex items-center gap-2 font-bold text-[15px]">
                                    <span class="material-symbols-outlined text-[20px]">task_alt</span>
                                    Confirmer la réception du colis
                                </span>
                                <span class="font-body-sm text-body-sm opacity-90 text-[11px] font-normal mt-0.5">À valider uniquement lorsque le coursier vous a remis le sac scellé</span>
                            </button>
                        </form>
                    @elseif ($statut === CommandeStatut::Livree)
                        <a href="{{ route('commandes.show', $commande) }}" class="w-full py-3.5 px-space-md rounded-xl bg-on-secondary-container text-on-primary font-label-lg text-label-lg transition-all delivery-shadow flex flex-col items-center justify-center text-center">
                            <span class="flex items-center gap-2 font-bold text-[15px]"><span class="material-symbols-outlined text-[20px]">check_circle</span> Réception validée avec succès</span>
                            <span class="font-body-sm text-body-sm opacity-90 text-[11px] font-normal mt-0.5">Laissez un avis sur l'officine et le coursier</span>
                        </a>
                    @endif
                    <div class="flex items-center justify-between pt-1">
                        <form method="POST" action="{{ route('messagerie.demarrer', $commande->pharmacie->user) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 font-label-sm text-label-sm text-tertiary hover:underline">
                                <span class="material-symbols-outlined text-[16px]">report_problem</span>
                                Signaler un retard ou problème
                            </button>
                        </form>
                        <span class="font-body-sm text-body-sm text-on-surface-variant text-[11px] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px] text-primary">lock</span> GPS crypté
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

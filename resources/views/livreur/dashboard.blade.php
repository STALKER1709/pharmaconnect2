@extends('layouts.livreur')

@section('titre', 'Missions en direct')

@php
    $utilisateur = auth()->user();
    $enLigne = $livreur->disponibilite !== 'hors_ligne';
    $mission = $actives->first();
@endphp

@section('contenu')
<div class="flex flex-col w-full">
    <div class="max-w-7xl mx-auto px-margin md:px-margin-desktop py-space-md w-full flex flex-col gap-space-lg">
        <!-- En-tête coursier -->
        <div class="w-full bg-surface-container-lowest rounded-2xl p-6 shadow-sm flex flex-col gap-space-md">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-14 h-14 rounded-2xl bg-secondary-container/40 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[32px]">two_wheeler</span>
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 {{ $enLigne ? 'bg-primary' : 'bg-outline' }} rounded-full ring-2 ring-surface-container-lowest"></span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-headline-md text-headline-md text-on-surface tracking-tight">Bonjour {{ $utilisateur->name }}</h1>
                            <span class="text-xl">👋</span>
                            <span class="inline-flex items-center gap-1 bg-surface-container px-2 py-0.5 rounded-full text-primary font-label-sm text-label-sm font-semibold">
                                <span class="material-symbols-outlined text-[14px] icon-fill">star</span> {{ number_format((float) $livreur->note_moyenne, 1, ',', ' ') }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-on-surface-variant font-label-md text-label-md">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">sports_motorsports</span>
                                {{ ucfirst($livreur->vehicule ?? 'moto') }}
                            </span>
                            @if ($livreur->immatriculation)
                                <span>•</span>
                                <span class="font-medium text-on-surface">{{ $livreur->immatriculation }}</span>
                            @endif
                            <span>•</span>
                            <span class="text-primary font-medium">Zone active : {{ $livreur->ville ?? 'Douala' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2 bg-surface-container-low px-3.5 py-2 rounded-xl">
                        <span class="relative flex h-2.5 w-2.5">
                            @if ($livreur->derniere_position_at)<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>@endif
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $livreur->derniere_position_at ? 'bg-primary' : 'bg-outline' }}"></span>
                        </span>
                        <div class="flex flex-col">
                            <span class="font-label-sm text-label-sm text-on-surface font-semibold flex items-center gap-1">{{ $livreur->derniere_position_at ? 'Position GPS active' : 'Position GPS en attente' }}</span>
                            <span class="text-[10px] text-on-surface-variant leading-none">{{ $livreur->derniere_position_at ? 'Synchro '.$livreur->derniere_position_at->diffForHumans() : 'Partagée pendant les courses' }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('livreur.disponibilite') }}" class="flex items-center gap-3 bg-secondary-container/20 px-4 py-2 rounded-xl">
                        @csrf
                        <div class="flex flex-col text-left">
                            <span class="font-label-sm text-label-sm font-bold text-on-secondary-container">Disponibilité</span>
                            <span class="text-[11px] text-on-surface-variant hidden sm:inline">{{ $livreur->ville ?? 'Douala' }}</span>
                        </div>
                        <button type="submit" role="switch" aria-checked="{{ $enLigne ? 'true' : 'false' }}" @disabled($livreur->disponibilite === 'en_course')
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out {{ $enLigne ? 'bg-primary' : 'bg-surface-variant' }} disabled:cursor-not-allowed">
                            <span class="{{ $enLigne ? 'translate-x-5' : 'translate-x-1' }} pointer-events-none inline-block h-5 w-5 transform rounded-full bg-on-primary shadow ring-0 transition duration-200 ease-in-out my-0.5"></span>
                        </button>
                        <span class="font-label-md text-label-md font-bold {{ $enLigne ? 'text-primary' : 'text-on-surface-variant' }}">{{ $livreur->disponibilite === 'en_course' ? 'En course' : ($enLigne ? 'En ligne' : 'Hors ligne') }}</span>
                    </form>
                </div>
            </div>

            @if ($livreur->statut !== 'actif')
                <div class="p-3 rounded-xl bg-tertiary-fixed text-on-tertiary-fixed font-body-md text-body-md flex items-center gap-2">
                    <span class="material-symbols-outlined">hourglass_top</span>
                    Votre compte coursier est en attente de validation par l'administration.
                </div>
            @endif

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-space-sm pt-2">
                <div class="bg-surface-container-low rounded-xl p-4 flex flex-col justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center justify-between text-on-surface-variant mb-1">
                        <span class="font-label-sm text-label-sm uppercase tracking-wider font-semibold">Gains du jour</span>
                        <span class="material-symbols-outlined text-primary text-[18px]">payments</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-primary tracking-tight whitespace-nowrap">{{ format_fcfa($gainsDuJour) }}</span>
                        <div class="flex items-center gap-1.5 mt-1 font-label-sm text-label-sm">
                            <span class="text-on-surface-variant">{{ $aujourdhui }} {{ \Illuminate\Support\Str::plural('course', $aujourdhui) }} aujourd'hui</span>
                        </div>
                    </div>
                </div>
                <div class="bg-surface-container-low rounded-xl p-4 flex flex-col justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center justify-between text-on-surface-variant mb-1">
                        <span class="font-label-sm text-label-sm uppercase tracking-wider font-semibold">Courses livrées</span>
                        <span class="material-symbols-outlined text-primary text-[18px]">savings</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-surface tracking-tight">{{ $totalLivrees }}</span>
                        <div class="flex items-center gap-1 mt-1 font-label-sm text-label-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-[14px] text-primary">verified</span>
                            <span>Paiement MoMo garanti</span>
                        </div>
                    </div>
                </div>
                <div class="bg-surface-container-low rounded-xl p-4 flex flex-col justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center justify-between text-on-surface-variant mb-1">
                        <span class="font-label-sm text-label-sm uppercase tracking-wider font-semibold">Distance du jour</span>
                        <span class="material-symbols-outlined text-primary text-[18px]">route</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-surface tracking-tight">{{ number_format($distanceDuJour, 1, ',', ' ') }} km</span>
                        <div class="flex items-center gap-1 mt-1 font-label-sm text-label-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-[14px] text-on-surface-variant">local_gas_station</span>
                            <span>Conso est. ~{{ number_format($distanceDuJour * 0.04, 1, ',', ' ') }} L (Super)</span>
                        </div>
                    </div>
                </div>
                <div class="bg-surface-container-low rounded-xl p-4 flex flex-col justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center justify-between text-on-surface-variant mb-1">
                        <span class="font-label-sm text-label-sm uppercase tracking-wider font-semibold">Note de service</span>
                        <span class="material-symbols-outlined text-primary text-[18px] icon-fill">stars</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-baseline gap-1">
                            <span class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-surface tracking-tight">{{ number_format((float) $livreur->note_moyenne, 1, ',', ' ') }}</span>
                            <span class="font-label-md text-label-md text-primary font-bold">/ 5.0</span>
                        </div>
                        <div class="flex items-center gap-1 mt-1 font-label-sm text-label-sm text-on-surface-variant truncate">
                            <span class="text-primary font-semibold">{{ $livreur->nb_avis }} avis</span>
                            <span>· Froid 100%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg w-full items-start">
            <!-- Mission en cours -->
            <div class="lg:col-span-8 flex flex-col gap-space-md">
                @if ($mission)
                    @include('livreur.partials.mission', ['livraison' => $mission])
                    @foreach ($actives->skip(1) as $autre)
                        <a href="{{ route('livreur.livraison', $autre) }}" class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm flex items-center justify-between gap-3 hover:bg-surface-container-low transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">pending_actions</span>
                                <div>
                                    <p class="font-label-lg text-label-lg text-on-surface">#{{ $autre->commande?->numero }} · {{ $autre->statut->label() }}</p>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $autre->commande?->pharmacie?->nom }} → {{ \Illuminate\Support\Str::limit($autre->commande?->adresse_livraison, 40) }}</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-outline">chevron_right</span>
                        </a>
                    @endforeach
                @else
                    <div class="bg-surface-container-lowest rounded-2xl shadow-sm p-10 flex flex-col items-center text-center gap-3">
                        <div class="w-16 h-16 rounded-2xl bg-secondary-container/40 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[34px]">radar</span>
                        </div>
                        <h2 class="font-headline-md text-headline-md text-on-surface">Aucune mission en cours</h2>
                        <p class="font-body-md text-body-md text-on-surface-variant max-w-md">{{ $enLigne ? 'Acceptez une course disponible dans la colonne de droite pour démarrer.' : 'Passez en ligne pour recevoir les courses des officines de Douala.' }}</p>
                    </div>
                @endif

                @if ($terminees->isNotEmpty())
                    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm flex flex-col gap-3" id="historique">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">history</span>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface">Historique des courses</h2>
                        </div>
                        <div class="divide-y divide-surface-container">
                            @foreach ($terminees as $course)
                                <div class="py-3 flex items-center justify-between gap-3 font-body-md text-body-md">
                                    <div>
                                        <p class="font-label-lg text-label-lg text-on-surface">#{{ $course->commande?->numero }}</p>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $course->livree_at?->format('d/m/Y H:i') ?? '—' }} · {{ \Illuminate\Support\Str::limit($course->commande?->adresse_livraison, 36) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-label-lg text-label-lg text-primary whitespace-nowrap">+{{ format_fcfa($course->commande?->frais_livraison) }}</p>
                                        <p class="font-label-sm text-label-sm {{ $course->statut === \App\Enums\LivraisonStatut::Livree ? 'text-on-surface-variant' : 'text-error' }}">{{ $course->statut->label() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{ $terminees->links('partials.pagination') }}
                    </div>
                @endif
            </div>

            <!-- Courses disponibles -->
            <div class="lg:col-span-4 flex flex-col gap-space-md" x-data="{ son: true, refusees: [] }">
                <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm flex flex-col gap-space-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[22px]">radar</span>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface tracking-tight">Courses disponibles</h2>
                        </div>
                        @if ($disponibles->isNotEmpty())
                            <span class="bg-secondary-container text-on-secondary-container px-2 py-0.5 rounded-full font-label-sm text-label-sm font-bold">{{ $disponibles->count() }} {{ $disponibles->count() > 1 ? 'nouvelles' : 'nouvelle' }}</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <button type="button" @click="son = ! son" class="flex items-center gap-1.5 text-on-surface-variant hover:text-primary font-label-sm text-label-sm transition-colors">
                            <span class="material-symbols-outlined text-primary text-[16px]" x-text="son ? 'volume_up' : 'volume_off'">volume_up</span>
                            <span x-text="son ? 'Alertes sonores actives' : 'Alertes sonores en sourdine'">Alertes sonores actives</span>
                        </button>
                        <div class="flex items-center gap-1 text-on-surface-variant font-label-sm text-label-sm">
                            <span class="material-symbols-outlined text-[15px]">filter_list</span>
                            <span>{{ $livreur->ville ?? 'Douala' }}</span>
                        </div>
                    </div>
                    @forelse ($disponibles as $dispo)
                        @php $c = $dispo->commande; $minutes = (int) $dispo->created_at->diffInMinutes(); @endphp
                        <div class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-3 transition-all hover:bg-surface-container duration-200" x-show="! refusees.includes({{ $dispo->id }})">
                            <div class="flex items-center justify-between">
                                @if ($loop->first && $minutes < 30)
                                    <span class="inline-flex items-center gap-1 bg-tertiary-container text-on-tertiary-container font-label-sm text-label-sm px-2.5 py-0.5 rounded-full font-bold">
                                        <span class="material-symbols-outlined text-[14px]">bolt</span> Urgent · Nouvelle
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-surface-container text-on-surface font-label-sm text-label-sm px-2.5 py-0.5 rounded-full font-semibold">
                                        <span class="material-symbols-outlined text-[14px] text-primary">description</span> {{ $c?->statut->label() }}
                                    </span>
                                @endif
                                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ ucfirst($dispo->created_at->diffForHumans()) }}</span>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-primary text-[16px] mt-0.5">local_pharmacy</span>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-sm text-label-sm font-semibold text-on-surface truncate">{{ $c?->pharmacie?->nom }}</span>
                                        <span class="text-[11px] text-on-surface-variant">{{ collect([$c?->pharmacie?->quartier, $c?->pharmacie?->adresse])->filter()->unique()->implode(', ') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-tertiary-container text-[16px] mt-0.5">home</span>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-sm text-label-sm font-semibold text-on-surface truncate">{{ $c?->adresse_livraison }}</span>
                                        <span class="text-[11px] text-on-surface-variant">{{ \Illuminate\Support\Str::limit($c?->ville_livraison, 40) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex items-center gap-1 text-on-surface-variant font-label-sm text-label-sm">
                                    <span class="material-symbols-outlined text-[15px]">straighten</span>
                                    <span>{{ $dispo->distance_km ? number_format($dispo->distance_km, 1, ',', ' ').' km' : 'Distance GPS' }}@if($dispo->duree_estimee_min) • ~{{ $dispo->duree_estimee_min }} min @endif</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-headline-sm text-headline-sm text-primary font-bold whitespace-nowrap">{{ format_fcfa($c?->frais_livraison) }}</span>
                                    <span class="block text-[10px] text-on-surface-variant">MoMo Garanti</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 pt-1">
                                <form method="POST" action="{{ route('livreur.livraisons.accepter', $dispo) }}" class="col-span-2 flex">
                                    @csrf
                                    <button class="w-full py-2.5 px-3 rounded-xl bg-primary text-on-primary font-label-md text-label-md font-bold transition-transform hover:-translate-y-0.5 shadow-sm text-center disabled:opacity-60" @disabled($livreur->statut !== 'actif')>Accepter</button>
                                </form>
                                <button type="button" @click="refusees.push({{ $dispo->id }})" class="py-2.5 px-3 rounded-xl bg-surface-container-lowest text-on-surface-variant hover:text-error hover:bg-error-container/20 font-label-md text-label-md text-center transition-colors">Refuser</button>
                            </div>
                        </div>
                    @empty
                        <div class="bg-surface-container-low rounded-xl p-5 text-center font-body-sm text-body-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-[28px] text-outline block mb-1">hourglass_empty</span>
                            Aucune course disponible pour le moment. Les nouvelles commandes prêtes apparaîtront ici.
                        </div>
                    @endforelse
                </div>
                <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container/40 flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-[24px]">verified_user</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-label-md text-label-md font-bold text-on-surface">Protocole Froid &amp; Sécurité</span>
                        <span class="text-body-sm text-on-surface-variant">Vérifiez toujours le thermo-indicateur avant d'ouvrir le sac scellé devant le patient.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full bg-surface-container-lowest rounded-2xl p-5 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[20px]">policy</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-label-md text-label-md font-semibold text-on-surface">Transport Médicalisé Homologué ONPC / MINSANTE</span>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Coursier vérifié par l'administration PharmaConnect pour la région du Littoral (Douala).</span>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="text-[12px] text-on-surface-variant font-medium">Astreinte Dispatch 24/7 :</span>
                <a class="font-label-md text-label-md font-bold text-primary hover:underline flex items-center gap-1" href="tel:+237670000000">
                    <span class="material-symbols-outlined text-[16px]">support_agent</span>
                    +237 670 00 00 00
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

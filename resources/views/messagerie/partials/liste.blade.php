{{-- Colonne « Discussions » — maquette messagerie_en_direct --}}
@php
    $actuelle = $actuelle ?? null;
    $nonLusTotal = $conversations->sum('non_lus_count');
    $cartes = $conversations->map(fn ($c) => ['conversation' => $c, 'carte' => $c->carteInterlocuteur($monId)]);
    $nbOfficines = $cartes->where('carte.type', 'Officine')->count();
    $nbCoursiers = $cartes->where('carte.type', 'Coursier')->count();
    $nbPatients = $cartes->where('carte.type', 'Patient')->count();
@endphp
<aside class="w-full lg:w-[380px] xl:w-[400px] flex-shrink-0 flex flex-col bg-surface-container-low/40 {{ $actuelle ? 'hidden lg:flex' : 'flex' }} min-h-0"
       x-data="{ filtre: 'Toutes', recherche: '' }">
    <div class="p-space-md bg-surface-container-lowest space-y-space-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-space-xs">
                <h1 class="font-headline-sm text-headline-sm text-on-surface font-bold">Discussions</h1>
                @if ($nonLusTotal > 0)
                    <span class="bg-tertiary-container text-on-tertiary-container font-label-sm text-label-sm px-2 py-0.5 rounded-full font-bold">{{ $nonLusTotal }} non {{ $nonLusTotal > 1 ? 'lus' : 'lu' }}</span>
                @endif
            </div>
            <a href="{{ auth()->user()->estClient() ? route('public.pharmacies') : route('dashboard') }}" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center hover:bg-surface-container-high text-primary transition-all" title="Nouvelle consultation rapide">
                <span class="material-symbols-outlined text-[20px]">edit_square</span>
            </a>
        </div>
        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
            <input x-model="recherche" class="w-full pl-9 pr-4 py-2 bg-surface-container-lowest rounded-xl border-0 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all" placeholder="Rechercher une officine, coursier..." type="text"/>
        </div>
        <div class="flex items-center gap-space-xs pt-1 overflow-x-auto">
            @foreach (array_filter(['Toutes' => $conversations->count(), 'Officine' => $nbOfficines, 'Coursier' => $nbCoursiers, 'Patient' => $nbPatients], fn ($n, $k) => $k === 'Toutes' || $n > 0, ARRAY_FILTER_USE_BOTH) as $type => $nombre)
                <button type="button" @click="filtre = '{{ $type }}'"
                        :class="filtre === '{{ $type }}' ? 'bg-primary text-on-primary shadow-sm font-semibold' : 'bg-surface-container hover:bg-surface-container-high text-on-surface-variant'"
                        class="px-3 py-1 rounded-full font-label-sm text-label-sm transition-colors whitespace-nowrap">
                    {{ ['Toutes' => 'Toutes', 'Officine' => 'Officines', 'Coursier' => 'Coursiers', 'Patient' => 'Patients'][$type] }} ({{ $nombre }})
                </button>
            @endforeach
        </div>
    </div>
    <div class="flex-1 overflow-y-auto p-space-xs space-y-1">
        @forelse ($cartes as ['conversation' => $c, 'carte' => $carte])
            @php
                $dernier = $c->messages->first();
                $active = $actuelle?->id === $c->id;
                $nonLus = (int) $c->non_lus_count;
                $date = $dernier?->created_at ?? $c->dernier_message_at ?? $c->created_at;
                $heure = $date?->isToday() ? $date->format('H:i') : ($date?->isYesterday() ? 'Hier' : $date?->format('d/m'));
            @endphp
            <a href="{{ route('messagerie.show', $c) }}"
               x-show="(filtre === 'Toutes' || filtre === @js($carte['type'])) && @js(mb_strtolower($carte['nom'].' '.$carte['sous_titre'])).includes(recherche.toLowerCase())"
               class="relative p-space-sm rounded-xl flex items-start gap-space-sm transition-all {{ $active ? 'bg-surface-container-lowest shadow-sm hover:shadow-md' : 'hover:bg-surface-container-lowest/80' }}">
                @if ($active)
                    <div class="absolute left-0 top-3 bottom-3 w-1.5 bg-primary rounded-r-full"></div>
                @endif
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl {{ $active ? 'bg-surface-container-highest text-primary shadow-inner' : 'bg-surface-container text-on-surface-variant' }} flex items-center justify-center">
                        <span class="material-symbols-outlined text-[26px]">{{ $carte['icone'] }}</span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1 mb-0.5">
                        <div class="flex items-center gap-1 min-w-0">
                            <h2 class="font-label-lg text-label-lg text-on-surface {{ $active || $nonLus ? 'font-bold' : 'font-medium' }} truncate">{{ $carte['nom'] }}</h2>
                            @if ($carte['type'] === 'Officine')
                                <span class="material-symbols-outlined text-[14px] text-primary flex-shrink-0" title="Agréée ONPC">verified</span>
                            @else
                                <span class="font-label-sm text-label-sm bg-surface-container px-1.5 rounded text-on-surface-variant font-medium">{{ $carte['type'] }}</span>
                            @endif
                        </div>
                        <span class="font-label-sm text-label-sm flex-shrink-0 {{ $active || $nonLus ? 'text-primary font-semibold' : 'text-outline' }}">{{ $heure }}</span>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-1 mb-1">{{ $carte['sous_titre'] }}</p>
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-body-sm text-body-sm truncate flex items-center gap-1 {{ $nonLus ? 'text-on-surface font-bold' : ($active ? 'text-on-surface font-semibold' : 'text-outline') }}">
                            @if ($dernier && $dernier->expediteur_id === $monId)
                                <span class="material-symbols-outlined text-[15px] {{ $dernier->lu_at ? 'text-primary' : '' }}">done_all</span>
                            @endif
                            {{ $dernier ? \Illuminate\Support\Str::limit($dernier->contenu, 40) : 'Nouvelle conversation' }}
                        </p>
                        @if ($nonLus > 0)
                            <span class="w-5 h-5 rounded-full bg-primary text-on-primary font-label-sm text-label-sm flex items-center justify-center font-bold flex-shrink-0">{{ $nonLus }}</span>
                        @elseif ($active)
                            <span class="inline-block w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="p-space-lg text-center">
                <span class="material-symbols-outlined text-[36px] text-outline">forum</span>
                <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">Aucune discussion pour le moment. Contactez une officine depuis sa fiche ou depuis une commande.</p>
            </div>
        @endforelse
    </div>
    <a href="{{ route('public.pharmacies') }}" class="p-space-sm bg-surface-container/60 m-space-xs rounded-xl flex items-center gap-space-sm hover:bg-surface-container transition-colors">
        <div class="w-9 h-9 rounded-lg bg-surface-container-lowest flex items-center justify-center text-primary shadow-sm flex-shrink-0">
            <span class="material-symbols-outlined text-[20px]">shield_with_heart</span>
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-label-sm text-label-sm text-on-surface font-bold truncate">Pharmacies de garde Douala</p>
            <p class="font-body-sm text-body-sm text-on-surface-variant text-[11px] truncate">{{ \App\Models\Pharmacie::where('statut', 'actif')->count() }} officines partenaires connectées</p>
        </div>
        <span class="material-symbols-outlined text-outline text-[16px]">chevron_right</span>
    </a>
</aside>

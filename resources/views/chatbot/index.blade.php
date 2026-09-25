@extends(layout_espace())

@section('titre', 'PharmaBot — Conseils santé')

@php
    $historique = $messages->map(fn ($m) => ['role' => $m->role, 'contenu' => $m->message, 'heure' => $m->created_at->toIso8601String()]);
    $suggestions = ['🌡️ J\'ai de la fièvre', '🤧 Médicament pour la toux', '💊 Posologie paracétamol', '🏥 Pharmacies de garde ce soir', '👶 Sirop pour nourrisson'];
    $espacePro = ! auth()->user()->estClient();
@endphp

@section('contenu')
<div class="flex flex-col w-full {{ $espacePro ? 'py-space-md' : '' }}">
    <div class="w-full bg-surface-container-lowest shadow-sm mb-space-lg {{ $espacePro ? 'rounded-2xl' : '' }}">
        <div class="max-w-[1280px] mx-auto px-margin md:px-margin-desktop py-space-sm flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs">
            <nav class="flex items-center gap-2 text-on-surface-variant font-label-md text-label-md">
                <a class="hover:text-primary transition-colors" href="{{ route('accueil') }}">Accueil</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="{{ route('public.medicaments') }}">Conseils santé</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-label-lg text-label-lg">PharmaBot IA</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-space-md py-1 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm w-fit">
                <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                <span>Assistant Santé Certifié · Développé avec des pharmaciens du Cameroun (ONPC)</span>
            </div>
        </div>
    </div>

    <div class="max-w-[1280px] mx-auto w-full {{ $espacePro ? '' : 'px-margin md:px-margin-desktop' }} pb-space-xl">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-desktop items-start">
            <!-- Conversation -->
            <div class="lg:col-span-8 flex flex-col bg-surface-container-lowest rounded-2xl overflow-hidden"
                 x-data="PharmaConnect.chatbot({ historique: @js($historique), suggestions: @js($suggestions) })">
                <div class="px-space-md md:px-space-lg py-space-md bg-surface-container-lowest flex items-center justify-between gap-space-md shadow-sm">
                    <div class="flex items-center gap-space-md min-w-0">
                        <div class="relative w-12 h-12 rounded-xl bg-primary flex items-center justify-center flex-shrink-0 text-on-primary shadow-sm">
                            <span class="material-symbols-outlined text-[26px]">smart_toy</span>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-secondary-fixed rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-[12px] text-on-secondary-fixed font-bold">add</span>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h1 class="font-headline-sm text-headline-sm text-on-surface truncate">PharmaBot — Conseils santé</h1>
                                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">v2.4 CAM</span>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant truncate">Orientation officinale &amp; posologique en temps réel · Réponse 24/7</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-space-xs flex-shrink-0">
                        <button class="hidden sm:inline-flex items-center gap-1.5 px-space-md py-2 rounded-xl bg-surface-container-lowest text-on-surface font-label-md text-label-md hover:bg-surface-container transition-colors" title="Consulter l'historique" type="button" @click="defiler()">
                            <span class="material-symbols-outlined text-[18px]">history</span>
                            <span>Historique</span>
                        </button>
                        <button class="inline-flex items-center gap-1.5 px-space-md py-2 rounded-xl bg-surface-container-low text-primary font-label-md text-label-md hover:bg-primary-fixed transition-colors" title="Nouvelle consultation" type="button" @click="nouvelle()">
                            <span class="material-symbols-outlined text-[18px]">refresh</span>
                            <span class="hidden md:inline">Nouvelle conversation</span>
                        </button>
                    </div>
                </div>

                <div class="p-space-md md:p-space-lg space-y-space-lg max-h-[640px] min-h-[420px] overflow-y-auto bg-[#f9fbf9]" id="chatbot-messages">
                    <div class="flex items-center justify-center">
                        <div class="px-3 py-1 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm shadow-sm">
                            Aujourd'hui, {{ now()->format('H:i') }}
                        </div>
                    </div>
                    <!-- Accueil -->
                    <div class="flex items-start gap-3 max-w-2xl">
                        <div class="w-9 h-9 rounded-xl bg-primary text-on-primary flex items-center justify-center flex-shrink-0 shadow-sm mt-1">
                            <span class="material-symbols-outlined text-[20px]">medical_services</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <div class="bg-surface-container-lowest p-space-md rounded-2xl rounded-tl-none shadow-sm text-on-surface">
                                <p class="font-body-md text-body-md leading-relaxed">
                                    Bonjour {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }} ! Je suis <strong class="text-primary font-headline-sm text-[15px]">PharmaBot</strong>, votre assistant santé connecté aux officines partenaires de PharmaConnect Cameroun.
                                </p>
                                <p class="font-body-md text-body-md leading-relaxed mt-2 text-on-surface-variant">
                                    Je peux vous orienter sur la posologie usuelle, les précautions d'emploi, identifier des pharmacies de garde disponibles à Douala ou vous guider sur vos symptômes bénins.
                                </p>
                            </div>
                            <span class="font-label-sm text-label-sm text-on-surface-variant px-1">{{ now()->format('H:i') }} · Vérification pharmaceutique active</span>
                        </div>
                    </div>
                    <div class="pl-12">
                        <p class="font-label-sm text-label-sm text-on-surface-variant mb-2">Suggestions rapides :</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="suggestion in suggestions" :key="suggestion">
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-container-lowest hover:bg-[#dcfce9] text-on-surface hover:text-primary transition-all shadow-sm font-label-md text-label-md" type="button" @click="envoyer(suggestion)">
                                    <span x-text="suggestion"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <template x-for="(r, i) in reponses" :key="i">
                        <div>
                            <template x-if="r.role === 'user'">
                                <div class="flex items-start justify-end gap-3 max-w-2xl ml-auto">
                                    <div class="flex flex-col items-end gap-1.5">
                                        <div class="bg-primary text-on-primary p-space-md rounded-2xl rounded-tr-none shadow-sm">
                                            <p class="font-body-md text-body-md leading-relaxed whitespace-pre-line" x-text="r.contenu"></p>
                                        </div>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant px-1" x-text="(r.heure ? new Date(r.heure).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) + ' · ' : '') + 'Vous'"></span>
                                    </div>
                                    <div class="w-9 h-9 rounded-xl bg-surface-container-high text-on-surface flex items-center justify-center flex-shrink-0 shadow-sm mt-1">
                                        <span class="material-symbols-outlined text-[20px]">person</span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="r.role !== 'user'">
                                <div class="flex items-start gap-3 max-w-2xl">
                                    <div class="w-9 h-9 rounded-xl bg-primary text-on-primary flex items-center justify-center flex-shrink-0 shadow-sm mt-1">
                                        <span class="material-symbols-outlined text-[20px]">smart_toy</span>
                                    </div>
                                    <div class="flex flex-col gap-2 w-full">
                                        <div class="bg-surface-container-lowest p-space-md rounded-2xl rounded-tl-none shadow-sm text-on-surface">
                                            <p class="font-body-md text-body-md leading-relaxed whitespace-pre-line" x-text="r.contenu"></p>
                                        </div>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant px-1" x-text="(r.heure ? new Date(r.heure).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) + ' · ' : '') + 'Réf. Guide Thérapeutique National Cameroun'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div class="flex items-center gap-2 pl-12 text-on-surface-variant" x-show="enCours" x-cloak>
                        <span class="font-body-sm text-body-sm">PharmaBot rédige une réponse</span>
                        <div class="flex items-center gap-1 bg-surface-container px-2.5 py-1.5 rounded-full shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-bounce"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-bounce [animation-delay:0.4s]"></span>
                        </div>
                    </div>
                </div>

                <div class="p-space-md md:p-space-lg bg-surface-container-lowest shadow-[0_-4px_16px_rgba(0,0,0,0.02)]">
                    <form class="flex flex-col gap-2" @submit.prevent="envoyer()">
                        <div class="flex items-center gap-2 bg-surface-container-low rounded-2xl p-2 focus-within:bg-surface-container-lowest shadow-sm">
                            <a href="{{ auth()->user()->estClient() ? route('messagerie.index') : '#' }}" class="p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-xl transition-all" title="Envoyer une ordonnance à une officine">
                                <span class="material-symbols-outlined text-[22px]">attach_file</span>
                            </a>
                            <input x-model="message" :disabled="enCours" class="w-full bg-transparent border-0 focus:ring-0 py-2.5 px-2 text-on-surface placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none" placeholder="Posez votre question… (ex: posologie, contre-indications, officine de garde)" type="text" autocomplete="off"/>
                            <button class="inline-flex items-center justify-center gap-2 px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all flex-shrink-0 shadow-md disabled:opacity-60" type="submit" :disabled="enCours || ! message.trim()">
                                <span>Envoyer</span>
                                <span class="material-symbols-outlined text-[18px]">send</span>
                            </button>
                        </div>
                        <div class="px-2 py-1 flex items-center gap-2 text-on-surface-variant">
                            <span class="material-symbols-outlined text-tertiary text-[16px] flex-shrink-0">info</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">
                                <strong>Avertissement :</strong> PharmaBot est un outil d'orientation algorithmique et ne remplace pas une consultation médicale. En cas d'urgence absolue, composez immédiatement le <strong>119 (SAMU)</strong> ou le <strong>8100 (Douala Urgences)</strong>.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Barre latérale -->
            <div class="lg:col-span-4 flex flex-col gap-space-lg">
                <div class="bg-surface-container-lowest rounded-2xl p-space-lg">
                    <div class="flex items-center justify-between mb-space-md">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[22px]">local_pharmacy</span>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface">Gardes à proximité</h2>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-[#dcfce9] text-[#14532d] font-label-sm text-label-sm">Ouvertes</span>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">Secteur sélectionné : Douala (toutes zones)</p>
                    <div class="space-y-space-sm">
                        @forelse ($pharmaciesOuvertes as $p)
                            @php $tel = $p->user?->telephone ? preg_replace('/[^0-9+]/', '', $p->user->telephone) : null; $h = $p->horaireDuJour(now()->dayOfWeek); @endphp
                            <div class="p-3 bg-surface-container-low rounded-xl flex items-start justify-between gap-3">
                                <a href="{{ route('public.pharmacie', $p) }}" class="min-w-0">
                                    <p class="font-label-lg text-label-lg text-on-surface truncate">{{ $p->nom }}</p>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ collect([$p->adresse, $p->quartier])->filter()->unique()->implode(', ') }}</p>
                                    <p class="font-label-sm text-label-sm text-primary mt-1">{{ $h?->estContinu() ? 'Garde 24h/24' : 'Ouvert jusqu\'à '.$h?->fermeture() }}</p>
                                </a>
                                <a class="p-2 rounded-lg bg-surface-container-lowest text-primary hover:bg-primary hover:text-on-primary transition-colors shadow-sm" href="{{ $tel ? 'tel:'.$tel : route('public.pharmacie', $p) }}" title="Appeler l'officine">
                                    <span class="material-symbols-outlined text-[20px]">call</span>
                                </a>
                            </div>
                        @empty
                            <div class="p-3 bg-surface-container-low rounded-xl font-body-sm text-body-sm text-on-surface-variant">Aucune officine partenaire n'est ouverte en ce moment. En cas d'urgence, appelez le 119.</div>
                        @endforelse
                    </div>
                    <a class="mt-space-md w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-surface-container-high hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors" href="{{ route('public.pharmacies') }}">
                        <span>Consulter la carte complète des gardes</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl p-space-lg">
                    <div class="flex items-center gap-2 mb-space-md">
                        <span class="material-symbols-outlined text-error text-[22px]">emergency</span>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Urgences médicales</h2>
                    </div>
                    <div class="space-y-3 font-body-sm text-body-sm text-on-surface-variant">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-surface-container-low">
                            <div><p class="font-label-lg text-label-lg text-on-surface">SAMU Cameroun</p><p>Urgences vitales réanimation</p></div>
                            <a class="px-3 py-1.5 rounded-lg bg-error text-on-error font-currency-display text-sm tracking-wide" href="tel:119">119</a>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-surface-container-low">
                            <div><p class="font-label-lg text-label-lg text-on-surface">Hôpital Général Douala</p><p>Standard Urgences 24/7</p></div>
                            <a class="px-3 py-1.5 rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md shadow-sm" href="tel:+237233370140">Appeler</a>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-surface-container-low">
                            <div><p class="font-label-lg text-label-lg text-on-surface">Centre Anti-Poisons</p><p>Intoxications &amp; surdosages</p></div>
                            <a class="px-3 py-1.5 rounded-lg bg-surface-container-lowest text-on-surface font-currency-display text-sm shadow-sm" href="tel:1505">1505</a>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl p-space-lg relative overflow-hidden">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-xl bg-[#dcfce9] text-primary flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-[28px]">clinical_notes</span>
                        </div>
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface">Besoin d'un pharmacien réel ?</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Un doute sur votre traitement ? Nos pharmaciens inscrits à l'ONPC répondent via la messagerie sécurisée.</p>
                        </div>
                    </div>
                    <div class="mt-space-md pt-space-sm flex gap-2">
                        <a class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-[#dcfce9] text-[#14532d] font-label-md text-label-md hover:bg-primary-fixed transition-colors" href="{{ route('messagerie.index') }}">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
                            <span>Parler à un pharmacien</span>
                        </a>
                    </div>
                </div>

                <div class="px-space-md py-3 rounded-xl bg-surface-container-low flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-[24px]">verified_user</span>
                    <p class="font-body-sm text-body-sm text-on-surface-variant leading-tight">Plateforme conforme aux standards de télésanté de l'<strong>Ordre National des Pharmaciens du Cameroun</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

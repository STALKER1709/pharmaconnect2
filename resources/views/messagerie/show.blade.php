@extends(layout_espace())

@section('titre', 'Messagerie')
@section('classe_main', 'w-full pt-20 bg-background')

@php
    $carte = $conversation->carteInterlocuteur($monId);
    $commande = $conversation->commande;
    $telephone = $carte['user']?->telephone ? preg_replace('/[^0-9+]/', '', $carte['user']->telephone) : null;
@endphp

@section('contenu')
<div class="flex flex-col w-full">
    <div class="max-w-[1280px] w-full mx-auto {{ auth()->user()->estPharmacie() || auth()->user()->estAdmin() ? 'py-space-md' : 'px-gutter md:px-gutter-desktop py-space-sm md:py-space-md' }}">
        @include('messagerie.partials.cadre', ['fil' => $carte['nom']])
        <div class="w-full bg-surface-container-lowest rounded-2xl shadow-sm flex flex-col lg:flex-row h-[calc(100vh-165px)] min-h-[560px] lg:min-h-[640px] overflow-hidden">
            @include('messagerie.partials.liste', ['actuelle' => $conversation])

            <section class="flex-1 flex flex-col bg-surface-bright min-w-0 relative"
                 x-data="PharmaConnect.messagerie({
                     conversationId: {{ $conversation->id }},
                     monId: {{ $monId }},
                     csrf: '{{ csrf_token() }}',
                     messages: @js($messages->map(fn ($m) => [
                         'id' => $m->id,
                         'expediteur_id' => $m->expediteur_id,
                         'contenu' => $m->contenu,
                         'created_at' => $m->created_at->toIso8601String(),
                     ])),
                 })">
                <!-- En-tête de la conversation -->
                <div class="px-space-md py-space-sm bg-surface-container-lowest flex flex-wrap items-center justify-between gap-space-sm z-10 shadow-sm">
                    <div class="flex items-center gap-space-sm min-w-0">
                        <a href="{{ route('messagerie.index') }}" class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container" aria-label="Retour">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </a>
                        <div class="relative flex-shrink-0">
                            <div class="w-11 h-11 rounded-xl bg-surface-container-highest flex items-center justify-center text-primary font-bold shadow-inner">
                                <span class="material-symbols-outlined text-[24px]">{{ $carte['icone'] }}</span>
                            </div>
                            <span x-show="enLigne.length > 0" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-primary rounded-full ring-2 ring-surface-container-lowest"></span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-space-xs">
                                <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold truncate">{{ $carte['nom'] }}</h2>
                                <span x-show="enLigne.length > 0" x-cloak class="font-label-sm text-label-sm bg-secondary-container text-on-secondary-container px-2 py-0.5 rounded-full font-bold flex items-center gap-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    En ligne
                                </span>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant truncate">
                                {{ $carte['type'] === 'Officine' ? ($carte['user']?->name.' (Pharmacien titulaire)') : $carte['sous_titre'] }} • <span class="text-primary font-medium">{{ $carte['type'] === 'Officine' ? 'Répond en ~3 min' : $carte['type'] }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-space-xs">
                        @if ($commande)
                            <a href="{{ auth()->user()->estClient() ? route('commandes.show', $commande) : (auth()->user()->estPharmacie() ? route('pharmacie.commandes.show', $commande) : '#') }}" class="hidden md:flex items-center gap-1.5 bg-surface-container hover:bg-surface-container-high px-space-sm py-1.5 rounded-xl text-on-surface font-label-sm text-label-sm transition-all shadow-sm">
                                <span class="material-symbols-outlined text-primary text-[18px]">receipt_long</span>
                                <span class="font-bold">#{{ $commande->numero }}</span>
                                <span class="text-on-surface-variant">•</span>
                                <span class="text-primary font-bold">{{ format_fcfa($commande->total) }}</span>
                            </a>
                        @endif
                        @if ($telephone)
                            <a class="w-9 h-9 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface flex items-center justify-center transition-colors" href="tel:{{ $telephone }}" title="Appeler">
                                <span class="material-symbols-outlined text-[18px]">call</span>
                            </a>
                        @endif
                        @if ($commande && auth()->user()->estClient())
                            <a class="w-9 h-9 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface flex items-center justify-center transition-colors" href="{{ route('suivi.show', $commande) }}" title="Suivre la livraison">
                                <span class="material-symbols-outlined text-[18px]">pin_drop</span>
                            </a>
                        @endif
                        @if ($carte['type'] === 'Officine' && $carte['user']?->pharmacie)
                            <a class="w-9 h-9 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface flex items-center justify-center transition-colors" href="{{ route('public.pharmacie', $carte['user']->pharmacie) }}" title="Fiche de l'officine">
                                <span class="material-symbols-outlined text-[18px]">description</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Fil de discussion -->
                <div id="fil-messages" class="flex-1 overflow-y-auto px-space-md py-space-md space-y-space-md bg-surface-bright">
                    <div class="max-w-xl mx-auto bg-surface-container-lowest/90 px-space-md py-space-xs rounded-xl shadow-sm text-center flex items-center justify-center gap-space-xs text-on-surface-variant">
                        <span class="material-symbols-outlined text-primary text-[16px] flex-shrink-0">lock</span>
                        <span class="font-body-sm text-body-sm text-[12px]">Échange médical sécurisé conforme à l'Ordre National des Pharmaciens du Cameroun (ONPC)</span>
                    </div>
                    <template x-if="messages.length === 0">
                        <p class="text-center font-body-sm text-body-sm text-on-surface-variant py-space-lg">Démarrez la conversation : posez votre question à {{ $carte['nom'] }}.</p>
                    </template>
                    <template x-for="(m, i) in messages" :key="m.id">
                        <div class="space-y-space-md">
                            <div class="flex items-center justify-center" x-show="i === 0 || new Date(m.created_at).toDateString() !== new Date(messages[i - 1].created_at).toDateString()">
                                <span class="px-3 py-1 rounded-full bg-surface-container font-label-sm text-label-sm text-on-surface-variant shadow-sm"
                                      x-text="(new Date(m.created_at).toDateString() === new Date().toDateString() ? 'Aujourd\'hui' : new Date(m.created_at).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' })) + ', ' + new Date(m.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })"></span>
                            </div>
                            <!-- Message envoyé -->
                            <template x-if="m.expediteur_id === monId">
                                <div class="flex items-start justify-end gap-space-sm ml-auto max-w-xl">
                                    <div class="bg-primary text-on-primary p-space-md rounded-2xl rounded-tr-sm shadow-md text-left">
                                        <p class="font-body-md text-body-md text-on-primary leading-relaxed whitespace-pre-line" x-text="m.contenu"></p>
                                        <div class="flex items-center justify-end gap-1.5 mt-1.5 text-primary-fixed">
                                            <span class="font-body-sm text-body-sm text-[11px]" x-text="new Date(m.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })"></span>
                                            <span class="material-symbols-outlined text-[14px]">done_all</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <!-- Message reçu -->
                            <template x-if="m.expediteur_id !== monId">
                                <div class="flex items-start gap-space-sm max-w-xl">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-primary flex-shrink-0 mt-1"
                                         :class="i > 0 && messages[i - 1].expediteur_id === m.expediteur_id ? 'bg-transparent' : 'bg-surface-container-highest shadow-sm'">
                                        <span class="material-symbols-outlined text-[18px]" x-show="! (i > 0 && messages[i - 1].expediteur_id === m.expediteur_id)">{{ $carte['icone'] }}</span>
                                    </div>
                                    <div class="bg-surface-container-lowest p-space-md rounded-2xl rounded-tl-sm shadow-sm">
                                        <div class="flex items-center justify-between gap-4 mb-1">
                                            <span class="font-label-sm text-label-sm text-primary font-bold">{{ $carte['nom'] }}</span>
                                            <span class="font-body-sm text-body-sm text-[11px] text-outline" x-text="new Date(m.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })"></span>
                                        </div>
                                        <p class="font-body-md text-body-md text-on-surface leading-relaxed whitespace-pre-line" x-text="m.contenu"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Saisie -->
                <div class="p-space-sm md:p-space-md bg-surface-container-lowest shadow-lg space-y-space-xs">
                    <div class="flex items-center justify-between gap-space-sm px-1">
                        <div class="flex items-center gap-space-xs text-on-surface-variant">
                            <button type="button" @click="message = (message ? message + '\n' : '') + 'Ordonnance : '; $refs.saisie.focus()" class="p-2 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1 text-xs" title="Décrire une ordonnance">
                                <span class="material-symbols-outlined text-[20px]">attach_file</span>
                                <span class="hidden sm:inline font-label-sm text-label-sm">Ordonnance</span>
                            </button>
                            <button type="button" @click="message = (message ? message + ' ' : '') + '🙂'; $refs.saisie.focus()" class="p-2 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-primary transition-colors" title="Insérer un émoticône">
                                <span class="material-symbols-outlined text-[20px]">sentiment_satisfied</span>
                            </button>
                        </div>
                        <div class="flex items-center gap-1 text-on-surface-variant">
                            <span class="material-symbols-outlined text-primary text-[14px]">lock</span>
                            <span class="font-body-sm text-body-sm text-[11px]">Chiffrement certifié SSL</span>
                        </div>
                    </div>
                    <form class="flex items-center gap-space-sm" @submit.prevent="envoyer()">
                        <div class="relative flex-1">
                            <textarea x-ref="saisie" x-model="message" :disabled="enCours" @keydown.enter.prevent="if (! $event.shiftKey) envoyer(); else message += '\n'"
                                      class="w-full px-space-md py-3 bg-surface-container-low rounded-xl border-0 font-body-md text-body-md text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none transition-all leading-normal"
                                      placeholder="Écrivez votre message... (ex: posologie, allergies, précision coursier)" rows="1"></textarea>
                        </div>
                        <button class="h-12 px-space-md rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all active:scale-95 flex-shrink-0 disabled:opacity-60" type="submit" :disabled="enCours || ! message.trim()">
                            <span class="hidden sm:inline">Envoyer</span>
                            <span class="material-symbols-outlined text-[18px]">send</span>
                        </button>
                    </form>
                </div>
            </section>
        </div>
        @include('messagerie.partials.pied')
    </div>
</div>
@endsection

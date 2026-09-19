@extends('layouts.app')

@section('titre', 'Conversation')

@section('contenu')
<div class="mx-auto max-w-3xl space-y-4"
     x-data="PharmaConnect.messagerie({
         conversationId: {{ $conversation->id }},
         monId: {{ $monId }},
         csrf: '{{ csrf_token() }}',
         messages: @json($messages->map(fn ($m) => [
             'id' => $m->id,
             'expediteur_id' => $m->expediteur_id,
             'contenu' => $m->contenu,
             'created_at' => $m->created_at->toIso8601String(),
         ])),
     })"
     x-init="init()">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('messagerie.index') }}" class="text-slate-400 hover:text-slate-600">←</a>
            <div>
                <h1 class="text-xl font-black">{{ $interlocuteur?->name ?? 'Conversation' }}</h1>
                <p class="text-xs text-slate-500">
                    @if($conversation->commande) 📦 Commande {{ $conversation->commande->numero }} · @endif
                    <span class="text-menthe-600">● <span x-text="enLigne.length + ' en ligne'"></span></span>
                </p>
            </div>
        </div>
        @if($interlocuteur?->telephone)
            <a href="tel:{{ $interlocuteur->telephone }}" class="btn-secondary text-sm">📞 Appeler</a>
        @endif
    </div>

    <div class="card flex h-[60vh] flex-col">
        <div id="fil-messages" class="flex-1 space-y-3 overflow-y-auto p-5">
            <template x-for="message in messages" :key="message.id">
                <div :class="message.expediteur_id === {{ $monId }} ? 'bulle-moi' : 'bulle-autre'">
                    <span x-text="message.contenu"></span>
                    <span class="mt-1 block text-[10px] opacity-60" x-text="new Date(message.created_at).toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})"></span>
                </div>
            </template>
        </div>

        <form @submit.prevent="envoyer()" class="flex gap-2 border-t border-menthe-100 p-3">
            <input x-model="message" :disabled="enCours" placeholder="Écrivez votre message…"
                   class="input flex-1" autocomplete="off">
            <button class="btn-primary" :disabled="enCours || !message.trim()">Envoyer ➤</button>
        </form>
    </div>
</div>
@endsection

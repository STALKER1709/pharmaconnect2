@extends('layouts.app')

@section('titre', 'Conversation')

@section('contenu')
<div style="max-width:820px; margin:0 auto;"
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

    <div class="rangee-entre mb-4">
        <div class="rangée">
            <a href="{{ route('messagerie.index') }}" class="btn-lien" style="font-size:18px;">←</a>
            <div>
                <h1 class="titre-section">{{ $interlocuteur?->name ?? 'Conversation' }}</h1>
                <p class="texte-petit texte-doux">
                    @if($conversation->commande) 📦 Commande {{ $conversation->commande->numero }} · @endif
                    <span style="color:var(--vert-600);">● <span x-text="enLigne.length + ' en ligne'"></span></span>
                </p>
            </div>
        </div>
        @if($interlocuteur?->telephone)
            <a href="tel:{{ $interlocuteur->telephone }}" class="btn btn-secondaire btn-petit">📞 Appeler</a>
        @endif
    </div>

    <div class="carte" style="display:flex; flex-direction:column; height:60vh;">
        <div id="fil-messages" style="flex:1; overflow-y:auto; padding:20px; display:grid; gap:12px; align-content:start;">
            <template x-for="message in messages" :key="message.id">
                <div :class="message.expediteur_id === {{ $monId }} ? 'bulle-moi' : 'bulle-autre'">
                    <span x-text="message.contenu"></span>
                    <span class="bulle-heure" style="display:block;" x-text="new Date(message.created_at).toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})"></span>
                </div>
            </template>
        </div>

        <form @submit.prevent="envoyer()" class="rangée" style="padding:12px; border-top:1px solid var(--vert-100);">
            <input x-model="message" :disabled="enCours" placeholder="Écrivez votre message…"
                   class="champ" style="flex:1;" autocomplete="off">
            <button class="btn btn-primaire" :disabled="enCours || !message.trim()">Envoyer ➤</button>
        </form>
    </div>
</div>
@endsection

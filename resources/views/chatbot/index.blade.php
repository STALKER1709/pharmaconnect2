@extends('layouts.app')

@section('titre', 'Assistant santé')

@section('contenu')
<div class="mx-auto max-w-2xl space-y-4"
     x-data="{
         ouvert: true,
         enCours: false,
         message: '',
         reponses: @json($messages->map(fn ($m) => ['role' => $m->role, 'contenu' => $m->message])),
         suggestions: ['Fièvre et palu ?', 'Comment payer ?', 'Paracétamol : dose ?', 'Suivre ma livraison'],

         async envoyer(texte = null) {
             const contenu = (texte ?? this.message).trim();
             if (!contenu || this.enCours) return;
             this.reponses.push({role: 'user', contenu});
             this.message = '';
             this.enCours = true;
             this.defiler();
             try {
                 const rep = await fetch('{{ route('chatbot.demander') }}', {
                     method: 'POST',
                     headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json'},
                     body: JSON.stringify({message: contenu}),
                 });
                 const data = await rep.json();
                 this.reponses.push({role: 'assistant', contenu: data.reponse});
                 if (data.suggestions) this.suggestions = data.suggestions;
             } catch {
                 this.reponses.push({role: 'assistant', contenu: 'Connexion impossible, réessayez.'});
             } finally {
                 this.enCours = false;
                 this.defiler();
             }
         },
         defiler() {
             this.$nextTick(() => {
                 const box = document.getElementById('fil-chatbot');
                 if (box) box.scrollTop = box.scrollHeight;
             });
         },
     }">

    <h1 class="text-2xl font-black">🤖 Assistant santé PharmaConnect</h1>
    <p class="text-sm text-slate-500">Conseils généraux (mode local, ne remplace pas un médecin).</p>

    <div class="card flex h-[65vh] flex-col">
        <div id="fil-chatbot" class="flex-1 space-y-3 overflow-y-auto p-5">
            <template x-if="reponses.length === 0">
                <div class="bulle-autre">Bonjour 👋 Je suis votre assistant santé. Posez-moi vos questions : fièvre, palu, posologie, paiement, livraison…</div>
            </template>
            <template x-for="(r, i) in reponses" :key="i">
                <div :class="r.role === 'user' ? 'bulle-moi' : 'bulle-autre'" x-text="r.contenu"></div>
            </template>
            <div x-show="enCours" class="bulle-autre animate-pulse">…</div>
        </div>

        <div class="flex flex-wrap gap-2 border-t border-menthe-100 px-4 pt-3">
            <template x-for="s in suggestions" :key="s">
                <button @click="envoyer(s)" class="rounded-full border border-menthe-200 px-3 py-1 text-xs text-menthe-800 hover:bg-menthe-50" x-text="s"></button>
            </template>
        </div>

        <form @submit.prevent="envoyer()" class="flex gap-2 p-3">
            <input x-model="message" :disabled="enCours" placeholder="Votre question…" class="input flex-1" autocomplete="off">
            <button class="btn-primary" :disabled="enCours">Envoyer</button>
        </form>
    </div>
</div>
@endsection

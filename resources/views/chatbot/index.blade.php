@extends('layouts.app')

@section('titre', 'Assistant santé')

@section('contenu')
<div style="max-width:760px; margin:0 auto;"
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

    <h1 class="titre-page">🤖 Assistant santé PharmaConnect</h1>
    <p class="sous-titre mb-4">Conseils généraux (mode local, ne remplace pas un médecin).</p>

    <div class="carte" style="display:flex; flex-direction:column; height:65vh;">
        <div id="fil-chatbot" style="flex:1; overflow-y:auto; padding:20px; display:grid; gap:12px; align-content:start;">
            <template x-if="reponses.length === 0">
                <div class="bulle-autre">Bonjour 👋 Je suis votre assistant santé. Posez-moi vos questions : fièvre, palu, posologie, paiement, livraison…</div>
            </template>
            <template x-for="(r, i) in reponses" :key="i">
                <div :class="r.role === 'user' ? 'bulle-moi' : 'bulle-autre'" x-text="r.contenu"></div>
            </template>
            <div x-show="enCours" class="bulle-autre" style="animation:pulse 1.2s infinite;">…</div>
        </div>

        <div class="rangée" style="padding:12px 16px 0; border-top:1px solid var(--vert-100);">
            <template x-for="s in suggestions" :key="s">
                <button type="button" @click="envoyer(s)" class="puce-recherche" x-text="s"></button>
            </template>
        </div>

        <form @submit.prevent="envoyer()" class="rangée" style="padding:12px;">
            <input x-model="message" :disabled="enCours" placeholder="Votre question…" class="champ" style="flex:1;" autocomplete="off">
            <button class="btn btn-primaire" :disabled="enCours">Envoyer</button>
        </form>
    </div>

    <div class="alerte alerte-ambre mt-4">
        ⚠️ PharmaBot donne des conseils généraux et ne remplace pas l'avis d'un professionnel de santé.
    </div>
</div>
@endsection

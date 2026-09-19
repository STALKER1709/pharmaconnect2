/**
 * Messagerie temps réel :
 *  - présence (qui est en ligne)
 *  - nouveaux messages via Reverb
 *  - envoi par fetch (pas de rechargement)
 */
window.PharmaConnect = window.PharmaConnect || {};

PharmaConnect.messagerie = (config) => ({
    conversationId: config.conversationId,
    monId: config.monId,
    csrf: config.csrf,
    message: '',
    enCours: false,
    enLigne: [],
    messages: config.messages ?? [],

    init() {
        PharmaConnect.conversation(this.conversationId, {
            onMessage: (e) => {
                this.messages.push({
                    id: e.id,
                    expediteur_id: e.expediteur_id,
                    expediteur_nom: e.expediteur_nom,
                    contenu: e.contenu,
                    created_at: e.created_at,
                });
                this.defiler();
            },
            onJoined: (here, user) => {
                if (user) {
                    this.enLigne.push(user);
                }
            },
            onLeft: (user) => {
                this.enLigne = this.enLigne.filter((u) => u.id !== user.id);
            },
        });

        this.defiler();
    },

    async envoyer() {
        const contenu = this.message.trim();
        if (! contenu || this.enCours) return;

        this.enCours = true;
        this.message = '';

        try {
            const rep = await fetch(`/messagerie/${this.conversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ contenu }),
            });
            const data = await rep.json();
            if (data.succes) {
                this.messages.push(data.message);
                this.defiler();
            }
        } finally {
            this.enCours = false;
        }
    },

    defiler() {
        this.$nextTick(() => {
            const box = document.getElementById('fil-messages');
            if (box) box.scrollTop = box.scrollHeight;
        });
    },
});

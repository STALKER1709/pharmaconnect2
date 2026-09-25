/**
 * Widget chatbot — Alpine ouvre/ferme, fetch vers /chatbot.
 */
window.PharmaConnect = window.PharmaConnect || {};

PharmaConnect.chatbot = (config = {}) => ({
    ouvert: false,
    enCours: false,
    message: '',
    reponses: config.historique ?? [],
    suggestions: config.suggestions ?? ['Fièvre et palu ?', 'Comment payer ?', 'Paracétamol : dose ?', 'Suivre ma livraison'],

    init() {
        this.defiler();
    },

    /** Efface l'historique côté serveur et repart d'une conversation vierge. */
    async nouvelle() {
        await fetch('/chatbot', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                'Accept': 'application/json',
            },
        }).catch(() => {});
        this.reponses = [];
    },

    async envoyer(texte = null) {
        const contenu = (texte ?? this.message).trim();
        if (! contenu || this.enCours) return;

        this.reponses.push({ role: 'user', contenu, heure: new Date().toISOString() });
        this.message = '';
        this.enCours = true;
        this.defiler();

        try {
            const rep = await fetch('/chatbot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: contenu }),
            });

            const data = await rep.json();
            this.reponses.push({ role: 'assistant', contenu: data.reponse ?? 'Erreur, réessayez.', heure: new Date().toISOString() });
        } catch {
            this.reponses.push({ role: 'assistant', contenu: 'Connexion impossible. Réessayez.', heure: new Date().toISOString() });
        } finally {
            this.enCours = false;
            this.defiler();
        }
    },

    defiler() {
        this.$nextTick(() => {
            const box = document.getElementById('chatbot-messages');
            if (box) box.scrollTop = box.scrollHeight;
        });
    },
});

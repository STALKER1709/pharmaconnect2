/**
 * Widget chatbot — Alpine ouvre/ferme, fetch vers /chatbot.
 */
window.PharmaConnect = window.PharmaConnect || {};

PharmaConnect.chatbot = () => ({
    ouvert: false,
    enCours: false,
    message: '',
    reponses: [],
    suggestions: ['Fièvre et palu ?', 'Comment payer ?', 'Paracétamol : dose ?', 'Suivre ma livraison'],

    async envoyer(texte = null) {
        const contenu = (texte ?? this.message).trim();
        if (! contenu || this.enCours) return;

        this.reponses.push({ role: 'user', contenu });
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
            this.reponses.push({ role: 'assistant', contenu: data.reponse ?? 'Erreur, réessayez.' });
            if (data.suggestions) this.suggestions = data.suggestions;
        } catch {
            this.reponses.push({ role: 'assistant', contenu: 'Connexion impossible. Réessayez.' });
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

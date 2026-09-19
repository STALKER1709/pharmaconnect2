import Echo from 'laravel-echo';

import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY ?? 'pharmaconnect-local-key',
    wsHost: import.meta.env.VITE_REVERB_HOST ?? 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

/**
 * Utilitaires temps réel réutilisables :
 *  - PharmaConnect.suivreCommande(id, callbacks)
 *  - PharmaConnect.conversation(id, callbacks)
 */
window.PharmaConnect = window.PharmaConnect || {};

PharmaConnect.suivreCommande = (commandeId, { onStatut, onPosition } = {}) => {
    const canal = window.Echo.private(`commande.${commandeId}`);

    if (onStatut) {
        canal.listen('.commande.statut', (e) => onStatut(e));
    }
    if (onPosition) {
        canal.listen('.position.mise-a-jour', (e) => onPosition(e));
    }

    return canal;
};

PharmaConnect.conversation = (conversationId, { onMessage, onJoined, onLeft } = {}) => {
    const canal = window.Echo.join(`conversation.${conversationId}`)
        .here((users) => onJoined && onJoined(users))
        .joining((user) => onJoined && onJoined(null, user))
        .leaving((user) => onLeft && onLeft(user))
        .listenForWhisper('typing', () => {});

    if (onMessage) {
        canal.listen('.message.envoye', (e) => onMessage(e));
    }

    return canal;
};

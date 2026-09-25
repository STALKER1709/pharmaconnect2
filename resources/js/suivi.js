/**
 * Page de suivi d'une commande :
 *  - carte Leaflet (pharmacie, livreur, destination)
 *  - écoute Reverb : commande.statut + position.mise-a-jour
 *  - polling JSON de secours toutes les 15 s
 *  - bouton « Partager ma position » (Geolocation API)
 */
window.PharmaConnect = window.PharmaConnect || {};

PharmaConnect.suivi = (config) => ({
    commandeId: config.commandeId,
    statut: config.statut,
    statutLabel: config.statutLabel,
    urls: config.urls,
    csrf: config.csrf,

    carte: null,
    icones: {},
    majPosition: null,
    maintenant: Date.now(),
    marqueurLivreur: null,
    marqueurDestination: null,
    marqueurPharmacie: null,
    timerPolling: null,
    timerPosition: null,

    init() {
        const el = document.getElementById('carte-suivi');
        if (! el) return;

        this.carte = PharmaConnect.carte(el, { zoomControl: false, attributionControl: false });
        this.icones = config.icones ?? {};
        this.carte.setView([config.latitude ?? 4.0511, config.longitude ?? 9.7679], 13);

        if (config.pharmacie) {
            this.marqueurPharmacie = PharmaConnect.pin(
                this.carte, config.pharmacie.lat, config.pharmacie.lng,
                '', this.icones.pharmacie, 32
            );
        }

        if (config.arrivee) {
            this.marqueurDestination = PharmaConnect.pin(
                this.carte, config.arrivee.lat, config.arrivee.lng,
                '', this.icones.destination, 36
            );
            this.carte.fitBounds(
                L.latLngBounds(
                    [config.pharmacie?.lat ?? config.arrivee.lat, config.pharmacie?.lng ?? config.arrivee.lng],
                    [config.arrivee.lat, config.arrivee.lng]
                ).pad(0.3)
            );
        }

        // Temps réel via Reverb
        PharmaConnect.suivreCommande(this.commandeId, {
            onStatut: (e) => {
                this.statut = e.statut;
                this.statutLabel = e.statut_label;
            },
            onPosition: (e) => this.deplacerLivreur(e.latitude, e.longitude),
        });

        // Polling de secours
        this.timerPolling = setInterval(() => this.interroger(), 15000);
        this.interroger();
        this.timerHorloge = setInterval(() => (this.maintenant = Date.now()), 1000);

        // Le livreur partage automatiquement sa position pendant la course
        if (config.estLivreur && ['acceptee', 'en_route', 'arrivee'].includes(config.statutLivraison ?? '')) {
            this.timerPosition = setInterval(() => this.envoyerMaPosition(), 10000);
            this.envoyerMaPosition();
        }
    },

    async interroger() {
        try {
            const rep = await fetch(this.urls.position, { headers: { Accept: 'application/json' } });
            const data = await rep.json();
            if (data.statut_label) {
                this.statut = data.statut_livraison;
                this.statutLabel = data.statut_label;
            }
            if (data.position) {
                this.deplacerLivreur(data.position.lat, data.position.lng);
            }
        } catch {
            // Silencieux : Reverb gère le temps réel en priorité
        }
    },

    deplacerLivreur(lat, lng) {
        if (this.marqueurLivreur) {
            this.marqueurLivreur.setLatLng([lat, lng]);
        } else {
            this.marqueurLivreur = PharmaConnect.pin(this.carte, lat, lng, '', this.icones.livreur, 44);
        }
        this.carte.panTo([lat, lng]);
        this.majPosition = new Date();
    },

    zoomer(delta) {
        delta > 0 ? this.carte?.zoomIn() : this.carte?.zoomOut();
    },

    recentrer() {
        const cible = this.marqueurLivreur ?? this.marqueurDestination ?? this.marqueurPharmacie;
        if (cible) this.carte.setView(cible.getLatLng(), 15);
    },

    pleinEcran() {
        const el = document.getElementById('carte-suivi')?.parentElement;
        if (! el) return;
        document.fullscreenElement ? document.exitFullscreen() : el.requestFullscreen?.();
        setTimeout(() => this.carte.invalidateSize(), 300);
    },

    partagerMaPosition() {
        PharmaConnect.geolocaliser(
            async (lat, lng) => {
                const rep = await fetch(this.urls.partager, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng }),
                });
                const data = await rep.json();
                if (data.succes) {
                    if (this.marqueurDestination) {
                        this.marqueurDestination.setLatLng([lat, lng]);
                    } else {
                        this.marqueurDestination = PharmaConnect.pin(this.carte, lat, lng, '', this.icones.destination, 36);
                    }
                    this.carte.setView([lat, lng], 15);
                    alert('Position enregistrée ✓');
                }
            },
            (err) => alert('Impossible de récupérer votre position : '+err)
        );
    },

    async envoyerMaPosition() {
        PharmaConnect.geolocaliser(async (lat, lng) => {
            await fetch(this.urls.signalerPosition, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ latitude: lat, longitude: lng }),
            });
            this.deplacerLivreur(lat, lng);
        });
    },

    /** « il y a 12 s » depuis la dernière position reçue. */
    get depuisMaj() {
        if (! this.majPosition) return 'en attente du coursier';
        const s = Math.max(0, Math.round((this.maintenant - this.majPosition) / 1000));

        return s < 60 ? `il y a ${s} s` : `il y a ${Math.round(s / 60)} min`;
    },

    destroy() {
        clearInterval(this.timerPolling);
        clearInterval(this.timerPosition);
        clearInterval(this.timerHorloge);
    },
});

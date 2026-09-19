import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

window.L = L;

// Icônes par défaut de Leaflet (bundlées par Vite)
import marqueur from 'leaflet/dist/images/marker-icon.png';
import marqueur2x from 'leaflet/dist/images/marker-icon-2x.png';
import ombre from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: marqueur,
    iconRetinaUrl: marqueur2x,
    shadowUrl: ombre,
});

/**
 * PharmaConnect.carte(el, options) — crée une carte OSM.
 * PharmaConnect.pin(carte, lat, lng, texte) — ajoute un marqueur.
 * PharmaConnect.geolocaliser(cb) — position du navigateur.
 */
Object.assign(window.PharmaConnect = window.PharmaConnect || {}, {
    carte(el, options = {}) {
        const map = L.map(el, {
            zoom: 13,
            scrollWheelZoom: false,
            ...options,
        });

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);

        return map;
    },

    pin(carte, lat, lng, texte = '') {
        return L.marker([lat, lng], { className: 'pharma-marker' })
            .addTo(carte)
            .bindPopup(texte);
    },

    geolocaliser(cb, erreur = null) {
        if (! navigator.geolocation) {
            erreur && erreur('Géolocalisation non supportée par ce navigateur.');

            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => cb(pos.coords.latitude, pos.coords.longitude),
            (e) => erreur && erreur(e.message),
            { enableHighAccuracy: true, timeout: 8000 }
        );
    },
});

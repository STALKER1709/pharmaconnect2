import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // chart.js et leaflet sont volumineux : on évite les faux positifs de chunk
        chunkSizeWarningLimit: 1200,
    },
});

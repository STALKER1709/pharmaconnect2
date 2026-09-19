import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                // Palette PharmaConnect — vert pharmacie
                menthe: {
                    50: '#f0fdf6',
                    100: '#dcfce9',
                    200: '#bbf7d3',
                    300: '#86efae',
                    400: '#4ade85',
                    500: '#22c76a',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },
            },

            fontFamily: {
                sans: ['ui-sans-serif', 'system-ui', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

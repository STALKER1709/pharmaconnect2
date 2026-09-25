/**
 * Configuration Tailwind reprise à l'identique des maquettes Google Stitch
 * (stitch_pharmaconnect_cameroon_web_app/ * /code.html) : mêmes couleurs, rayons,
 * espacements et échelles typographiques, pour un rendu au pixel près.
 * Compilée localement par Vite : aucun CDN.
 */
/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            "colors": {
                "on-secondary-fixed-variant": "#005321",
                "on-secondary-container": "#007432",
                "surface-container-low": "#f0f3ff",
                "on-surface-variant": "#3e4a3d",
                "error-container": "#ffdad6",
                "on-primary": "#ffffff",
                "on-tertiary-fixed-variant": "#8a143c",
                "inverse-primary": "#62df7d",
                "on-primary-fixed": "#002109",
                "secondary-fixed": "#6bff8f",
                "secondary-fixed-dim": "#4ae176",
                "error": "#ba1a1a",
                "tertiary-fixed-dim": "#ffb2bf",
                "on-primary-fixed-variant": "#005320",
                "inverse-surface": "#263143",
                "surface-container-highest": "#d8e3fb",
                "surface-bright": "#f9f9ff",
                "outline": "#6e7b6c",
                "on-secondary": "#ffffff",
                "on-error-container": "#93000a",
                "inverse-on-surface": "#ecf1ff",
                "background": "#f9f9ff",
                "secondary-container": "#6bff8f",
                "primary": "#006b2c",
                "surface": "#f9f9ff",
                "primary-container": "#00873a",
                "surface-container": "#e7eeff",
                "primary-fixed": "#7ffc97",
                "surface-container-high": "#dee8ff",
                "outline-variant": "#bdcaba",
                "on-error": "#ffffff",
                "on-primary-container": "#f7fff2",
                "on-tertiary-fixed": "#3f0016",
                "on-surface": "#111c2d",
                "on-tertiary": "#ffffff",
                "surface-container-lowest": "#ffffff",
                "tertiary-container": "#c74668",
                "on-secondary-fixed": "#002109",
                "secondary": "#006e2f",
                "on-background": "#111c2d",
                "surface-variant": "#d8e3fb",
                "surface-dim": "#cfdaf2",
                "surface-tint": "#006e2d",
                "on-tertiary-container": "#fffbff",
                "tertiary": "#a72d51",
                "tertiary-fixed": "#ffd9de",
                "primary-fixed-dim": "#62df7d"
            },
            "borderRadius": {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
            "spacing": {
                "margin": "1rem",
                "space-sm": "0.5rem",
                "space-xl": "2.5rem",
                "space-lg": "1.5rem",
                "gutter": "1rem",
                "space-xs": "0.25rem",
                "space-md": "1rem",
                "margin-desktop": "2.5rem",
                "gutter-desktop": "1.5rem"
            },
            "fontFamily": {
                "body-lg": [
                    "Inter"
                ],
                "headline-lg-mobile": [
                    "Plus Jakarta Sans"
                ],
                "label-lg": [
                    "Inter"
                ],
                "label-md": [
                    "Inter"
                ],
                "body-md": [
                    "Inter"
                ],
                "headline-lg": [
                    "Plus Jakarta Sans"
                ],
                "currency-display": [
                    "Plus Jakarta Sans"
                ],
                "headline-md": [
                    "Plus Jakarta Sans"
                ],
                "headline-xl": [
                    "Plus Jakarta Sans"
                ],
                "label-sm": [
                    "Inter"
                ],
                "headline-sm": [
                    "Plus Jakarta Sans"
                ],
                "headline-xl-mobile": [
                    "Plus Jakarta Sans"
                ],
                "body-sm": [
                    "Inter"
                ]
            },
            "fontSize": {
                "body-lg": [
                    "16px",
                    {
                        "lineHeight": "24px",
                        "fontWeight": "400"
                    }
                ],
                "headline-lg-mobile": [
                    "24px",
                    {
                        "lineHeight": "32px",
                        "fontWeight": "600"
                    }
                ],
                "label-lg": [
                    "14px",
                    {
                        "lineHeight": "20px",
                        "fontWeight": "600"
                    }
                ],
                "label-md": [
                    "12px",
                    {
                        "lineHeight": "16px",
                        "fontWeight": "600"
                    }
                ],
                "body-md": [
                    "14px",
                    {
                        "lineHeight": "20px",
                        "fontWeight": "400"
                    }
                ],
                "headline-lg": [
                    "32px",
                    {
                        "lineHeight": "40px",
                        "fontWeight": "600"
                    }
                ],
                "currency-display": [
                    "20px",
                    {
                        "lineHeight": "24px",
                        "fontWeight": "700"
                    }
                ],
                "headline-md": [
                    "22px",
                    {
                        "lineHeight": "28px",
                        "fontWeight": "600"
                    }
                ],
                "headline-xl": [
                    "40px",
                    {
                        "lineHeight": "48px",
                        "fontWeight": "700"
                    }
                ],
                "label-sm": [
                    "11px",
                    {
                        "lineHeight": "14px",
                        "fontWeight": "500"
                    }
                ],
                "headline-sm": [
                    "18px",
                    {
                        "lineHeight": "24px",
                        "fontWeight": "600"
                    }
                ],
                "headline-xl-mobile": [
                    "30px",
                    {
                        "lineHeight": "38px",
                        "fontWeight": "700"
                    }
                ],
                "body-sm": [
                    "12px",
                    {
                        "lineHeight": "16px",
                        "fontWeight": "400"
                    }
                ]
            }
        },
    },
    plugins: [],
};

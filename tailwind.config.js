import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // Police dédiée à la landing page Cortex Bénin TV (voir accueil.blade.php).
                cortex: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Palette officielle CSS (logo "Cours de Soutien Scolaire") — voir config/ecole.php.
                // Définies via variables CSS (voir resources/css/app.css) pour permettre à
                // layouts/app.blade.php de les recolorer par établissement (ex. UCAO en rose)
                // sans toucher aux dizaines de vues qui utilisent ces classes.
                'brand-sky': 'rgb(var(--brand-sky) / <alpha-value>)',
                'brand-sky-deep': 'rgb(var(--brand-sky-deep) / <alpha-value>)',
                'brand-green': '#4CAF6D',
                'brand-green-deep': '#379258',
                'brand-salmon': '#D32F2F',
                'brand-salmon-deep': '#B71C1C',
                'brand-ink': '#1A1A1A',

                // Palette Cortex Bénin TV — page d'accueil publique (voir config/cortex.php).
                'cortex-rouge': '#E53935',
                'cortex-rouge-deep': '#C62828',
                'cortex-encre': '#111114',
                'cortex-body': '#4A4A4F',
                'cortex-surface': '#FAFAFA',
                'cortex-surface-2': '#F7F7F8',
                'cortex-border': '#ECECEE',
            },
            keyframes: {
                'defilement-ecoles': {
                    '0%': { transform: 'translateX(0)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
                flotter: {
                    '0%, 100%': { transform: 'translate(0, 0)' },
                    '50%': { transform: 'translate(8px, -8px)' },
                },
                'flotter-inverse': {
                    '0%, 100%': { transform: 'translate(0, 0)' },
                    '50%': { transform: 'translate(-10px, 10px)' },
                },
            },
            animation: {
                // Vitesse proportionnelle au nombre d'établissements (voir config/cortex.php).
                'defilement-ecoles': 'defilement-ecoles 30s linear infinite',
                flotter: 'flotter 7s ease-in-out infinite',
                'flotter-lent': 'flotter-inverse 8s ease-in-out infinite',
            },
            transitionTimingFunction: {
                entree: 'cubic-bezier(0.16, 1, 0.3, 1)',
                rapide: 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
        },
    },

    plugins: [forms],
};

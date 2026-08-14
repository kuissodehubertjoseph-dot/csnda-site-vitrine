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
            },
            colors: {
                // Palette officielle CSNDA — voir config/ecole.php pour l'usage sur les cartes.
                'brand-sky': '#5EB3E4',
                'brand-sky-deep': '#3E93C4',
                'brand-green': '#4CAF6D',
                'brand-green-deep': '#379258',
                'brand-salmon': '#F2A6B0',
                'brand-salmon-deep': '#E58B97',
                'brand-ink': '#1A1A1A',
            },
        },
    },

    plugins: [forms],
};

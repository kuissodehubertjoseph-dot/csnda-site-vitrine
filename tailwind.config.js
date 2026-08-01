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
                // Palette officielle CSNDA - ne pas ajouter d'autres couleurs
                ciel: '#5EB3E4',
                laurier: '#4CAF6D',
                saumon: '#F2A6B0',
                encre: '#1A1A1A',
            },
        },
    },

    plugins: [forms],
};

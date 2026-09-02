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
                // Satu sumber warna brand — semua shade emerald yang dipakai
                // di aplikasi didaftarkan di sini (bukan hardcode "emerald-*"
                // tersebar di view), supaya rebrand cukup ubah di satu tempat.
                brand: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    DEFAULT: '#047857', // = brand-700
                    light: '#059669',   // = brand-600
                    dark: '#065f46',    // = brand-800
                    darker: '#064e3b',  // = brand-900
                },
            },
        },
    },

    plugins: [forms],
};

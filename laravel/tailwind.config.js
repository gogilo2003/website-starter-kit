import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                primary: {
                    DEFAULT: '#A3D52F',
                    50: '#E7F4C8',
                    100: '#DFF0B7',
                    200: '#D0EA95',
                    300: '#C1E373',
                    400: '#B2DC51',
                    500: '#A3D52F',
                    600: '#81AA22',
                    700: '#5D7B19',
                    800: '#3A4C0F',
                    900: '#161E06',
                    950: '#050601'
                },
                secondary: {
                    DEFAULT: '#EC8F3C',
                    50: '#FCEFE3',
                    100: '#FAE4D1',
                    200: '#F7CFAC',
                    300: '#F3BA86',
                    400: '#F0A461',
                    500: '#EC8F3C',
                    600: '#DB7215',
                    700: '#A75810',
                    800: '#743D0B',
                    900: '#412206',
                    950: '#281504'
                },
            },
        },
    },

    plugins: [forms],
};

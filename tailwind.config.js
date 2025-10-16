const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./app/View/Components/**/*.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                'roboto':['Roboto'],
                'manrope':['Manrope'],
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            screens: {
                'xs': '640px',
                'sm': '768px',
                'md': '1024px',
                'lg': '1366px', // Optimizado para 1366x768
                'xl': '1536px',
            },
        },
        fontSize: {
            'xs': '.75rem',
            'sm': '.875rem',
            'tiny': '.925rem',
            'base': '1rem',
            'lg': '1.125rem',
            'xl': '1.25rem',
            '2xl': '1.5rem',
            '3xl': '1.875rem',
            '4xl': '2.25rem',
            '5xl': '3rem',
            '6xl': '4rem',
            '7xl': '5rem',
          }
    },

    corePlugins: {
       container: false,
    },

    plugins: [require('@tailwindcss/forms')],
};

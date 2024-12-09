import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                dmSerif: ['DM Serif Display', 'serif'],
            },
            colors: {
                lightgray: '#EDECEA',
                medgray: '#7E7F7F',
                darkgray: '#302F2D',
                orange: '#FF904C',
                lightyellow: '#FFDF58',
                warmyellow: '#FEBD58',
            },
            keyframes:{
                'gradient-x': {
                    '0%': { 'background-position': '0% 50%' },
                    '25%': { 'background-position': '25% 50%' },
                    '50%': { 'background-position': '100% 50%' }, // Speeds up here
                    '75%': { 'background-position': '75% 50%' },
                    '100%': { 'background-position': '0% 50%' },
                  },
            },
            animation: {
                'gradient': 'gradient-x 6s cubic-bezier(0.25, 1, 0.5, 1) infinite', // Ease-out at start and speed up
            },
        },
    },
    plugins: [],
};

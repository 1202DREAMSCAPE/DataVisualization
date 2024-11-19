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
        },
    },
    plugins: [],
};


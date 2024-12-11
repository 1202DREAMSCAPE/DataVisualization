import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/chart-init.js',
                'resources/js/chart.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        extensions: ['.js', '.jsx', '.ts', '.tsx'], // Add these extensions for proper resolution
    },
    esbuild: {
        loader: 'js', // Ensure .js files are treated as JavaScript
    },
});

import inertia from '@inertiajs/vite';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import path from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
    resolve: {
        alias: {
            // Ziggy's JS ships with the Composer package; `@routes` in app.blade.php provides the route list.
            'ziggy-js': path.resolve('vendor/tightenco/ziggy'),
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Outfit', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        inertia({
            ssr: false,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

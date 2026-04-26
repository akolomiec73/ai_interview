import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/calendar.css',
                'resources/css/create-event-modal.css',
                'resources/js/app.js',
                'resources/js/calendar.js',
                'resources/js/create-event-modal.js',
                'resources/js/notifications.js',
            ],
            refresh: true,
        }),

        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
});

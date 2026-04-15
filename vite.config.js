import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/landing.css",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        target: "esnext", // Target browser paling modern
        cssTarget: "chrome110", // Memastikan CSS tidak di-prefix berlebihan
    },
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});

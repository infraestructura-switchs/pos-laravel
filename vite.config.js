import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/ts/app.ts'
            ],
        }),
    ],
    // Configuración para multi-tenancy
    // Los assets se sirven desde el dominio central
    base: process.env.APP_ENV === 'production'
        ? '/build/'
        : 'https://dokploy.movete.cloud/build/',
    server: {
        host: 'dokploy.movete.cloud',
        port: 5173,
        strictPort: false,
        hmr: {
            host: 'dokploy.movete.cloud'
        }
    }
});

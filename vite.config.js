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
        : 'http://switchs.test/build/',
    server: {
        host: 'switchs.test',
        port: 5173,
        strictPort: false,
        hmr: {
            host: 'switchs.test'
        }
    }
});

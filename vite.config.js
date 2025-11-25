import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    // Cargar variables de entorno
    const env = loadEnv(mode, process.cwd(), '');
    const centralDomain = env.CENTRAL_DOMAIN || 'dokploy.movete.cloud';
    
    return {
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
        // Usar ruta relativa por defecto para evitar problemas de CORS/Mixed Content
        // Solo usar dominio central si estamos explícitamente en local
        base: process.env.APP_ENV === 'local' 
            ? `http://${centralDomain}/build/`
            : '/build/',
        server: {
            host: centralDomain,
            port: 5173,
            strictPort: false,
            hmr: {
                host: centralDomain
            }
        }
    };
});

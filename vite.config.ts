import {wayfinder} from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import {defineConfig,} from 'vite';

// В docker-контейнере (docker/compose.yaml): слушаем все интерфейсы, а в public/hot и HMR
// отдаём localhost; polling — события ФС из Windows bind mount в контейнер не доходят
const inDocker = !!process.env.VITE_DOCKER;

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        })
    ],
    server: {
        host: inDocker ? '0.0.0.0' : 'localhost',
        port: 5173,
        hmr: inDocker ? {host: 'localhost'} : undefined,
        watch: inDocker ? {usePolling: true, interval: 300, ignored: ['**/vendor/**', '**/storage/**']} : undefined,
        allowedHosts: true,
        cors: true,
    },
});

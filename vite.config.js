import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/buscar-equipo.js', 'resources/js/previsualizar-imagenes.js',
                'resources/js/buscar-empleado.js', 'resources/js/buscar-equipo-tras.js', 'resources/js/buscar-codl4.js', 'resources/js/buscar-traslado.js' 

            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

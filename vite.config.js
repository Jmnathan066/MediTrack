import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/home.css',
                'resources/js/home.js',
                'resources/css/styleLogin.css',
                'resources/css/styleRegistro.css',
                'resources/css/recuperar.css',
                'resources/css/validarCodigo.css',
                'resources/css/nuevaPassword.css',
                'resources/css/medic/medicamento.css',
                'resources/js/medic/medicamento.js',
                'resources/css/perfil/perfil.css',
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

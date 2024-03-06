import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/default.js',
                'resources/js/admin.js',
                'resources/js/user.js',
                'resources/js/genmanager.js'
            ],
            refresh: true,
        }),
    ],
});

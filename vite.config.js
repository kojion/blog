import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/blog.scss', 'resources/js/blog.js', 'resources/sass/admin.scss', 'resources/js/admin.js'],
            refresh: true,
        }),
    ],
});

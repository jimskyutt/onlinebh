import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/add_bh.css',
                'resources/js/app.js', 
                'resources/js/add_bh.js',
                'resources/js/map.js',
                'resources/js/owners.js',
            ],
            refresh: true,
        }),
    ],
});
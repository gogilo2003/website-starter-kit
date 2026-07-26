import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    build: {
        outDir: '../public_html/build'
    },
    resolve: {
        alias: [
            { find: '@meacms/menu', replacement: path.resolve(__dirname, './packages/meacms/menu/resources/js') },
            { find: '@meacms/products', replacement: path.resolve(__dirname, './packages/meacms/products/resources/js') },
            { find: '@meacms/quotes', replacement: path.resolve(__dirname, './packages/meacms/quotes/resources/js') },
            { find: '@meacms/news', replacement: path.resolve(__dirname, './packages/meacms/news/resources/js') },
            { find: '@meacms/downloads', replacement: path.resolve(__dirname, './packages/meacms/downloads/resources/js') },
            { find: '@meacms/partners', replacement: path.resolve(__dirname, './packages/meacms/partners/resources/js') },
            { find: '@meacms/slides', replacement: path.resolve(__dirname, './packages/meacms/slides/resources/js') },
            { find: '@meacms/page-sections', replacement: path.resolve(__dirname, './packages/meacms/page-sections/resources/js') },
            { find: '@', replacement: path.resolve(__dirname, './resources/js') },
        ],
    },
    plugins: [
        laravel({
            input: 'resources/js/app.ts',
            ssr: 'resources/js/ssr.ts',
            refresh: true,
            hotFile: '../public_html/hot'
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});

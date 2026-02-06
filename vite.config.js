import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { nodePolyfills } from 'vite-plugin-node-polyfills';

export default defineConfig({
    plugins: [
        nodePolyfills({
        // Specific modules to polyfill
        include: ['crypto', 'buffer', 'stream'],
        // Whether to polyfill `node:` protocol imports.
        globals: {
            Buffer: true, 
            global: true,
            process: true,
        },
        }),
        laravel({
            input: 'resources/js/app.tsx',
            refresh: true,
        }),
        react(),
    ],
    define: {
        'process.env': {},
        'process.version': JSON.stringify('v18.0.0'), // Mock a node version
    },
});

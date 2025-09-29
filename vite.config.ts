import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// https://vite.dev/config/
export default defineConfig({
    plugins: [react()],
    resolve: {
        alias: {
            '@api': '/src/api',
            '@assets': '/src/assets',
            '@pages': '/src/pages',
            '@components': '/src/components',
            '@constants': '/src/constants',
            '@hooks': '/src/hooks',
            '@information-texts': '/src/information-texts',
            '@models': '/src/models',
            '@redux': '/src/redux',
            '@utils': '/src/utils',
        },
    },
});

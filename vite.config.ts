import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';
import os from 'os';

function getLocalIP() {
    const interfaces = os.networkInterfaces();

    // 1. Priorizar interfaces conocidas de WiFi/Ethernet
    for (const name of Object.keys(interfaces)) {
        const lowerName = name.toLowerCase();
        // Buscar nombres comunes de adaptadores reales y excluir virtuales
        if ((lowerName.includes('wi-fi') || lowerName.includes('ethernet') || lowerName.includes('wlan')) &&
            !lowerName.includes('vethernet') &&
            !lowerName.includes('vmware') &&
            !lowerName.includes('virtualbox')) {

            for (const iface of interfaces[name]) {
                if (iface.family === 'IPv4' && !iface.internal && !iface.address.startsWith('169.254')) {
                    return iface.address;
                }
            }
        }
    }

    // 2. Fallback: Tomar la primera IPv4 válida si no se encontró una preferida
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            if (iface.family === 'IPv4' && !iface.internal && !iface.address.startsWith('169.254')) {
                return iface.address;
            }
        }
    }

    return 'localhost';
}

const localIP = getLocalIP();

export default defineConfig({
    server: {
        host: '0.0.0.0',
        hmr: {
            host: localIP,
        },
    },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
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

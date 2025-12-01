import fs from 'fs';
import os from 'os';

function getLocalIP() {
    const interfaces = os.networkInterfaces();

    // 1. Prioritize known WiFi/Ethernet interfaces
    for (const name of Object.keys(interfaces)) {
        const lowerName = name.toLowerCase();
        // Search for common real adapter names and exclude virtual ones
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

    // 2. Fallback: Take the first valid IPv4 if no preferred one found
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            if (iface.family === 'IPv4' && !iface.internal && !iface.address.startsWith('169.254')) {
                return iface.address;
            }
        }
    }

    return 'localhost';
}

const ip = getLocalIP();
const envPath = '.env';

try {
    if (fs.existsSync(envPath)) {
        let content = fs.readFileSync(envPath, 'utf8');
        const newUrl = `APP_URL=http://${ip}:8000`;

        // Regex to replace existing APP_URL line
        if (content.match(/^APP_URL=.*$/m)) {
            content = content.replace(/^APP_URL=.*$/m, newUrl);
        } else {
            content += `\n${newUrl}`;
        }

        fs.writeFileSync(envPath, content);
        console.log(`✅ .env updated automatically: ${newUrl}`);
    } else {
        console.warn('⚠️ .env file not found, skipping IP update.');
    }
} catch (error) {
    console.error('❌ Error updating .env:', error);
}

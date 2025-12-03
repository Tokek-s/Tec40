#!/bin/bash

# Script de despliegue para Técnica 40
set -e

echo "🚀 Iniciando despliegue de Técnica 40..."

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Crear estructura de directorios
echo -e "${YELLOW}📁 Creando estructura de directorios...${NC}"
mkdir -p /mnt/data/web/tec40
cd /mnt/data/web/tec40

# 2. Clonar repositorio
if [ ! -d ".git" ]; then
    echo -e "${YELLOW}📥 Clonando repositorio...${NC}"
    git clone https://github.com/Tokek-s/Tec40.git .
else
    echo -e "${YELLOW}🔄 Actualizando repositorio...${NC}"
    git pull
fi

# 3. Crear archivo .env.production
echo -e "${YELLOW}⚙️  Configurando variables de entorno...${NC}"
cat > .env << 'EOF'
APP_NAME="Técnica 40"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tec40.taildc11c4.ts.net

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=tec40-db
DB_PORT=3306
DB_DATABASE=bd_tec_40
DB_USERNAME=tec40_user
DB_PASSWORD=tec40_pass_2024

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF

# 4. Instalar dependencias de Composer
echo -e "${YELLOW}📦 Instalando dependencias de PHP...${NC}"
if ! command -v composer &> /dev/null; then
    echo "Instalando Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi
composer install --no-dev --optimize-autoloader

# 5. Generar APP_KEY
echo -e "${YELLOW}🔑 Generando clave de aplicación...${NC}"
php artisan key:generate --force

# 6. Instalar dependencias de Node y compilar assets
echo -e "${YELLOW}🎨 Compilando assets...${NC}"
npm install
npm run build

# 7. Configurar permisos
echo -e "${YELLOW}🔒 Configurando permisos...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 8. Crear directorio para init.sql
echo -e "${YELLOW}🗄️  Preparando base de datos...${NC}"
mkdir -p database

# 9. Levantar contenedores Docker
echo -e "${YELLOW}🐳 Iniciando contenedores Docker...${NC}"
docker-compose down
docker-compose up -d

# Esperar a que la BD esté lista
echo -e "${YELLOW}⏳ Esperando a que la base de datos esté lista...${NC}"
sleep 15

# 10. Ejecutar migraciones
echo -e "${YELLOW}📊 Ejecutando migraciones...${NC}"
docker exec tec40-app php artisan migrate --force

# 11. Limpiar cache
echo -e "${YELLOW}🧹 Limpiando cache...${NC}"
docker exec tec40-app php artisan config:cache
docker exec tec40-app php artisan route:cache
docker exec tec40-app php artisan view:cache

echo -e "${GREEN}✅ Despliegue completado!${NC}"
echo ""
echo "🌐 Acceso local: http://localhost:8081"
echo "🌐 Acceso Tailscale: https://tec40.taildc11c4.ts.net"
echo ""
echo "📝 Próximos pasos:"
echo "   1. Configurar Caddy para el dominio Tailscale"
echo "   2. Crear usuario administrador"
echo ""

#!/bin/bash

# Script de finalización de despliegue para Técnica 40
# Ejecutar desde: /mnt/data/web/tec40

set -e

echo "=========================================="
echo "  Finalizando Despliegue de Técnica 40"
echo "=========================================="

# Verificar que estamos en el directorio correcto
if [ ! -f "docker-compose.yml" ]; then
    echo "Error: Ejecuta este script desde /mnt/data/web/tec40"
    exit 1
fi

echo ""
echo "1️⃣  Verificando contenedores Docker..."
docker ps | grep tec40

echo ""
echo "2️⃣  Importando schema de base de datos..."
if [ -f "schema_export.sql" ]; then
    docker exec -i tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 < schema_export.sql
    echo "✅ Schema importado correctamente"
else
    echo "⚠️  Archivo schema_export.sql no encontrado"
    echo "   Por favor, transfiere el archivo al servidor primero:"
    echo "   scp schema_export.sql david@100.85.207.81:/mnt/data/web/tec40/"
    exit 1
fi

echo ""
echo "3️⃣  Creando usuario administrador..."
docker exec -i tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 << 'EOSQL'
INSERT INTO usuarios (
    nombre, 
    apellido_paterno, 
    apellido_materno, 
    correo, 
    password, 
    rol,
    created_at,
    updated_at
) VALUES (
    'Admin',
    'Sistema',
    'Técnica40',
    'admin@tecnica40.edu.mx',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'sistemas',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE 
    nombre = VALUES(nombre),
    password = VALUES(password),
    rol = VALUES(rol),
    updated_at = NOW();
EOSQL

echo "✅ Usuario administrador creado"

echo ""
echo "4️⃣  Verificando tablas en la base de datos..."
docker exec tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 -e "SHOW TABLES;"

echo ""
echo "5️⃣  Verificando acceso a la aplicación..."
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" http://localhost:8081

echo ""
echo "=========================================="
echo "  ✅ Despliegue completado exitosamente!"
echo "=========================================="
echo ""
echo "📝 Información de acceso:"
echo "   - URL local: http://100.85.207.81:8081"
echo "   - Usuario admin: admin@tecnica40.edu.mx"
echo "   - Password: password"
echo ""
echo "⚠️  IMPORTANTE: Cambia la contraseña del administrador"
echo ""
echo "📋 Próximos pasos:"
echo "   1. Configura el reverse proxy para Tailscale"
echo "   2. Accede a https://tec40.taildc11c4.ts.net"
echo "   3. Cambia la contraseña del administrador"
echo ""

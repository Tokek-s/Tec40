#!/bin/bash
# Script rápido de despliegue para Técnica 40
# Cópialo manualmente al servidor y ejecútalo

echo "=== Despliegue Técnica 40 - Script Rápido ==="

cd /mnt/data/web/tec40

# Importar schema usando PHP desde el contenedor
echo "Creando tabla de usuarios..."
docker exec -i tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 << 'EOF'
CREATE TABLE IF NOT EXISTS usuarios (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    nombre varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    apellido_paterno varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    apellido_materno varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    correo varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    password varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    rol enum('sistemas','coordinacion','docente','prefectura','vinculacion') COLLATE utf8mb4_unicode_ci NOT NULL,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY usuarios_correo_unique (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF

echo "Crear admin..."
docker exec -i tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 << 'EOF'
INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, correo, password, rol, created_at, updated_at)
VALUES ('Admin', 'Sistema', 'Técnica40', 'admin@tecnica40.edu.mx', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sistemas', NOW(), NOW())
ON DUPLICATE KEY UPDATE password = VALUES(password);
EOF

echo "Ejecutar migraciones desde Laravel..."
docker exec tec40-app php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$status = \$kernel->call('migrate', ['--force' => true]);
echo 'Migraciones ejecutadas con código: ' . \$status . PHP_EOL;
"

echo "Verificar..."
docker exec tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 -e "SHOW TABLES;"

echo ""
echo "=== COMPLETADO ==="
echo "URL: http://100.85.207.81:8081"
echo "Usuario: admin@tecnica40.edu.mx"
echo "Password: password"

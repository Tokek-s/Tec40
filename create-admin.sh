#!/bin/bash

# Script para crear usuario administrador en Técnica 40
set -e

echo "👤 Creando usuario administrador..."

# Conectar al contenedor y ejecutar el script PHP
docker exec -i tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 << 'EOSQL'

-- Crear usuario administrador
INSERT INTO usuarios (
    nombre, 
    apellido_paterno, 
    apellido_materno, 
    correo, 
    password, 
    rol
) VALUES (
    'Administrador',
    'Sistema',
    'Técnica40',
    'admin@tecnica40.edu.mx',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    'sistemas'
) ON DUPLICATE KEY UPDATE 
    nombre = VALUES(nombre),
    password = VALUES(password),
    rol = VALUES(rol);

EOSQL

echo "✅ Usuario administrador creado exitosamente!"
echo ""
echo "📧 Email: admin@tecnica40.edu.mx"
echo "🔑 Password: password"
echo ""
echo "⚠️  IMPORTANTE: Cambia esta contraseña inmediatamente después del primer login!"

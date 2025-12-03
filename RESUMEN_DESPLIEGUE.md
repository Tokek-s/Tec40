# ✅ RESUMEN DEL DESPLIEGUE - Técnica 40

## 🎯 ESTADO ACTUAL (Actualizado: 2025-12-03 11:59)

### ✅ **COMPLETADO:**

1. **Docker Containers corriendo:**
   - ✅ `tec40-db` - MySQL 8.0 en puerto 3307
   - ✅ `tec40-app` - PHP 8.2 + Apache en puerto 8081

2. **Base de datos:**
   - ✅ Base de datos `bd_tec_40` creada
   - ✅ Tabla `usuarios` creada
   - ✅ Usuario administrador creado:
     - Email: `admin@tecnica40.edu.mx`
     - Password: `password`

3. **Configuración Apache:**
   - ✅ Archivo `apache-config.conf` creado
   - ✅ DocumentRoot apuntando a `/var/www/html/public`
   - ✅ Contenedor reiniciado con nueva configuración

4. **Archivos en servidor:**
   - Ubicación: `/mnt/data/web/tec40`
   - `.env` configurado
   - `docker-compose.yml` actualizado

---

## ⏳ **PENDIENTE (Ejecutar manualmente):**

### Paso 1: Conectarse al servidor

```bash
ssh david@100.85.207.81
# Password: a1s2d3f4
```

### Paso 2: Ejecutar migraciones de Laravel

```bash
cd /mnt/data/web/tec40

# Crear script de migración
cat > /tmp/migrate.sh << 'EOF'
#!/bin/bash
docker exec tec40-app php -d memory_limit=512M artisan migrate --force
EOF

chmod +x /tmp/migrate.sh
/tmp/migrate.sh
```

**Alternativa si artisan no funciona:**

```bash
# Importar schema completo desde tu máquina local
# 1. Desde Windows (tu máquina):
scp C:\Users\david\OneDrive\Escritorio\Tecnica40\schema_export.sql david@100.85.207.81:/tmp/

# 2. En el servidor:
docker exec -i tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 < /tmp/schema_export.sql
```

### Paso 3: Verificar que la aplicación funcione

```bash
# Desde el servidor
curl http://localhost:8081

# Debería mostrar HTML de Laravel, no "Forbidden"
```

### Paso 4: Verificar tablas creadas

```bash
docker exec tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 -e "SHOW TABLES;"
```

### Paso 5: Limpiar contenedores del simulador (si existen)

```bash
# Ver todos los contenedores
docker ps -a

# Detener y eliminar contenedores del simulador
# (SOLO si hay contenedores que no sean nextcloud ni tec40)
docker stop <nombre_contenedor_simulador>
docker rm <nombre_contenedor_simulador>

# Eliminar imágenes no utilizadas
docker image prune -a
```

---

## 🌐 **ACCESO A LA APLICACIÓN:**

### Local (desde el servidor):
```
http://localhost:8081
```

### Desde tu red local:
```
http://100.85.207.81:8081
```

### Tailscale (requiere configurar reverse proxy):
```
https://tec40.taildc11c4.ts.net
```

---

## 🔧 **CONFIGURAR REVERSE PROXY (Opcional):**

Si tienes nginx-proxy-manager (que ya está corriendo):

1. Accede a nginx-proxy-manager
2. Añade un Proxy Host:
   - **Domain Names**: `tec40.taildc11c4.ts.net`
   - **Forward Hostname/IP**: `tec40-app` (o `localhost`)
   - **Forward Port**: `80` (puerto interno del contenedor)
   - **Enable SSL**: Sí (Let's Encrypt o certificado de Tailscale)

---

## 🐛 **TROUBLESHOOTING:**

### Si ves "Forbidden":
```bash
docker exec tec40-app ls -la /var/www/html/public
# Debe existir el directorio public con index.php

docker logs tec40-app | tail -50
# Ver errores de Apache
```

### Si la base de datos no tiene tablas:
```bash
# Opción 1: Ejecutar migraciones
docker exec tec40-app php artisan migrate --force

# Opción 2: Importar schema completo
docker exec -i tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 < /tmp/schema_export.sql
```

### Reiniciar contenedores:
```bash
cd /mnt/data/web/tec40
docker compose restart
```

---

## 📊 **ARCHIVOS IMPORTANTES:**

En tu máquina local (Windows):
- `schema_export.sql` - Base de datos completa
- `EJECUTAR_MANUALMENTE.md` - Pasos detallados
- `quick-deploy.sh` - Script automatizado
- `apache-config.conf` - Configuración de Apache

En el servidor (`/mnt/data/web/tec40`):
- `docker-compose.yml` - Configuración de containers
- `.env` - Variables de entorno
- `apache-config.conf` - Configuración de Apache

---

## ⚠️ **IMPORTANTE:**

1. **Cambiar contraseña del admin** después del primer login
2. **Configurar backups** de la base de datos
3. **No eliminar** contenedores de Nextcloud
4. El error "Forbidden" se debía a que Apache no apuntaba a `/var/www/html/public`

---

## 📝 **CREDENCIALES:**

**Aplicación:**
- URL: http://100.85.207.81:8081
- Usuario: admin@tecnica40.edu.mx
- Password: password

**Base de datos:**
- Host: tec40-db (o 100.85.207.81:3307 desde fuera)
- Database: bd_tec_40
- User: tec40_user
- Password: tec40_pass_2024

---

**El despliegue está al 90% completo. Solo falta ejecutar las migraciones y verificar que todo funcione correctamente.**

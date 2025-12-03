# 🚀 Guía de Despliegue en Servidor

## Información del Servidor
- **Host**: 100.85.207.81
- **Usuario**: david  
- **Carpeta**: /mnt/data/web/tec40
- **Dominio Tailscale**: tec40.taildc11c4.ts.net

## Puertos Utilizados
- **Base de Datos (MySQL)**: 3307 (externo), 3306 (interno contenedor)
- **Aplicación Web**: 8081 (HTTP)
- **Dominio Tailscale**: 443 (HTTPS vía Caddy)

## 📦 Pasos de Despliegue

### 1. Conectarse al Servidor
```bash
ssh david@100.85.207.81
```

### 2. Ejecutar Script de Despliegue Automático
```bash
# Descargar y ejecutar el script
curl -O https://raw.githubusercontent.com/Tokek-s/Tec40/main/deploy.sh
chmod +x deploy.sh
sudo ./deploy.sh
```

### 3. Configurar Caddy para Tailscale

```bash
# Copiar el Caddyfile
sudo cp /mnt/data/web/tec40/Caddyfile /etc/caddy/Caddyfile.d/tec40

# O agregar al Caddyfile principal
sudo nano /etc/caddy/Caddyfile
# Pegar el contenido del archivo Caddyfile

# Recargar Caddy
sudo systemctl reload caddy
```

### 4. Crear Usuario Administrador
```bash
cd /mnt/data/web/tec40
chmod +x create-admin.sh
./create-admin.sh
```

**Credenciales por defecto**:
- Email: `admin@tecnica40.edu.mx`
- Password: `password`

⚠️ **IMPORTANTE**: Cambiar la contraseña después del primer login.

### 5. Verificar el Despliegue
```bash
# Ver logs de los contenedores
docker logs tec40-app
docker logs tec40-db

# Verificar que los contenedores estén corriendo
docker ps | grep tec40

# Probar conexión a la base de datos
docker exec -it tec40-db mysql -u tec40_user -p
# Password: tec40_pass_2024
```

## 🔧 Comandos Útiles

### Gestión de Contenedores
```bash
# Reiniciar todos los servicios
cd /mnt/data/web/tec40
docker-compose restart

# Ver logs en tiempo real
docker-compose logs -f

# Detener servicios
docker-compose down

# Iniciar servicios
docker-compose up -d
```

### Actualizar Código
```bash
cd /mnt/data/web/tec40
git pull
docker exec tec40-app composer install --no-dev
docker exec tec40-app npm run build
docker exec tec40-app php artisan migrate --force
docker exec tec40-app php artisan config:cache
docker-compose restart tec40-app
```

### Backup de Base de Datos
```bash
# Crear backup
docker exec tec40-db mysqldump -u tec40_user -ptec40_pass_2024 bd_tec_40 > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
docker exec -i tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 < backup.sql
```

### Acceder a la Aplicación
```bash
# Ejecutar comandos de Artisan
docker exec tec40-app php artisan [comando]

# Acceder al contenedor
docker exec -it tec40-app bash

# Limpiar cache
docker exec tec40-app php artisan cache:clear
docker exec tec40-app php artisan config:clear
docker exec tec40-app php artisan route:clear
docker exec tec40-app php artisan view:clear
```

## 🌐 URLs de Acceso

- **Desarrollo/Local**: http://100.85.207.81:8081
- **Producción (Tailscale)**: https://tec40.taildc11c4.ts.net

## 🔒 Seguridad

### Variables de Entorno Sensibles
El archivo `.env` contiene información sensible. Asegúrate de:
- No subirlo al repositorio
- Usar contraseñas fuertes en producción
- Configurar `APP_DEBUG=false`

### Firewall
Si es necesario, abre los puertos:
```bash
sudo ufw allow 8081/tcp
sudo ufw allow 3307/tcp  # Solo si necesitas acceso externo a la BD
```

## 📊 Monitoreo

### Salud de los Servicios
```bash
# CPU y memoria
docker stats tec40-app tec40-db

# Espacio en disco
df -h /mnt/data/web/tec40
docker system df
```

## 🐛 Resolución de Problemas

### La aplicación no carga
```bash
# Verificar logs
docker logs tec40-app -n 100

# Verificar permisos
docker exec tec40-app chown -R www-data:www-data storage bootstrap/cache
docker exec tec40-app chmod -R 775 storage bootstrap/cache
```

### Error de conexión a BD
```bash
# Verificar que la BD esté corriendo
docker ps | grep tec40-db

# Probar conexión
docker exec tec40-db mysql -u tec40_user -ptec40_pass_2024 -e "SELECT 1"
```

### Actualización de dependencias
```bash
cd /mnt/data/web/tec40
docker exec tec40-app composer update
docker exec tec40-app npm update
docker exec tec40-app npm run build
docker-compose restart tec40-app
```

## 📝 Notas Adicionales

- Los datos de la BD se persisten en el volumen Docker `tec40-db-data`
- Los logs de Caddy están en `/var/log/caddy/tec40-access.log`
- El proyecto usa MariaDB 10.5 (compatible con MySQL 8.0)
- El contenedor PHP incluye todas las extensiones necesarias para Laravel

---

**Contacto**: Para soporte técnico, contactar al administrador del sistema.

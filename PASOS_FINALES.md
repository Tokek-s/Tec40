# 🚀 PASOS FINALES DE DESPLIEGUE - Técnica 40

## ⚠️ El servidor no responde actualmente

Hay un problema de conexión con el servidor `100.85.207.81`. Una vez que se restablezca la conexión, sigue estos pasos:

---

## 📦 Paso 1: Transferir archivos al servidor

```bash
# Desde tu máquina local
scp schema_export.sql david@100.85.207.81:/mnt/data/web/tec40/
scp finish-deployment.sh david@100.85.207.81:/mnt/data/web/tec40/
```

---

## 🔧 Paso 2: Ejecutar script de finalización

```bash
# Conectarse al servidor
ssh david@100.85.207.81

# Ir al directorio del proyecto
cd /mnt/data/web/tec40

# Dar permisos de ejecución
chmod +x finish-deployment.sh

# Ejecutar el script
./finish-deployment.sh
```

Este script hará automáticamente:
- ✅ Importar el schema de la base de datos (21 tablas)
- ✅ Crear el usuario administrador
- ✅ Verificar que todo funcione correctamente

---

## 🌐 Paso 3: Configurar Reverse Proxy para Tailscale

### Opción A: Si tienes Caddy instalado

```bash
# Instalar Caddy (si no está instalado)
sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list
sudo apt update
sudo apt install caddy

# Configurar Caddy
sudo nano /etc/caddy/Caddyfile
```

Agregar esta configuración:

```
tec40.taildc11c4.ts.net {
    reverse_proxy localhost:8081
    
    header {
        Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
        X-Content-Type-Options "nosniff"
        X-Frame-Options "SAMEORIGIN"
        X-XSS-Protection "1; mode=block"
    }
    
    log {
        output file /var/log/caddy/tec40-access.log
        format json
    }
    
    encode gzip
}
```

```bash
# Recargar Caddy
sudo systemctl reload caddy
```

### Opción B: Si tienes Nginx

```bash
# Crear configuración para Nginx
sudo nano /etc/nginx/sites-available/tec40
```

Contenido:

```nginx
server {
    listen 80;
    server_name tec40.taildc11c4.ts.net;

    location / {
        proxy_pass http://localhost:8081;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

```bash
# Habilitar el sitio
sudo ln -s /etc/nginx/sites-available/tec40 /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## ✅ Verificación Final

Una vez completados los pasos:

1. **Acceder localmente**: `http://100.85.207.81:8081`
2. **Acceder por Tailscale**: `https://tec40.taildc11c4.ts.net`

**Credenciales de administrador**:
- Email: `admin@tecnica40.edu.mx`
- Password: `password`

⚠️ **IMPORTANTE**: Cambia la contraseña inmediatamente después del primer login.

---

## 🐛 Resolución de problemas

### La aplicación no carga

```bash
# Ver logs de Docker
docker logs tec40-app
docker logs tec40-db

# Reiniciar contenedores
cd /mnt/data/web/tec40
docker-compose restart
```

### Error de base de datos

```bash
# Verificar conexión
docker exec tec40-db mysql -u tec40_user -ptec40_pass_2024 -e "SELECT 1"

# Ver tablas
docker exec tec40-db mysql -u tec40_user -ptec40_pass_2024 bd_tec_40 -e "SHOW TABLES;"
```

### Problema con el reverse proxy

```bash
# Verificar que el puerto 8081 está escuchando
curl http://localhost:8081

# Ver logs de Caddy
sudo journalctl -u caddy -f

# Ver logs de Nginx
sudo tail -f /var/log/nginx/error.log
```

---

## 📊 Estado Actual

### ✅ Completado
- Proyecto en GitHub
- Código en servidor
- Docker containers corriendo
- Base de datos MySQL creada
- Configuración `.env` lista
- APP_KEY generada

### ⏳ Pendiente (cuando recuperes conexión)
1. Transferir `schema_export.sql`
2. Ejecutar `finish-deployment.sh`
3. Configurar reverse proxy
4. Acceder y cambiar password admin

---

## 📞 Soporte

Si necesitas ayuda adicional, verifica:
1. Que el servidor esté encendido
2. Que SSH esté accesible
3. Que los contenedores Docker estén corriendo
4. Que el firewall permita el puerto 8081

Una vez restablecida la conexión, los scripts están listos para ejecutarse automáticamente.

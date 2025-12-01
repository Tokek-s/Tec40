# 🚀 Guía de Instalación - Sistema de Gestión Escolar Técnica 40

## Requisitos Previos
- PHP 8.2 o superior
- Composer
- Node.js y npm
- MySQL o MariaDB
- Git

## 📦 Pasos de Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/Tokek-s/Tec40.git
cd Tec40
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Instalar dependencias de Node.js
```bash
npm install
```

### 4. Configurar el archivo de entorno
```bash
# Copiar el archivo de ejemplo
copy .env.example .env

# O en Linux/Mac:
# cp .env.example .env
```

### 5. Editar el archivo `.env`
Abrir el archivo `.env` y configurar:
```env
APP_NAME="Técnica 40"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bd_tec_40
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 6. Generar la clave de aplicación
```bash
php artisan key:generate
```

### 7. Crear la base de datos
Crear la base de datos en MySQL:
```sql
CREATE DATABASE bd_tec_40;
```

### 8. Ejecutar las migraciones
```bash
php artisan migrate
```

### 9. Ejecutar el servidor de desarrollo

**Opción 1: Comando único (Recomendado)**
```bash
composer run dev
```
Este comando ejecuta automáticamente:
- Laravel server en `http://0.0.0.0:8000` (accesible desde la red local)
- Vite dev server para assets
- Queue listener
- Detecta automáticamente tu IP local

**Opción 2: Servidores separados**
```bash
# Terminal 1: Laravel
php artisan serve --host=0.0.0.0

# Terminal 2: Vite
npm run dev
```

### 10. Acceder a la aplicación
- **Desde la misma PC**: http://localhost:8000
- **Desde otro dispositivo en la red**: http://TU_IP_LOCAL:8000
  - Ejemplo: http://192.168.1.102:8000

## 🔐 Crear Usuario Administrador (Opcional)

Puedes usar el script incluido:
```bash
php crear_usuario_admin.php
```

O crear uno manualmente en la base de datos.

## 📝 Notas Importantes

- **Firewall**: Asegúrate de permitir conexiones en los puertos 8000 y 5173 si quieres acceder desde otros dispositivos.
- **Permisos**: En Linux/Mac, asegúrate de dar permisos a las carpetas `storage` y `bootstrap/cache`:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```
- **IP Dinámica**: El sistema detecta automáticamente tu IP local, pero si cambia, solo reinicia el servidor.

## 🛠️ Comandos Útiles

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Compilar assets para producción
npm run build

# Ver logs en tiempo real (solo en Linux/Mac)
tail -f storage/logs/laravel.log
```

## ❓ Problemas Comunes

### Error: "No application encryption key"
```bash
php artisan key:generate
```

### Error: "Access denied for user"
Verifica las credenciales de la base de datos en el archivo `.env`

### Pantalla negra en dispositivos externos
1. Verifica que el firewall permita conexiones
2. Asegúrate de usar la IP correcta (no localhost)
3. Reinicia el servidor con `composer run dev`

---

**¿Necesitas ayuda?** Contacta al equipo de desarrollo.

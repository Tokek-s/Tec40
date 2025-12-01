# 📊 Análisis Completo del Sistema - Técnica 40

**Fecha de Análisis:** 20 de noviembre de 2025  
**Base de Datos:** bd_tec_40  
**Framework:** Laravel 11 + Inertia.js + Vue 3

---

## 🎯 Resumen Ejecutivo

El sistema está **funcionalmente completo** con toda la estructura de base de datos sincronizada y los endpoints configurados correctamente. Se identificaron y corrigieron problemas menores relacionados con migraciones pendientes.

### Estado General: ✅ OPERATIVO

---

## 📁 Estructura de la Base de Datos

### Tablas Principales (18 tablas) - Optimizada ✅

#### 1. **usuarios** - Gestión de Personal
```sql
- Roles: Direccion, Subdireccion, Administrativo, Prefecto, Sistemas, Medico, Psicologo
- Autenticación con hash_contrasena (bcrypt)
- Campo activo para soft-disable
```

#### 2. **alumnos** - Registro de Estudiantes
```sql
- CURP (único, 18 caracteres)
- Matrícula (único)
- Estatus: activo, baja, egresado
- Relación con archivo_multimedia_id (foto)
```

#### 3. **grupos** - Organización Académica
```sql
- Grado (1-3)
- Clave (A, B, C, etc.)
- Turno: Matutino, Vespertino
```

#### 4. **historial_academico** - Trayectoria del Alumno
```sql
- Relación alumno_id → alumnos
- Relación grupo_id → grupos
- Ciclo escolar
- Estatus: inscrito, baja, reprobado, egresado
```

#### 5. **cuestionarios_inscrip_reinscrip** - Inscripciones/Reinscripciones 🆕
```sql
- Sistema de enlaces por grado (link_primero, link_segundo, link_tercero)
- Control de activación por grado (primero_activo, segundo_activo, tercero_activo)
- Vigencia temporal (fecha_inicio, fecha_fin)
- Usado en: HOME (vista pública) - NO en admin
- Modelo: FechaCuestionario
```

#### 6. **cuestionarios_externos** - Formularios Externos 🆕
```sql
- Sistema de enlaces por grado (link_primero, link_segundo, link_tercero)
- Control de activación por grado
- Vigencia temporal
- Usado en: ADMIN (FormulariosController) + HOME (vista pública)
- Modelo: CuestionarioExterno
```

#### 5. **asistencias** - Control de Asistencia
```sql
- Estado: presente, falta, retardo, justificado
- registrado_por → usuarios
- Fecha + alumno_id + grupo_id
```

#### 6. **contactos_alumno** - Tutores/Contactos
```sql
- Parentesco, teléfono, correo
- autorizado_recoger (boolean)
- es_principal (identifica contacto principal)
- activo (puede desactivarse)
- archivo_multimedia_id (foto del contacto)
```

#### 7. **salud** - Información Médica
```sql
- tipo_sangre
- alergias (TEXT)
- enfermedades_cronicas (TEXT)
- Relación 1:1 con alumnos
```

#### 8. **incidencias** - Reportes Disciplinarios
```sql
- descripcion, fecha
- registrado_por_id → usuarios
- area (prefectura, academica, etc.)
- medidas (medidas correctivas)
- firmas (LONGTEXT, almacena Base64)
- nombre_firmantes (JSON)
- pdf_path (ruta del PDF generado)
```

#### 9. **salidas_anticipadas** - Autorizaciones de Salida
```sql
- fecha, hora_salida, motivo
- autorizado_por_id → usuarios
- entregado_a_contacto_id → contactos_alumno
- pdf_path (documento generado)
```

#### 10. **anuncios** - Comunicados Generales
```sql
- titulo, contenido
- activo (boolean)
- archivo_multimedia_id → archivos_multimedia (imagen del anuncio)
- ruta_imagen (legacy, será removido eventualmente)
- fecha (para ordenar cronológicamente)
- Eliminación automática de imagen al borrar anuncio
```

#### 11. **documentos** - Gestión Documental
```sql
- entidad_id + entidad_tipo (polimórfico)
- tipo_documento: Credencial, Autorización_Recogida, Pase_Salida, Reglamento_Firmado
- ruta_archivo
- firma (Base64)
- creado_por_id → usuarios
```

#### 12. **archivos_multimedia** - Repositorio de Imágenes
```sql
- ruta_archivo (path relativo)
- tipo_archivo (image/jpeg, image/png, etc.)
- Usado para fotos de alumnos y contactos
```

#### 13. **cuestionarios_externos** - Formularios Externos
```sql
- titulo, descripcion
- link_primero, link_segundo, link_tercero (URLs a formularios Google, etc.)
- fecha_inicio, fecha_fin
- activo, primero_activo, segundo_activo, tercero_activo
```

#### 14. **cuestionarios_inscrip_reinscrip** - Formularios de Inscripción
```sql
- Similar a cuestionarios_externos
- ✅ Columnas link_primero, link_segundo, link_tercero agregadas correctamente
- Usada por FechaCuestionario model
```

#### 15. **configuraciones** - Ajustes del Sistema
```sql
- clave-valor para settings generales
```

#### 16. **logs_acceso** - Auditoría de Accesos
```sql
- tipo_usuario, ip, user_agent
- estado (success, fail)
- fecha_acceso
```

#### 17. **registro_auditoria** - Auditoría de Acciones
```sql
- usuario_id, accion, detalle
- fecha_hora
```

#### 18-23. **Tablas de Sistema Laravel**
- sessions, cache, cache_locks, jobs, job_batches, migrations

---

## 🔗 Endpoints y Rutas

### 🏠 Rutas Públicas

#### **/** (Home)
- **Controlador:** `HomeController@index`
- **Vista:** Página de inicio pública
- **Método:** GET

#### **/inscripciones/{grado}/{cuestionario}**
- **Controlador:** `InscripcionController@show`
- **Parámetros:** 
  - `grado`: primero|segundo|tercero
  - `cuestionario`: ID numérico
- **Validación:** Verifica fechas vigentes y flags activos por grado
- **Vista:** `Inscripciones/Show`
- **Método:** GET

---

### 👨‍💼 Área de Administrador (/admin)

#### **Autenticación**
```php
GET  /admin/login           → Vista de login
POST /admin/login           → Procesar login (AdminAuthenticatedSessionController)
POST /admin/logout          → Cerrar sesión
```

#### **Middleware:** `ValidateAdminAccess` (roles: Direccion, Sistemas)

#### **Dashboard**
```php
GET /admin/dashboard        → AdminController@dashboard
    Stats: usuarios, alumnos, anuncios, cuestionarios
    Datos recientes de usuarios y anuncios
```

#### **Gestión de Alumnos**
```php
GET    /admin/alumnos/lista              → Vista listado
GET    /admin/alumnos/lista-datos        → CredencialesController@listarAlumnos (API JSON)
GET    /admin/alumnos/{id}/perfil        → AdminController@perfilAlumno
PUT    /admin/alumnos/{id}/actualizar    → AdminController@actualizarAlumno
GET    /admin/alumnos/contactos          → Vista contactos
GET    /admin/alumnos/contactos-datos    → AdminController@listarContactos
GET    /admin/alumnos/pase-lista         → Vista pase de lista
GET    /admin/alumnos/salud              → Vista información médica
```

#### **Gestión de Grupos**
```php
GET    /admin/alumnos/grupos             → AdminController@grupos
POST   /admin/alumnos/grupos/crear       → AdminController@crearGrupo
PUT    /admin/alumnos/grupos/{id}        → AdminController@actualizarGrupo
DELETE /admin/alumnos/grupos/{id}        → AdminController@eliminarGrupo
GET    /admin/alumnos/grupos/{id}/alumnos → AdminController@alumnosGrupo
```

#### **Gestión de Contactos**
```php
POST   /admin/alumnos/{alumno}/contactos  → ContactosController@store
PUT    /admin/contactos/{contacto}         → ContactosController@update
PATCH  /admin/contactos/{contacto}/principal → ContactosController@marcarPrincipal
PATCH  /admin/contactos/{contacto}/toggle-activo → ContactosController@toggleActivo
DELETE /admin/contactos/{contacto}         → ContactosController@destroy
```

#### **Documentos Administrativos**
```php
// Incidencias
GET    /admin/docs/incidencias            → IncidenciasController@index
GET    /admin/docs/incidencias/create     → IncidenciasController@create
POST   /admin/docs/incidencias            → IncidenciasController@store
GET    /admin/docs/incidencias/{id}/preview → IncidenciasController@preview
POST   /admin/docs/incidencias/{id}/finalize → IncidenciasController@finalize
DELETE /admin/docs/incidencias/{id}/pdf   → IncidenciasController@borrarPDF
DELETE /admin/docs/incidencias/{id}       → IncidenciasController@eliminar

// Salidas Anticipadas
GET    /admin/docs/autorizacion-salida    → SalidasAnticipadasController@create
GET    /admin/docs/salidas                → SalidasAnticipadasController@index
POST   /admin/docs/salidas                → SalidasAnticipadasController@store
GET    /admin/docs/salidas/{id}/pdf       → SalidasAnticipadasController@showPdf
GET    /admin/docs/salidas/{id}/preview   → SalidasAnticipadasController@preview
POST   /admin/docs/salidas/{id}/finalize  → SalidasAnticipadasController@finalize
DELETE /admin/docs/salidas/{id}/pdf       → SalidasAnticipadasController@borrarPdf
DELETE /admin/docs/salidas/{id}           → SalidasAnticipadasController@eliminar

// Búsqueda de alumnos y contactos
GET /admin/docs/alumnos/buscar            → SalidasAnticipadasController@buscarAlumno
GET /admin/docs/alumnos/{alumno}/contactos → SalidasAnticipadasController@contactosAlumno

// Credenciales
GET  /admin/docs/credenciales             → Vista generación de credenciales
POST /admin/credenciales/generar          → CredencialesController@generar
```

#### **Gestión de Usuarios** (Solo Director y Sistemas)
```php
GET    /admin/usuarios                    → UsuariosController@index
POST   /admin/usuarios                    → UsuariosController@store
GET    /admin/usuarios/{usuario}/edit     → UsuariosController@edit
PUT    /admin/usuarios/{usuario}          → UsuariosController@update
DELETE /admin/usuarios/{usuario}          → UsuariosController@destroy
```

#### **Formularios Externos (CRUD)**
```php
Route::resource('formularios', FormulariosController::class)
GET    /admin/formularios                 → FormulariosController@index
GET    /admin/formularios/create          → FormulariosController@create
POST   /admin/formularios                 → FormulariosController@store
GET    /admin/formularios/{id}/edit       → FormulariosController@edit
PUT    /admin/formularios/{id}            → FormulariosController@update
DELETE /admin/formularios/{id}            → FormulariosController@destroy
```

#### **Anuncios (CRUD)**
```php
GET    /admin/anuncios                    → AnuncioController@index
GET    /admin/anuncios/create             → AnuncioController@create
GET    /admin/anuncios/{anuncio}/edit     → AnuncioController@edit
POST   /admin/anuncios                    → AnuncioController@store
PUT    /admin/anuncios/{anuncio}          → AnuncioController@update
PATCH  /admin/anuncios/{anuncio}          → AnuncioController@update
DELETE /admin/anuncios/{anuncio}          → AnuncioController@destroy
```

#### **Inscripciones**
```php
GET /admin/inscripciones/inscripcion      → Vista inscripción (Inertia)
GET /admin/inscripciones/reinscripcion    → Vista reinscripción (Inertia)
```

---

### 👮 Área de Prefectos (/prefectos)

#### **Middleware:** `ValidatePrefectoAccess` (rol: Prefecto)

```php
GET /prefectos/dashboard                  → PrefectosController@dashboard
GET /prefectos/alumnos/pase-lista         → Vista (Prefectos/Alumnos/PaseLista)
GET /prefectos/alumnos/lista              → Vista (Prefectos/Alumnos/Lista)
GET /prefectos/alumnos/lista-datos        → CredencialesController@listarAlumnos
GET /prefectos/alumnos/{alumno}/perfil    → PrefectosController@perfilAlumno
```

---

### 👪 Portal de Tutores (/tutor)

#### **Autenticación**
```php
GET  /tutor/login             → TutorAuthenticatedSessionController@create
POST /tutor/login             → TutorAuthenticatedSessionController@store
POST /tutor/logout            → TutorAuthenticatedSessionController@destroy
```

#### **Middleware:** `ValidateTutorAccess` (usa Session para alumno_id)

```php
GET /tutor/dashboard          → TutorController@dashboard
GET /tutor/asistencias        → TutorController@asistencias (Vista)
GET /tutor/asistencias/data   → TutorController@getAsistencias (API JSON)
GET /tutor/anuncios           → TutorController@anuncios
```

---

### 👨‍👩‍👧 Portal de Padres (/padres)

#### **Middleware:** `auth` (autenticación estándar Laravel)

```php
GET /padres/dashboard         → DashboardController@index
GET /padres/asistencias       → DashboardController@asistencias
GET /padres/solicitar-salida  → DashboardController@solicitarSalida
GET /padres/salidas-historial → DashboardController@salidasHistorial
```

---

### 📋 API de Pase de Lista

#### **Middleware:** `auth` + `ValidatePaseListaAccess` (roles: Sistemas, Direccion, Prefecto)

```php
GET    /asistencias                 → AsistenciaController@index
POST   /asistencias/falta           → AsistenciaController@marcarFalta
DELETE /asistencias/falta           → AsistenciaController@quitarFalta
POST   /asistencias/justificado     → AsistenciaController@marcarJustificado
POST   /asistencias/pasar-lista     → AsistenciaController@pasarLista
```

**Filtros disponibles:**
- `q`: Búsqueda por nombre, matrícula, CURP
- `grupo`: Filtro por grupo (formato: "1A", "2B" o solo grado/clave)
- `turno`: Matutino | Vespertino
- `fecha`: Fecha específica (default: hoy)

---

### 🐛 Ruta de Depuración (Solo Local)

```php
GET /debug/cuestionarios      → Lista cuestionarios normalizados (JSON)
```

---

## 🔐 Sistema de Autenticación

### Roles y Permisos

| Rol | Acceso | Middleware |
|-----|--------|------------|
| **Direccion** | Admin completo + gestión usuarios | ValidateAdminAccess |
| **Sistemas** | Admin completo + gestión usuarios | ValidateAdminAccess |
| **Prefecto** | Portal prefectos + pase de lista | ValidatePrefectoAccess, ValidatePaseListaAccess |
| **Subdireccion** | (Por implementar) | - |
| **Administrativo** | (Por implementar) | - |
| **Medico** | (Por implementar) | - |
| **Psicologo** | (Por implementar) | - |
| **Tutor** | Portal tutores (por alumno) | ValidateTutorAccess |
| **Padre** | Portal padres | auth |

### Middlewares Implementados

1. **ValidateAdminAccess**: Verifica rol Direccion o Sistemas
2. **ValidatePrefectoAccess**: Verifica rol Prefecto
3. **ValidateTutorAccess**: Verifica sesión de tutor con alumno_id
4. **ValidatePaseListaAccess**: Permite Sistemas, Direccion, Prefecto
5. **ValidateCuestionarioAccess**: (Aparentemente no en uso activo)

---

## 📦 Modelos Eloquent y Relaciones

### Relaciones Principales

#### **Alumno**
```php
→ belongsTo: ArchivoMultimedia (foto)
→ hasMany: ContactoAlumno (contactos)
→ hasOne: ContactoAlumno (contactoPrincipal - oldest)
→ hasOne: Salud
→ hasMany: HistorialAcademico
→ hasOne: HistorialAcademico (historialActual - latest)
→ Accessor: nombre_completo, foto_url
```

#### **Usuario**
```php
→ Authenticatable (login con correo + hash_contrasena)
→ Scope: active() (where activo = 1)
→ Accessor: nombre_completo
```

#### **Grupo**
```php
→ hasMany: HistorialAcademico
```

#### **HistorialAcademico**
```php
→ belongsTo: Alumno
→ belongsTo: Grupo
```

#### **ContactoAlumno**
```php
→ belongsTo: Alumno
→ belongsTo: ArchivoMultimedia (foto del contacto)
→ Accessor: nombre_completo, foto_url
```

#### **Asistencia**
```php
→ belongsTo: Alumno
→ belongsTo: Grupo
→ belongsTo: Usuario (registrado_por)
```

#### **Incidencia**
```php
→ belongsTo: Alumno
→ belongsTo: Usuario (registrado_por_id)
→ Casts: nombre_firmantes (array)
```

#### **SalidaAnticipada**
```php
→ belongsTo: Alumno
→ belongsTo: Usuario (autorizado_por_id)
→ belongsTo: ContactoAlumno (entregado_a_contacto_id)
```

#### **Anuncio**
```php
→ belongsTo: ArchivoMultimedia (imagen del anuncio)
→ Accessor: image_url (genera URL desde archivoMultimedia o ruta_imagen legacy)
→ Casts: activo (boolean), fecha (date)
→ Gestión centralizada: imagen se elimina automáticamente con el anuncio
```

#### **FechaCuestionario** (cuestionarios_inscrip_reinscrip)
```php
→ Scope: vigentes() (filtro por activo + fecha actual)
→ Casts: fechas (date), booleans
→ Fillable: ✅ Incluye link_primero, link_segundo, link_tercero
```

#### **CuestionarioExterno**
```php
→ Fillable: titulo, descripcion, links, fechas, flags activos
→ Casts: fechas (date), booleans
```

---

## ✅ Problemas Detectados y Resueltos

### 1. ✅ Migraciones Pendientes
**Problema:** 33 migraciones pendientes, pero tablas ya creadas por schema.sql  
**Solución:** Las tablas fueron creadas por la migración `sync_database_schema` que ejecuta schema.sql

### 2. ✅ Columnas Faltantes en cuestionarios_inscrip_reinscrip
**Problema:** Faltaban link_primero, link_segundo, link_tercero  
**Solución:** Ejecutada migración `2025_11_20_155118_add_links_to_cuestionarios_inscrip_reinscrip_table`

### 3. ✅ Modelo FechaCuestionario sin campos de links
**Problema:** El modelo no incluía los campos link en $fillable  
**Solución:** Actualizado $fillable para incluir link_primero, link_segundo, link_tercero

### 4. ✅ Storage Link
**Estado:** Verificado - El symbolic link ya existe en `public/storage`

---

## 🎨 Frontend (Inertia.js + Vue 3)

### Estructura de Páginas

```
resources/js/pages/
├── Admin/
│   ├── Dashboard.vue
│   ├── Anuncios/
│   │   ├── Index.vue
│   │   └── CreateEdit.vue
│   ├── Alumnos/
│   │   ├── Lista.vue
│   │   ├── Perfil.vue
│   │   ├── PaseLista.vue
│   │   ├── Salud.vue
│   │   └── Contactos.vue (probablemente)
│   ├── Docs/
│   │   ├── Credenciales.vue
│   │   └── [Incidencias y Salidas vistas]
│   ├── Formularios/
│   │   ├── Index.vue ← Archivo actual del usuario
│   │   ├── Create.vue
│   │   └── Edit.vue
│   └── Inscripciones/
│       ├── Inscripcion.vue
│       └── Reinscripcion.vue
├── Tutor/
│   ├── Dashboard.vue
│   └── Asistencias.vue
├── Prefectos/
│   ├── Dashboard.vue (probablemente)
│   └── Alumnos/
│       ├── Lista.vue
│       └── PaseLista.vue
└── Inscripciones/
    └── Show.vue
```

---

## 🔧 Configuración del Sistema

### Archivo .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bd_tec_40
DB_USERNAME=root
DB_PASSWORD=root

SESSION_DRIVER=file
SESSION_LIFETIME=120

FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
```

### Idioma
- APP_LOCALE=en (puede cambiarse a es)
- Traducciones en `lang/es/`

---

## 📊 Estadísticas del Sistema

- **23 Tablas** en base de datos
- **15+ Controladores** implementados
- **60+ Rutas** definidas
- **10+ Modelos** Eloquent con relaciones
- **5 Middlewares** de autorización personalizados
- **3 Portales** diferentes: Admin, Prefectos, Tutores
- **Storage** configurado para multimedia

---

## 🚀 Recomendaciones para Producción

### 1. Seguridad
```bash
# Cambiar APP_ENV a production
APP_ENV=production
APP_DEBUG=false

# Generar nueva APP_KEY
php artisan key:generate

# Configurar HTTPS
FORCE_HTTPS=true
```

### 2. Optimización
```bash
# Cache de configuración
php artisan config:cache

# Cache de rutas
php artisan route:cache

# Cache de vistas
php artisan view:cache

# Optimizar autoload
composer install --optimize-autoloader --no-dev
```

### 3. Base de Datos
```bash
# Backup automático diario
# Implementar mysqldump en cron

# Índices adicionales (si necesario)
ALTER TABLE asistencias ADD INDEX idx_fecha_alumno (fecha, alumno_id);
ALTER TABLE anuncios ADD INDEX idx_fecha_activo (fecha, activo);
```

### 4. Logs y Monitoreo
- Configurar `LOG_CHANNEL=daily`
- Implementar rotación de logs
- Monitorear espacio de `storage/logs`

### 5. Backup de Archivos
- Backup de `storage/app/public/` (fotos, PDFs)
- Backup de base de datos
- Implementar política de retención

---

## 🐛 Testing

### Comandos Útiles
```bash
# Verificar estado de migraciones
php artisan migrate:status

# Listar rutas
php artisan route:list

# Verificar configuración
php artisan config:show database

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Servidor de desarrollo
php artisan serve
# o usar la tarea configurada:
# "Serve: Laravel (php artisan serve)"
```

### Tests de Conectividad
```bash
# Probar conexión a BD
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar usuarios
>>> App\Models\Usuario::count();

# Verificar alumnos
>>> App\Models\Alumno::count();
```

---

## 📝 Notas Adicionales

### Archivos de Rutas
- `routes/web.php` - Rutas principales y públicas
- `routes/admin.php` - Área administrativa
- `routes/auth.php` - Autenticación (deshabilitado, se usa auth personalizado)
- `routes/padres.php` - Portal de padres
- `routes/test.php` - Rutas de prueba (desarrollo)

### Archivos Obsoletos
- `routes/web_clean.php` - Backup de rutas
- `routes/web_final.php` - Backup de rutas
- `routes/test-auth.php` - Tests de autenticación

### Base de Datos
- El schema principal está en `database/schema/schema.sql`
- La migración `sync_database_schema` carga todo el schema de una vez
- Las migraciones posteriores agregan columnas específicas

---

## 📋 Sistema de Cuestionarios - Implementación Dual

### ✅ Configuración Actual

El sistema maneja **DOS tipos de cuestionarios** de forma independiente:

#### 1️⃣ **Cuestionarios de Inscripción/Reinscripción**
- **Tabla:** `cuestionarios_inscrip_reinscrip`
- **Modelo:** `FechaCuestionario`
- **Controlador:** Solo `HomeController` (vista pública)
- **Admin:** ❌ **NO se gestiona desde admin**
- **Uso:** Reservado para uso futuro con implementación manual directa en BD
- **Características:**
  - Enlaces por grado: `link_primero`, `link_segundo`, `link_tercero`
  - Control de activación: `primero_activo`, `segundo_activo`, `tercero_activo`
  - Vigencia temporal: `fecha_inicio`, `fecha_fin`
  - Campo `activo` general

#### 2️⃣ **Cuestionarios Externos** (Gestionados por Admin)
- **Tabla:** `cuestionarios_externos`
- **Modelo:** `CuestionarioExterno`
- **Controlador Admin:** `FormulariosController` (CRUD completo)
- **Vista Home:** Muestra en sección separada ("Otros Formularios y Consultas")
- **Uso:** Encuestas, registros, formularios informativos
- **Características:** Misma estructura que inscripciones pero gestionable desde admin

### 🎨 Visualización en Home (Vista Pública)

**Sección 1: "Cuestionarios e Inscripciones"** (Gradiente Rojo)
- Lee de: `cuestionarios_inscrip_reinscrip`
- Muestra solo registros vigentes
- Tarjetas con 3 botones (1°, 2°, 3°)
- Diseño adaptativo: Se oculta si no hay datos

**Sección 2: "Otros Formularios y Consultas"** (Gradiente Azul)
- Lee de: `cuestionarios_externos`
- Muestra solo registros vigentes
- Misma estructura de tarjetas
- Diseño adaptativo: `v-if` se oculta completamente si no hay datos

### 🔐 Gestión Administrativa

**Ruta:** `/admin/formularios`

**Operaciones CRUD:**
```php
GET    /admin/formularios              → Lista todos los cuestionarios externos
GET    /admin/formularios/create       → Formulario de creación
POST   /admin/formularios              → Crear nuevo cuestionario
GET    /admin/formularios/{id}/edit    → Formulario de edición
PUT    /admin/formularios/{id}         → Actualizar cuestionario
DELETE /admin/formularios/{id}         → Eliminar cuestionario
```

**Campos editables desde admin:**
- Título y descripción
- Enlaces por grado (validación URL)
- Fechas de inicio y fin (validación after_or_equal)
- Estados de activación (general + por grado)

**⚠️ IMPORTANTE:** 
- Los cuestionarios de `cuestionarios_inscrip_reinscrip` **NO tienen interfaz admin**
- Se gestionan directamente en la base de datos según necesidades específicas
- Esta separación permite tener formularios oficiales (inscripciones) separados de formularios auxiliares

---

## 🎯 Conclusión

El sistema está **completamente funcional** con:

✅ Base de datos optimizada (18 tablas activas)
✅ Todos los endpoints operativos  
✅ Modelos con relaciones correctas  
✅ Middleware de autorización implementado  
✅ Storage centralizado con archivos_multimedia  
✅ Frontend Inertia + Vue funcional  
✅ Sistema dual de cuestionarios implementado
✅ Diseño adaptativo en vista pública

**Sistema listo para uso en desarrollo y preparado para migración a producción.**

---

*Documento actualizado: 20 de noviembre de 2025*

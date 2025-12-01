CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_s VARCHAR(50) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    hash_contrasena VARCHAR(255) NOT NULL,
    rol ENUM('Direccion','Subdireccion','Administrativo','Prefecto','Sistemas','Medico','Psicologo') NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS alumnos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    curp CHAR(18) NOT NULL UNIQUE,
    matricula VARCHAR(20) NOT NULL UNIQUE,
    nombre_s VARCHAR(80) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    sexo ENUM('M','F','X') NOT NULL,
    generacion VARCHAR(40) NOT NULL,
    estatus ENUM('activo','baja','egresado') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS grupos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grado TINYINT UNSIGNED NOT NULL,
    clave VARCHAR(10) NOT NULL,
    turno ENUM('Matutino','Vespertino') NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS historial_academico (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id BIGINT UNSIGNED NOT NULL,
    grupo_id BIGINT UNSIGNED NOT NULL,
    ciclo VARCHAR(9) NOT NULL,
    estatus ENUM('inscrito','baja','reprobado','egresado') NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
    FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS asistencias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id BIGINT UNSIGNED NOT NULL,
    grupo_id BIGINT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('presente','falta','retardo') NOT NULL,
    registrado_por BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
    FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS contactos_alumno (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id BIGINT UNSIGNED NOT NULL,
    nombre_s VARCHAR(80) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50) NOT NULL,
    parentesco VARCHAR(40) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(120),
    autorizado_recoger TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS documentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entidad_id BIGINT UNSIGNED NOT NULL,
    entidad_tipo VARCHAR(50) NOT NULL,
    tipo_documento ENUM('Credencial','Autorización_Recogida','Pase_Salida','Reglamento_Firmado') NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    firma TEXT,
    creado_por_id BIGINT UNSIGNED NOT NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (creado_por_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS archivos_multimedia (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ruta_archivo VARCHAR(255) NOT NULL,
    tipo_archivo VARCHAR(50) NOT NULL,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS anuncios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    activo TINYINT(1) NOT NULL,
    ruta_imagen VARCHAR(255),
    archivo_multimedia_id BIGINT UNSIGNED,
    fecha DATE NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (archivo_multimedia_id) REFERENCES archivos_multimedia(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS incidencias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id BIGINT UNSIGNED NOT NULL,
    descripcion TEXT NOT NULL,
    registrado_por_id BIGINT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS salidas_anticipadas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id BIGINT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    hora_salida TIME NOT NULL,
    motivo TEXT NOT NULL,
    autorizado_por_id BIGINT UNSIGNED NOT NULL,
    entregado_a_contacto_id BIGINT UNSIGNED NOT NULL,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
    FOREIGN KEY (autorizado_por_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (entregado_a_contacto_id) REFERENCES contactos_alumno(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cuestionarios_externos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    link_primero TEXT,
    link_segundo TEXT,
    link_tercero TEXT,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo TINYINT(1) NOT NULL,
    primero_activo TINYINT(1) NOT NULL,
    segundo_activo TINYINT(1) NOT NULL,
    tercero_activo TINYINT(1) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS cuestionarios_inscrip_reinscrip (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo TINYINT(1) NOT NULL,
    primero_activo TINYINT(1) NOT NULL,
    segundo_activo TINYINT(1) NOT NULL,
    tercero_activo TINYINT(1) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS salud (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id BIGINT UNSIGNED NOT NULL UNIQUE,
    tipo_sangre VARCHAR(5) NULL COMMENT 'Ej. O+, AB-, A-, etc.',
    alergias TEXT NULL COMMENT 'Descripción de alergias conocidas (ej. Penicilina, Polvo, Nueces)',
    enfermedades_cronicas TEXT NULL COMMENT 'Descripción de enfermedades crónicas (ej. Asma, Diabetes tipo 1)',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE
);

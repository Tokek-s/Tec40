-- Database schema export for bd_tec_40
-- Generated: 2025-12-03 07:46:41

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `alertas_sistema`;
CREATE TABLE `alertas_sistema` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('sobrecupo','error','advertencia','info') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `grado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `turno` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL,
  `resuelta` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `nota_resolucion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alertas_sistema_resuelta_tipo_index` (`resuelta`,`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `alumnos`;
CREATE TABLE `alumnos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `curp` char(18) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `nombre_s` varchar(80) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `sexo` enum('M','F','X') NOT NULL,
  `generacion` varchar(40) NOT NULL,
  `estatus` enum('activo','baja','egresado') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `archivo_multimedia_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `curp` (`curp`),
  UNIQUE KEY `matricula` (`matricula`),
  KEY `alumnos_archivo_multimedia_id_foreign` (`archivo_multimedia_id`),
  CONSTRAINT `alumnos_archivo_multimedia_id_foreign` FOREIGN KEY (`archivo_multimedia_id`) REFERENCES `archivos_multimedia` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `anuncios`;
CREATE TABLE `anuncios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `ruta_imagen` varchar(255) DEFAULT NULL,
  `archivo_multimedia_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `anuncios_archivo_multimedia_id_foreign` (`archivo_multimedia_id`),
  CONSTRAINT `anuncios_archivo_multimedia_id_foreign` FOREIGN KEY (`archivo_multimedia_id`) REFERENCES `archivos_multimedia` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `archivos_multimedia`;
CREATE TABLE `archivos_multimedia` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) NOT NULL,
  `tipo_mime` varchar(100) NOT NULL,
  `tipo_archivo` varchar(50) NOT NULL,
  `tamano` int NOT NULL,
  `contenido` longblob,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `archivos_pdf`;
CREATE TABLE `archivos_pdf` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ruta_archivo` varchar(255) NOT NULL,
  `tipo_archivo` varchar(50) NOT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `asistencias`;
CREATE TABLE `asistencias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` bigint unsigned NOT NULL,
  `grupo_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('presente','falta','retardo') NOT NULL,
  `registrado_por` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `grupo_id` (`grupo_id`),
  KEY `registrado_por` (`registrado_por`),
  CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asistencias_ibfk_3` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `contactos_alumno`;
CREATE TABLE `contactos_alumno` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` bigint unsigned NOT NULL,
  `nombre_s` varchar(80) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) NOT NULL,
  `parentesco` varchar(40) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `autorizado_recoger` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `archivo_multimedia_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `contactos_alumno_archivo_multimedia_id_foreign` (`archivo_multimedia_id`),
  CONSTRAINT `contactos_alumno_archivo_multimedia_id_foreign` FOREIGN KEY (`archivo_multimedia_id`) REFERENCES `archivos_multimedia` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contactos_alumno_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `cuestionarios_externos`;
CREATE TABLE `cuestionarios_externos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `link_primero` text,
  `link_segundo` text,
  `link_tercero` text,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `primero_activo` tinyint(1) NOT NULL,
  `segundo_activo` tinyint(1) NOT NULL,
  `tercero_activo` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `cuestionarios_inscrip_reinscrip`;
CREATE TABLE `cuestionarios_inscrip_reinscrip` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `link_primero` text,
  `link_segundo` text,
  `link_tercero` text,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `primero_activo` tinyint(1) NOT NULL,
  `segundo_activo` tinyint(1) NOT NULL,
  `tercero_activo` tinyint(1) NOT NULL,
  `tipo` enum('inscripcion','reinscripcion') NOT NULL DEFAULT 'inscripcion',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `datos_extra`;
CREATE TABLE `datos_extra` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` bigint unsigned NOT NULL,
  `cuntas_personas_viven_con_el_alumno` text,
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  CONSTRAINT `datos_extra_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `documentos`;
CREATE TABLE `documentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `entidad_id` bigint unsigned NOT NULL,
  `entidad_tipo` varchar(50) NOT NULL,
  `tipo_documento` enum('Credencial','Autorización_Recogida','Pase_Salida','Reglamento_Firmado') NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `firma` text,
  `creado_por_id` bigint unsigned NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `creado_por_id` (`creado_por_id`),
  CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`creado_por_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `grupos`;
CREATE TABLE `grupos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grado` tinyint unsigned NOT NULL,
  `clave` varchar(10) NOT NULL,
  `turno` enum('Matutino','Vespertino') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `historial_academico`;
CREATE TABLE `historial_academico` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` bigint unsigned NOT NULL,
  `grupo_id` bigint unsigned DEFAULT NULL,
  `ciclo` varchar(9) NOT NULL,
  `estatus` enum('inscrito','activo','baja','reprobado','egresado') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `grupo_id` (`grupo_id`),
  CONSTRAINT `historial_academico_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `historial_academico_ibfk_2` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `incidencias`;
CREATE TABLE `incidencias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` bigint unsigned NOT NULL,
  `descripcion` text NOT NULL,
  `registrado_por_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `registrado_por_id` (`registrado_por_id`),
  CONSTRAINT `incidencias_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `incidencias_ibfk_2` FOREIGN KEY (`registrado_por_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `salidas_anticipadas`;
CREATE TABLE `salidas_anticipadas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `hora_salida` time NOT NULL,
  `motivo` text NOT NULL,
  `autorizado_por_id` bigint unsigned NOT NULL,
  `entregado_a_contacto_id` bigint unsigned NOT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `autorizado_por_id` (`autorizado_por_id`),
  KEY `entregado_a_contacto_id` (`entregado_a_contacto_id`),
  CONSTRAINT `salidas_anticipadas_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salidas_anticipadas_ibfk_2` FOREIGN KEY (`autorizado_por_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salidas_anticipadas_ibfk_3` FOREIGN KEY (`entregado_a_contacto_id`) REFERENCES `contactos_alumno` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `salud`;
CREATE TABLE `salud` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` bigint unsigned NOT NULL,
  `tipo_sangre` varchar(5) DEFAULT NULL COMMENT 'Ej. O+, AB-, A-, etc.',
  `alergias` text COMMENT 'Descripción de alergias conocidas (ej. Penicilina, Polvo, Nueces)',
  `enfermedades_cronicas` text COMMENT 'Descripción de enfermedades crónicas (ej. Asma, Diabetes tipo 1)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumno_id` (`alumno_id`),
  CONSTRAINT `salud_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre_s` varchar(50) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `hash_contrasena` varchar(255) NOT NULL,
  `rol` enum('Direccion','Subdireccion','Administrativo','Prefecto','Sistemas','Medico','Psicologo') NOT NULL DEFAULT 'Administrativo',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

SET FOREIGN_KEY_CHECKS=1;

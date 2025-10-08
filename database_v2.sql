-- ================================================
-- Sistema de Gestión de Documentos v2.0
-- Base de datos con sistema de usuarios
-- ================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS blog_documentos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE blog_documentos;

-- ================================================
-- TABLA: usuarios
-- ================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    tema_preferido ENUM('light', 'dark') DEFAULT 'light',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME DEFAULT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: documentos (actualizada)
-- ================================================
CREATE TABLE IF NOT EXISTS documentos (
    id_documento INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    contenido LONGTEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    vistas INT DEFAULT 0,
    palabras INT DEFAULT 0,
    caracteres INT DEFAULT 0,
    favorito BOOLEAN DEFAULT FALSE,
    INDEX idx_usuario (id_usuario),
    INDEX idx_titulo (titulo),
    INDEX idx_fecha_modificacion (fecha_modificacion DESC),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: historial_cambios
-- ================================================
CREATE TABLE IF NOT EXISTS historial_cambios (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_documento INT NOT NULL,
    contenido_anterior LONGTEXT,
    accion VARCHAR(50) NOT NULL,
    fecha_cambio DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_documento (id_documento),
    FOREIGN KEY (id_documento) REFERENCES documentos(id_documento) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: sesiones
-- ================================================
CREATE TABLE IF NOT EXISTS sesiones (
    id_sesion VARCHAR(64) PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATETIME NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    INDEX idx_usuario (id_usuario),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- INSERTAR USUARIO DE PRUEBA
-- ================================================
-- Contraseña: admin123 (hasheada con password_hash)
INSERT INTO usuarios (nombre, email, password, avatar) VALUES 
('Usuario Demo', 'demo@sistema.com', '$2y$12$IPfp8qOC1cL75ERtEUnpVu6EKBkuHmgPFVxSQnK/jrJT32oSuE7zu', NULL);

-- ================================================
-- INSERTAR DOCUMENTOS DE EJEMPLO
-- ================================================
INSERT INTO documentos (id_usuario, titulo, contenido, palabras, caracteres) VALUES 
(1, 'Bienvenido al Sistema v2.0', '<h1>🎉 Bienvenido al Sistema de Gestión de Documentos v2.0</h1><p>Esta es la nueva versión con diseño profesional y características avanzadas.</p><h2>Nuevas características:</h2><ul><li><strong>Sistema de autenticación</strong> - Gestiona tus documentos de forma segura</li><li><strong>Modo oscuro/claro</strong> - Cambia el tema según tu preferencia</li><li><strong>Autoguardado inteligente</strong> - Nunca pierdas tu trabajo</li><li><strong>Exportar a PDF</strong> - Descarga tus documentos</li><li><strong>Estadísticas detalladas</strong> - Analiza tu productividad</li><li><strong>Historial de cambios</strong> - Revisa versiones anteriores</li></ul><p><em>¡Explora todas las funcionalidades del sistema!</em></p>', 89, 650),
(1, 'Guía Rápida de Uso', '<h2>📖 Cómo usar el sistema</h2><p>Esta guía te ayudará a aprovechar al máximo todas las características.</p><h3>1. Crear un documento</h3><p>Haz clic en el botón "+ Nuevo Documento" en la barra lateral.</p><h3>2. Editar y formatear</h3><p>Usa las herramientas del editor para dar formato a tu texto: <strong>negrita</strong>, <em>cursiva</em>, listas, etc.</p><h3>3. Guardar automáticamente</h3><p>El sistema guarda tus cambios cada 30 segundos automáticamente.</p>', 75, 480),
(1, 'Mis Ideas para el Proyecto', '<h1>💡 Ideas Creativas</h1><p>Lista de ideas para desarrollar:</p><ol><li>Implementar colaboración en tiempo real</li><li>Agregar etiquetas y categorías</li><li>Sistema de compartir documentos</li><li>Búsqueda avanzada con filtros</li><li>Integración con almacenamiento en la nube</li></ol>', 45, 320);

-- ================================================
-- VISTA: estadisticas_usuario
-- ================================================
CREATE OR REPLACE VIEW estadisticas_usuario AS
SELECT 
    u.id_usuario,
    u.nombre,
    u.email,
    COUNT(d.id_documento) as total_documentos,
    SUM(d.palabras) as total_palabras,
    SUM(d.caracteres) as total_caracteres,
    SUM(d.vistas) as total_vistas,
    MAX(d.fecha_modificacion) as ultima_edicion
FROM usuarios u
LEFT JOIN documentos d ON u.id_usuario = d.id_usuario
GROUP BY u.id_usuario, u.nombre, u.email;


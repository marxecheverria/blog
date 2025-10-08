# 📝 Sistema de Gestión de Documentos - Versión Simplificada

Sistema completo de gestión de documentos con editor de texto enriquecido, desarrollado con PHP, MySQL y JavaScript.

## ✨ Características

- ✅ **Editor de texto enriquecido** con Quill.js
- ✅ **Sistema de autenticación** con usuarios
- ✅ **Gestión completa de documentos** (crear, editar, eliminar)
- ✅ **Búsqueda** de documentos por título y contenido
- ✅ **Estadísticas** de productividad
- ✅ **Tema oscuro/claro** con toggle
- ✅ **Interfaz moderna** y responsive
- ✅ **API REST** completa
- ✅ **Diseño limpio** y funcional

## 🛠️ Tecnologías

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Editor**: Quill.js 1.3.6
- **Backend**: PHP 7.4+ con PDO
- **Base de datos**: MySQL 5.7+
- **Servidor**: XAMPP (Apache + MySQL)

## 📋 Requisitos

- XAMPP instalado (Apache y MySQL activos)
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Navegador web moderno

## 🚀 Instalación

### 1. Copiar archivos

Coloca todos los archivos en `C:\xampp\htdocs\blog\`

### 2. Crear base de datos

1. Abre **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Ve a la pestaña **SQL**
3. Ejecuta el contenido de `database_v2.sql`
4. Esto creará la base de datos y tablas necesarias

### 3. Configurar conexión

Abre `config.php` y verifica las credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'blog_documentos');
define('DB_USER', 'root');
define('DB_PASS', ''); // Vacío por defecto en XAMPP
```

### 4. Iniciar servicios

1. Abre el **Panel de Control de XAMPP**
2. Inicia **Apache** y **MySQL**
3. Verifica que ambos estén en verde

### 5. Acceder al sistema

```
http://localhost/blog/
```

## 🔐 Credenciales de Acceso

**Usuario demo:**
- Email: `demo@sistema.com`
- Contraseña: `admin123`

O crea tu propia cuenta desde el login.

## 📖 Uso del Sistema

### Crear documento

1. Haz clic en **"Nuevo Documento"** en la barra lateral
2. Escribe el título del documento
3. Usa el editor para escribir el contenido
4. Haz clic en **"Guardar"** o usa `Ctrl+S`

### Editar documento

1. Ve a **"Mis Documentos"**
2. Haz clic en el documento que deseas editar
3. Modifica el contenido
4. Guarda los cambios

### Buscar documentos

1. Ve a **"Buscar"** en la barra lateral
2. Escribe tu término de búsqueda
3. Los resultados aparecerán automáticamente

### Ver estadísticas

1. Ve a **"Estadísticas"** en la barra lateral
2. Verás métricas de tus documentos

### Cambiar tema

1. Haz clic en el icono de **luna/sol** en la barra superior
2. El tema cambiará entre claro y oscuro
3. Tu preferencia se guarda automáticamente

## ⌨️ Atajos de Teclado

- `Ctrl + S` → Guardar documento
- `Ctrl + N` → Nuevo documento
- `Esc` → Cerrar modal

## 📁 Estructura de Archivos

```
blog/
├── index.html          # Redireccionador
├── login.html          # Página de login/registro
├── dashboard.html      # Interfaz principal
├── styles.css          # Estilos CSS
├── app.js              # JavaScript principal
├── api.php             # API REST
├── auth.php            # Sistema de autenticación
├── config.php          # Configuración
├── database_v2.sql     # Script de base de datos
└── README.md           # Esta documentación
```

## 🔒 Seguridad

- Contraseñas hasheadas con bcrypt
- Consultas preparadas (PDO)
- Validación de sesiones
- Escape de HTML
- Verificación de permisos

## 📊 Base de Datos

**Tabla: `usuarios`**
- id_usuario, nombre, email, password, tema_preferido

**Tabla: `documentos`**
- id_documento, id_usuario, titulo, contenido, palabras, caracteres, vistas

## 🌐 API Endpoints

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `api.php?action=crear` | Crear documento |
| GET | `api.php?action=listar` | Listar documentos |
| GET | `api.php?action=ver&id=X` | Ver documento |
| PUT | `api.php?action=editar` | Editar documento |
| DELETE | `api.php?action=eliminar&id=X` | Eliminar documento |

## 🎨 Personalización

### Cambiar colores

Edita las variables CSS en `styles.css`:

```css
:root {
    --primary-color: #1E2A78;    /* Color principal */
    --accent-color: #5AE4A8;     /* Color de acento */
}
```

### Cambiar tipografía

En `dashboard.html` y `login.html`:

```html
<link href="https://fonts.googleapis.com/css2?family=TuFuente:wght@300;400;500;600;700&display=swap" rel="stylesheet">
```

## 🐛 Solución de Problemas

### Error de conexión a BD
- Verifica que MySQL esté activo en XAMPP
- Ejecuta `database_v2.sql` en phpMyAdmin
- Revisa las credenciales en `config.php`

### No funciona el login
- Verifica que la tabla `usuarios` exista
- Confirma que se ejecutó `database_v2.sql`
- Borra cookies del navegador

### El editor no aparece
- Verifica tu conexión a internet (Quill.js usa CDN)
- Revisa la consola del navegador (F12)
- Limpia la caché del navegador

## 📱 Responsive

El sistema es totalmente responsive:
- Desktop (1920px+)
- Laptop (1024px - 1920px)
- Tablet (768px - 1024px)
- Móvil (< 768px)

## 📄 Licencia

Este proyecto es de código abierto y está disponible para uso personal y educativo.

## 👤 Autor

Sistema desarrollado como ejemplo de gestión de documentos con editor WYSIWYG.

---

**¡Disfruta gestionando tus documentos!** 📚✨

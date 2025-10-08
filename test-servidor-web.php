<?php
/**
 * Test específico para el servidor web
 * http://blogtemas.myartsonline.com/
 */

// Configuración del servidor web
define('DB_HOST', 'fdb1033.awardspace.net');
define('DB_NAME', '4650827_documentos');
define('DB_USER', '4650827_documentos');
define('DB_PASS', 'Tristania1201');

date_default_timezone_set('America/Guayaquil');
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Servidor Web - Sistema de Documentos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-box {
            background: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        h1 { color: #333; text-align: center; }
        .config { background: #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🔧 Test Servidor Web - Sistema de Documentos</h1>
    
    <div class="test-box">
        <h3>🌐 Información del Servidor</h3>
        <div class="config">
            <strong>URL Actual:</strong> <?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?><br>
            <strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br>
            <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?><br>
            <strong>Fecha:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
            <strong>Zona Horaria:</strong> <?php echo date_default_timezone_get(); ?>
        </div>
    </div>

    <?php
    // Test de conexión
    echo '<div class="test-box">';
    echo '<h3>🔌 Test de Conexión a Base de Datos</h3>';
    
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        echo '<div class="success">✅ Conexión exitosa a la base de datos</div>';
        
        // Test de tablas
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo '<div class="info">📊 Tablas encontradas: ' . implode(', ', $tables) . '</div>';
        
        // Test de usuarios
        if (in_array('usuarios', $tables)) {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
            $result = $stmt->fetch();
            echo '<div class="success">👤 Usuarios: ' . $result['total'] . '</div>';
        }
        
        // Test de documentos
        if (in_array('documentos', $tables)) {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM documentos");
            $result = $stmt->fetch();
            echo '<div class="success">📄 Documentos: ' . $result['total'] . '</div>';
        }
        
    } catch (PDOException $e) {
        echo '<div class="error">❌ Error de conexión: ' . $e->getMessage() . '</div>';
        echo '<div class="warning">💡 Posibles causas:</div>';
        echo '<ul>';
        echo '<li>El servidor de base de datos está inactivo</li>';
        echo '<li>Las credenciales son incorrectas</li>';
        echo '<li>La base de datos no existe</li>';
        echo '<li>Problemas de red/firewall</li>';
        echo '</ul>';
    }
    echo '</div>';

    // Test de archivos
    echo '<div class="test-box">';
    echo '<h3>📁 Test de Archivos del Sistema</h3>';
    
    $files = [
        'index.html' => 'Redireccionador principal',
        'login.html' => 'Página de login',
        'dashboard.html' => 'Dashboard principal',
        'config.php' => 'Configuración',
        'api.php' => 'API REST',
        'auth.php' => 'Autenticación',
        'app.js' => 'JavaScript',
        'styles.css' => 'Estilos CSS'
    ];
    
    foreach ($files as $file => $description) {
        if (file_exists($file)) {
            echo '<div class="success">✅ ' . $file . ' - ' . $description . '</div>';
        } else {
            echo '<div class="error">❌ ' . $file . ' - NO ENCONTRADO</div>';
        }
    }
    echo '</div>';

    // Test de API
    echo '<div class="test-box">';
    echo '<h3>🔗 Test de API</h3>';
    
    if (file_exists('api.php')) {
        echo '<div class="info">📡 API disponible en: <a href="api.php?action=listar" target="_blank">api.php?action=listar</a></div>';
        echo '<div class="info">🔐 Auth disponible en: <a href="auth.php?action=check" target="_blank">auth.php?action=check</a></div>';
    } else {
        echo '<div class="error">❌ api.php no encontrado</div>';
    }
    echo '</div>';
    ?>

    <div class="test-box">
        <h3>🚀 Enlaces del Sistema</h3>
        <a href="index.html" class="btn">🏠 Sistema Principal</a>
        <a href="login.html" class="btn">🔐 Login</a>
        <a href="dashboard.html" class="btn">📊 Dashboard</a>
        <a href="config.php" class="btn">⚙️ Config</a>
    </div>

    <div class="test-box">
        <h3>🔧 Solución de Problemas</h3>
        <p><strong>Si config.php te redirige a otra página:</strong></p>
        <ol>
            <li>Verifica que el archivo config.php esté en la raíz del servidor</li>
            <li>Confirma que no haya redirecciones en .htaccess</li>
            <li>Revisa que el servidor web esté configurado correctamente</li>
            <li>Verifica que PHP esté funcionando</li>
        </ol>
        
        <p><strong>Si hay error de conexión:</strong></p>
        <ol>
            <li>Verifica que el servidor de BD esté activo</li>
            <li>Confirma las credenciales de conexión</li>
            <li>Revisa la configuración de red</li>
            <li>Contacta al proveedor de hosting</li>
        </ol>
    </div>
</body>
</html>

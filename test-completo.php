<?php
/**
 * Test Completo de Conexión y Diagnóstico
 */

// Configuración
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
    <title>Test Completo - Sistema de Documentos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
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
        .code {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            border-left: 4px solid #007bff;
            margin: 10px 0;
        }
        .log-box {
            background: #1a1a1a;
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <h1>🔧 Test Completo - Sistema de Documentos</h1>
    
    <div class="test-section">
        <h3>🌐 Información del Servidor</h3>
        <div class="code">
            <strong>URL:</strong> <?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?><br>
            <strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br>
            <strong>PHP:</strong> <?php echo PHP_VERSION; ?><br>
            <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
            <strong>Script:</strong> <?php echo $_SERVER['SCRIPT_NAME']; ?><br>
            <strong>Fecha:</strong> <?php echo date('Y-m-d H:i:s'); ?>
        </div>
    </div>

    <div class="test-section">
        <h3>🔌 Test de Conexión Detallado</h3>
        <?php
        echo '<div class="code">';
        echo '<strong>Host:</strong> ' . DB_HOST . '<br>';
        echo '<strong>Base de Datos:</strong> ' . DB_NAME . '<br>';
        echo '<strong>Usuario:</strong> ' . DB_USER . '<br>';
        echo '<strong>Contraseña:</strong> ' . str_repeat('*', strlen(DB_PASS)) . '<br>';
        echo '</div>';

        try {
            echo '<div class="info">⏳ Intentando conectar...</div>';
            
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            echo '<div class="code">DSN: ' . $dsn . '</div>';
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            echo '<div class="success">✅ Conexión PDO exitosa</div>';
            
            // Test de consulta
            $stmt = $pdo->query("SELECT 1 as test");
            $result = $stmt->fetch();
            echo '<div class="success">✅ Consulta de prueba exitosa: ' . json_encode($result) . '</div>';
            
            // Verificar tablas
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo '<div class="success">✅ Tablas encontradas: ' . implode(', ', $tables) . '</div>';
            
            // Verificar usuarios
            if (in_array('usuarios', $tables)) {
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
                $result = $stmt->fetch();
                echo '<div class="success">✅ Usuarios: ' . $result['total'] . '</div>';
            }
            
            // Verificar documentos
            if (in_array('documentos', $tables)) {
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM documentos");
                $result = $stmt->fetch();
                echo '<div class="success">✅ Documentos: ' . $result['total'] . '</div>';
            }
            
        } catch (PDOException $e) {
            echo '<div class="error">❌ Error PDO: ' . $e->getMessage() . '</div>';
            echo '<div class="error">❌ Código: ' . $e->getCode() . '</div>';
            echo '<div class="error">❌ Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine() . '</div>';
            
            // Información adicional
            echo '<div class="warning">💡 Posibles causas:</div>';
            echo '<ul>';
            echo '<li>Servidor de base de datos inactivo</li>';
            echo '<li>Credenciales incorrectas</li>';
            echo '<li>Base de datos no existe</li>';
            echo '<li>Problemas de red/firewall</li>';
            echo '<li>Límites de conexión del hosting</li>';
            echo '</ul>';
        } catch (Exception $e) {
            echo '<div class="error">❌ Error general: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>

    <div class="test-section">
        <h3>📁 Verificación de Archivos</h3>
        <?php
        $files = [
            'config.php' => 'Configuración principal',
            'api.php' => 'API REST',
            'auth.php' => 'Autenticación',
            'app.js' => 'JavaScript',
            'styles.css' => 'Estilos',
            'dashboard.html' => 'Dashboard',
            'login.html' => 'Login',
            'index.html' => 'Index'
        ];
        
        foreach ($files as $file => $description) {
            if (file_exists($file)) {
                $size = filesize($file);
                echo '<div class="success">✅ ' . $file . ' - ' . $description . ' (' . $size . ' bytes)</div>';
            } else {
                echo '<div class="error">❌ ' . $file . ' - NO ENCONTRADO</div>';
            }
        }
        ?>
    </div>

    <div class="test-section">
        <h3>🔗 Test de API</h3>
        <?php
        if (file_exists('api.php')) {
            echo '<div class="info">📡 Probando API...</div>';
            
            // Simular llamada a API
            try {
                $_GET['action'] = 'listar';
                ob_start();
                include 'api.php';
                $output = ob_get_clean();
                
                echo '<div class="code">Respuesta API: ' . htmlspecialchars($output) . '</div>';
            } catch (Exception $e) {
                echo '<div class="error">❌ Error en API: ' . $e->getMessage() . '</div>';
            }
        } else {
            echo '<div class="error">❌ api.php no encontrado</div>';
        }
        ?>
    </div>

    <div class="test-section">
        <h3>🔧 Soluciones Recomendadas</h3>
        <ol>
            <li><strong>Si la conexión falla:</strong>
                <ul>
                    <li>Verifica que el servidor de BD esté activo</li>
                    <li>Confirma las credenciales con el hosting</li>
                    <li>Revisa la configuración de red</li>
                    <li>Contacta al soporte del hosting</li>
                </ul>
            </li>
            <li><strong>Si config.php redirige:</strong>
                <ul>
                    <li>Verifica el archivo .htaccess</li>
                    <li>Revisa si hay redirecciones en config.php</li>
                    <li>Confirma la configuración del servidor web</li>
                </ul>
            </li>
            <li><strong>Si faltan archivos:</strong>
                <ul>
                    <li>Sube todos los archivos al servidor</li>
                    <li>Verifica permisos de archivos</li>
                    <li>Confirma que estén en la carpeta correcta</li>
                </ul>
            </li>
        </ol>
    </div>
</body>
</html>

<?php
/**
 * Diagnóstico de Redirección
 * Para solucionar el problema de config.php que redirige
 */

// Configuración
define('DB_HOST', 'fdb1033.awardspace.net');
define('DB_NAME', '4650827_documentos');
define('DB_USER', '4650827_documentos');
define('DB_PASS', 'Tristania1201');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Redirección</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .diagnostic-box {
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
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico de Redirección</h1>
    
    <div class="diagnostic-box">
        <h3>🌐 Información del Servidor</h3>
        <div class="code">
            <strong>URL Actual:</strong> <?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?><br>
            <strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br>
            <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?><br>
            <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
            <strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME']; ?>
        </div>
    </div>

    <div class="diagnostic-box">
        <h3>📁 Verificación de Archivos</h3>
        <?php
        $files = [
            'config.php' => 'Configuración principal',
            'config-servidor.php' => 'Configuración del servidor',
            '.htaccess' => 'Configuración Apache',
            'index.html' => 'Página principal',
            'login.html' => 'Página de login'
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

    <div class="diagnostic-box">
        <h3>🔗 Test de Enlaces</h3>
        <p>Prueba estos enlaces para verificar el comportamiento:</p>
        
        <a href="config.php" class="btn" target="_blank">⚙️ config.php</a>
        <a href="config-servidor.php" class="btn" target="_blank">🔧 config-servidor.php</a>
        <a href="index.html" class="btn" target="_blank">🏠 index.html</a>
        <a href="login.html" class="btn" target="_blank">🔐 login.html</a>
        
        <p><strong>Resultado esperado:</strong></p>
        <ul>
            <li><strong>config.php:</strong> Debería mostrar configuración o JSON</li>
            <li><strong>config-servidor.php:</strong> Debería mostrar configuración</li>
            <li><strong>index.html:</strong> Debería redirigir al login</li>
            <li><strong>login.html:</strong> Debería mostrar formulario de login</li>
        </ul>
    </div>

    <div class="diagnostic-box">
        <h3>🔌 Test de Conexión Directa</h3>
        <?php
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            echo '<div class="success">✅ Conexión directa exitosa</div>';
            
            // Test de consulta
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
            $result = $stmt->fetch();
            echo '<div class="success">👤 Usuarios en BD: ' . $result['total'] . '</div>';
            
        } catch (PDOException $e) {
            echo '<div class="error">❌ Error de conexión: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>

    <div class="diagnostic-box">
        <h3>🔧 Soluciones Posibles</h3>
        
        <h4>Si config.php redirige a otra página:</h4>
        <ol>
            <li><strong>Verificar .htaccess:</strong> Puede tener redirecciones</li>
            <li><strong>Verificar config.php:</strong> Puede tener header() de redirección</li>
            <li><strong>Verificar servidor:</strong> El hosting puede tener redirecciones</li>
            <li><strong>Usar config-servidor.php:</strong> Como alternativa</li>
        </ol>
        
        <h4>Si hay error de conexión:</h4>
        <ol>
            <li><strong>Verificar hosting:</strong> El servidor de BD puede estar inactivo</li>
            <li><strong>Verificar credenciales:</strong> Pueden haber cambiado</li>
            <li><strong>Verificar red:</strong> Problemas de conectividad</li>
            <li><strong>Contactar soporte:</strong> Del proveedor de hosting</li>
        </ol>
    </div>

    <div class="diagnostic-box">
        <h3>📋 Comandos de Verificación</h3>
        <div class="code">
            # Verificar archivos en el servidor
            ls -la *.php *.html
            
            # Verificar contenido de config.php
            head -20 config.php
            
            # Verificar .htaccess
            cat .htaccess
        </div>
    </div>
</body>
</html>

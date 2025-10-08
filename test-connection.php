<?php
/**
 * Test de Conexión a Base de Datos
 * Verifica la conexión con el servidor web
 */

// Configuración de la base de datos
define('DB_HOST', 'fdb1033.awardspace.net');
define('DB_NAME', '4650827_documentos');
define('DB_USER', '4650827_documentos');
define('DB_PASS', 'Tristania1201');

// Configuración de zona horaria
date_default_timezone_set('America/Guayaquil');

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - Sistema de Documentos</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .test-item {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ddd;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }
        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #666;
            margin-top: 30px;
        }
        .config-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🔧 Test de Conexión - Sistema de Documentos</h1>
        
        <div class="test-item info">
            <h3>📋 Configuración Actual</h3>
            <div class="config-info">
                <strong>Host:</strong> <?php echo DB_HOST; ?><br>
                <strong>Base de Datos:</strong> <?php echo DB_NAME; ?><br>
                <strong>Usuario:</strong> <?php echo DB_USER; ?><br>
                <strong>Contraseña:</strong> <?php echo str_repeat('*', strlen(DB_PASS)); ?><br>
                <strong>Zona Horaria:</strong> <?php echo date_default_timezone_get(); ?>
            </div>
        </div>

        <?php
        // Test 1: Conexión básica
        echo '<div class="test-item">';
        echo '<h3>🔌 Test 1: Conexión Básica</h3>';
        
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            echo '<div class="success">✅ Conexión exitosa a la base de datos</div>';
        } catch (PDOException $e) {
            echo '<div class="error">❌ Error de conexión: ' . $e->getMessage() . '</div>';
            $pdo = null;
        }
        echo '</div>';

        // Test 2: Verificar tablas
        if ($pdo) {
            echo '<div class="test-item">';
            echo '<h3>📊 Test 2: Verificar Tablas</h3>';
            
            try {
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (empty($tables)) {
                    echo '<div class="warning">⚠️ No se encontraron tablas en la base de datos</div>';
                    echo '<div class="info">💡 Necesitas ejecutar el script database_v2.sql</div>';
                } else {
                    echo '<div class="success">✅ Tablas encontradas: ' . implode(', ', $tables) . '</div>';
                    
                    // Verificar tablas específicas
                    $requiredTables = ['usuarios', 'documentos'];
                    $missingTables = [];
                    
                    foreach ($requiredTables as $table) {
                        if (!in_array($table, $tables)) {
                            $missingTables[] = $table;
                        }
                    }
                    
                    if (!empty($missingTables)) {
                        echo '<div class="error">❌ Faltan tablas requeridas: ' . implode(', ', $missingTables) . '</div>';
                    } else {
                        echo '<div class="success">✅ Todas las tablas requeridas están presentes</div>';
                    }
                }
            } catch (PDOException $e) {
                echo '<div class="error">❌ Error verificando tablas: ' . $e->getMessage() . '</div>';
            }
            echo '</div>';

            // Test 3: Verificar usuarios
            echo '<div class="test-item">';
            echo '<h3>👤 Test 3: Verificar Usuarios</h3>';
            
            try {
                if (in_array('usuarios', $tables)) {
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
                    $result = $stmt->fetch();
                    $userCount = $result['total'];
                    
                    if ($userCount > 0) {
                        echo '<div class="success">✅ Se encontraron ' . $userCount . ' usuario(s)</div>';
                        
                        // Mostrar usuarios
                        $stmt = $pdo->query("SELECT id_usuario, nombre, email FROM usuarios LIMIT 5");
                        $users = $stmt->fetchAll();
                        
                        echo '<div class="info">📋 Usuarios en la base de datos:</div>';
                        echo '<ul>';
                        foreach ($users as $user) {
                            echo '<li><strong>' . htmlspecialchars($user['nombre']) . '</strong> (' . htmlspecialchars($user['email']) . ')</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<div class="warning">⚠️ No se encontraron usuarios</div>';
                        echo '<div class="info">💡 Necesitas insertar el usuario demo</div>';
                    }
                } else {
                    echo '<div class="error">❌ La tabla usuarios no existe</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="error">❌ Error verificando usuarios: ' . $e->getMessage() . '</div>';
            }
            echo '</div>';

            // Test 4: Verificar documentos
            echo '<div class="test-item">';
            echo '<h3>📄 Test 4: Verificar Documentos</h3>';
            
            try {
                if (in_array('documentos', $tables)) {
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM documentos");
                    $result = $stmt->fetch();
                    $docCount = $result['total'];
                    
                    echo '<div class="success">✅ Se encontraron ' . $docCount . ' documento(s)</div>';
                    
                    if ($docCount > 0) {
                        // Mostrar documentos recientes
                        $stmt = $pdo->query("SELECT titulo, fecha_creacion FROM documentos ORDER BY fecha_creacion DESC LIMIT 3");
                        $docs = $stmt->fetchAll();
                        
                        echo '<div class="info">📋 Documentos recientes:</div>';
                        echo '<ul>';
                        foreach ($docs as $doc) {
                            echo '<li><strong>' . htmlspecialchars($doc['titulo']) . '</strong> - ' . $doc['fecha_creacion'] . '</li>';
                        }
                        echo '</ul>';
                    }
                } else {
                    echo '<div class="error">❌ La tabla documentos no existe</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="error">❌ Error verificando documentos: ' . $e->getMessage() . '</div>';
            }
            echo '</div>';

            // Test 5: Test de escritura
            echo '<div class="test-item">';
            echo '<h3>✏️ Test 5: Test de Escritura</h3>';
            
            try {
                $testTable = 'test_connection_' . time();
                $stmt = $pdo->prepare("CREATE TABLE $testTable (id INT AUTO_INCREMENT PRIMARY KEY, test_data VARCHAR(100))");
                $stmt->execute();
                
                $stmt = $pdo->prepare("INSERT INTO $testTable (test_data) VALUES (?)");
                $stmt->execute(['Test de conexión - ' . date('Y-m-d H:i:s')]);
                
                $stmt = $pdo->prepare("SELECT test_data FROM $testTable WHERE id = ?");
                $stmt->execute([$pdo->lastInsertId()]);
                $result = $stmt->fetch();
                
                if ($result) {
                    echo '<div class="success">✅ Test de escritura exitoso</div>';
                    echo '<div class="info">📝 Datos insertados: ' . htmlspecialchars($result['test_data']) . '</div>';
                }
                
                // Limpiar tabla de prueba
                $pdo->exec("DROP TABLE $testTable");
                echo '<div class="info">🧹 Tabla de prueba eliminada</div>';
                
            } catch (PDOException $e) {
                echo '<div class="error">❌ Error en test de escritura: ' . $e->getMessage() . '</div>';
            }
            echo '</div>';
        }

        // Test 6: Información del servidor
        echo '<div class="test-item">';
        echo '<h3>🖥️ Test 6: Información del Servidor</h3>';
        
        echo '<div class="info">';
        echo '<strong>PHP Version:</strong> ' . PHP_VERSION . '<br>';
        echo '<strong>Servidor:</strong> ' . $_SERVER['SERVER_SOFTWARE'] . '<br>';
        echo '<strong>Fecha/Hora:</strong> ' . date('Y-m-d H:i:s') . '<br>';
        echo '<strong>Zona Horaria:</strong> ' . date_default_timezone_get() . '<br>';
        echo '<strong>Extensión PDO:</strong> ' . (extension_loaded('pdo') ? '✅ Disponible' : '❌ No disponible') . '<br>';
        echo '<strong>Extensión PDO MySQL:</strong> ' . (extension_loaded('pdo_mysql') ? '✅ Disponible' : '❌ No disponible') . '<br>';
        echo '</div>';
        echo '</div>';
        ?>

        <div class="test-item info">
            <h3>🚀 Próximos Pasos</h3>
            <p>Si todos los tests pasan correctamente:</p>
            <ol>
                <li>Actualiza el archivo <code>config.php</code> con estas credenciales</li>
                <li>Ejecuta <code>database_v2.sql</code> en tu servidor web</li>
                <li>Accede al sistema desde tu dominio</li>
            </ol>
            
            <a href="index.html" class="btn">🏠 Ir al Sistema</a>
            <a href="login.html" class="btn">🔐 Ir al Login</a>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Test de Conexión con Logs Detallados
 * Para identificar problemas de conexión
 */

// Habilitar logs de error
ini_set('log_errors', 1);
ini_set('error_log', 'connection_errors.log');

// Configuración
define('DB_HOST', 'fdb1033.awardspace.net');
define('DB_NAME', '4650827_documentos');
define('DB_USER', '4650827_documentos');
define('DB_PASS', 'Tristania1201');

date_default_timezone_set('America/Guayaquil');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión con Logs</title>
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
        .log-output {
            background: #1a1a1a;
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
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
    <h1>🔧 Test de Conexión con Logs Detallados</h1>
    
    <div class="test-box">
        <h3>📋 Configuración Actual</h3>
        <div class="info">
            <strong>Host:</strong> <?php echo DB_HOST; ?><br>
            <strong>Base de Datos:</strong> <?php echo DB_NAME; ?><br>
            <strong>Usuario:</strong> <?php echo DB_USER; ?><br>
            <strong>Contraseña:</strong> <?php echo str_repeat('*', strlen(DB_PASS)); ?><br>
            <strong>Log de errores:</strong> connection_errors.log
        </div>
    </div>

    <div class="test-box">
        <h3>🔌 Test de Conexión Paso a Paso</h3>
        <div id="connection-test">
            <p class="info">⏳ Ejecutando test de conexión...</p>
        </div>
    </div>

    <div class="test-box">
        <h3>📝 Logs de Conexión</h3>
        <div id="log-output" class="log-output">
            Cargando logs...
        </div>
        <button onclick="refreshLogs()" class="btn">🔄 Actualizar Logs</button>
    </div>

    <div class="test-box">
        <h3>🔗 Enlaces de Prueba</h3>
        <a href="api.php?action=listar" class="btn" target="_blank">📡 API Listar</a>
        <a href="auth.php?action=check" class="btn" target="_blank">🔐 Auth Check</a>
        <a href="config.php" class="btn" target="_blank">⚙️ Config</a>
    </div>

    <script>
        // Función para actualizar logs
        function refreshLogs() {
            fetch('get-logs.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('log-output').textContent = data;
                })
                .catch(error => {
                    document.getElementById('log-output').textContent = 'Error cargando logs: ' + error;
                });
        }

        // Test de conexión
        async function testConnection() {
            const testDiv = document.getElementById('connection-test');
            
            try {
                testDiv.innerHTML = '<p class="info">⏳ Probando conexión...</p>';
                
                const response = await fetch('test-connection-simple.php');
                const data = await response.json();
                
                if (data.success) {
                    testDiv.innerHTML = '<p class="success">✅ Conexión exitosa</p>';
                } else {
                    testDiv.innerHTML = '<p class="error">❌ Error: ' + data.message + '</p>';
                }
            } catch (error) {
                testDiv.innerHTML = '<p class="error">❌ Error de red: ' + error.message + '</p>';
            }
        }

        // Cargar logs inicial
        refreshLogs();
        
        // Ejecutar test
        testConnection();
        
        // Actualizar logs cada 5 segundos
        setInterval(refreshLogs, 5000);
    </script>
</body>
</html>

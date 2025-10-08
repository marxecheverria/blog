<?php
/**
 * Configuración de conexión a la base de datos
 */

// Configuración de la base de datos
if (!defined('DB_HOST')) define('DB_HOST', 'fdb1033.awardspace.net');
if (!defined('DB_NAME')) define('DB_NAME', '4650827_documentos');
if (!defined('DB_USER')) define('DB_USER', '4650827_documentos');
if (!defined('DB_PASS')) define('DB_PASS', 'Tristania1201'); 





// Configuración de zona horaria
date_default_timezone_set('America/Guayaquil');

// Configuración de sesiones
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
    session_start();
}

// Configuración de errores (desactivar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Función para obtener conexión PDO a la base de datos
 */
function getDBConnection() {
    // Log de inicio
    error_log("=== INICIO CONEXIÓN BD ===");
    error_log("Host: " . DB_HOST);
    error_log("Base de datos: " . DB_NAME);
    error_log("Usuario: " . DB_USER);
    error_log("Contraseña: " . (DB_PASS ? "***CONFIGURADA***" : "VACÍA"));
    
    try {
        // Construir DSN
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        error_log("DSN construido: " . $dsn);
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        error_log("Opciones PDO configuradas");
        
        // Intentar conexión
        error_log("Intentando conectar...");
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        error_log("✅ Conexión PDO exitosa");
        
        // Test de consulta simple
        error_log("Probando consulta simple...");
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        error_log("✅ Consulta de prueba exitosa: " . json_encode($result));
        
        error_log("=== CONEXIÓN BD EXITOSA ===");
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("❌ ERROR PDO: " . $e->getMessage());
        error_log("❌ Código de error: " . $e->getCode());
        error_log("❌ Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        
        // Información adicional del error
        $errorInfo = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'host' => DB_HOST,
            'database' => DB_NAME,
            'user' => DB_USER,
            'dsn' => $dsn ?? 'No construido'
        ];
        error_log("❌ Información completa del error: " . json_encode($errorInfo));
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos: ' . $e->getMessage(),
            'debug_info' => $errorInfo
        ]);
        exit;
    } catch (Exception $e) {
        error_log("❌ ERROR GENERAL: " . $e->getMessage());
        error_log("❌ Código de error: " . $e->getCode());
        error_log("❌ Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error general: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Función para enviar respuesta JSON
 */
function sendJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

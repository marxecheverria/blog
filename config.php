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

// Configuración de sesiones (solo si no estamos en modo diagnóstico)
if (!defined('DIAGNOSTIC_MODE') && session_status() === PHP_SESSION_NONE) {
    @ini_set('session.cookie_httponly', 1);
    @ini_set('session.use_only_cookies', 1);
    @ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
    @session_start();
}

// Configuración de errores (desactivar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Función para obtener conexión PDO a la base de datos
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Error de conexión a BD: " . $e->getMessage());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos'
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

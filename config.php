<?php
/**
 * Configuración de conexión a la base de datos
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'blog_documentos');
define('DB_USER', 'root');
define('DB_PASS', ''); // Cambiar si tienes contraseña en XAMPP





// Configuración de zona horaria
date_default_timezone_set('America/Guayaquil');

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
session_start();

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
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()
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
?>


<?php
/**
 * Leer logs de conexión
 */

header('Content-Type: text/plain; charset=utf-8');

$logFile = 'connection_errors.log';

if (file_exists($logFile)) {
    // Leer las últimas 50 líneas
    $lines = file($logFile);
    $recentLines = array_slice($lines, -50);
    
    foreach ($recentLines as $line) {
        echo $line;
    }
} else {
    echo "No se encontró el archivo de logs: " . $logFile . "\n";
    echo "Verificando configuración de logs...\n";
    echo "log_errors: " . (ini_get('log_errors') ? 'ON' : 'OFF') . "\n";
    echo "error_log: " . ini_get('error_log') . "\n";
    echo "display_errors: " . (ini_get('display_errors') ? 'ON' : 'OFF') . "\n";
}
?>

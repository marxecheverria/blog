<?php
/**
 * Test Simple de Conexión
 * Para usar con AJAX
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Intentar conexión
    $pdo = getDBConnection();
    
    // Test de consulta
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexión exitosa',
        'usuarios' => $result['total']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
}
?>

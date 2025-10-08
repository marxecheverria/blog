<?php
/**
 * Test para verificar que se solucionaron los problemas de headers
 */

echo "<h1>🔧 Test de Corrección de Headers</h1>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

echo "<h2>📋 Verificaciones:</h2>";

// Test 1: Incluir config.php
echo "<h3>1. Incluyendo config.php...</h3>";
try {
    require_once 'config.php';
    echo "✅ config.php incluido sin errores<br>";
} catch (Exception $e) {
    echo "❌ Error incluyendo config.php: " . $e->getMessage() . "<br>";
}

// Test 2: Verificar constantes
echo "<h3>2. Verificando constantes...</h3>";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDA') . "<br>";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NO DEFINIDA') . "<br>";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NO DEFINIDA') . "<br>";
echo "DB_PASS: " . (defined('DB_PASS') ? '***CONFIGURADA***' : 'NO DEFINIDA') . "<br>";

// Test 3: Verificar sesión
echo "<h3>3. Verificando sesión...</h3>";
echo "Estado de sesión: " . session_status() . " (1=inactiva, 2=activa)<br>";
echo "ID de sesión: " . session_id() . "<br>";

// Test 4: Test de conexión
echo "<h3>4. Test de conexión a BD...</h3>";
try {
    $pdo = getDBConnection();
    echo "✅ Conexión exitosa<br>";
    
    // Test simple
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "✅ Usuarios en BD: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

// Test 5: Test de API
echo "<h3>5. Test de API...</h3>";
try {
    // Simular request a API
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['action'] = 'get_documents';
    
    // Capturar output
    ob_start();
    include 'api.php';
    $api_output = ob_get_clean();
    
    echo "✅ API ejecutada<br>";
    echo "Respuesta: " . substr($api_output, 0, 100) . "...<br>";
    
} catch (Exception $e) {
    echo "❌ Error en API: " . $e->getMessage() . "<br>";
}

// Test 6: Test de Auth
echo "<h3>6. Test de Auth...</h3>";
try {
    // Simular request a Auth
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['action'] = 'check';
    
    // Capturar output
    ob_start();
    include 'auth.php';
    $auth_output = ob_get_clean();
    
    echo "✅ Auth ejecutado<br>";
    echo "Respuesta: " . substr($auth_output, 0, 100) . "...<br>";
    
} catch (Exception $e) {
    echo "❌ Error en Auth: " . $e->getMessage() . "<br>";
}

echo "<h2>🎯 Resultado:</h2>";
echo "<p>Si no ves warnings de 'headers already sent' o 'constants already defined', ¡las correcciones funcionaron!</p>";

echo "<h2>🔗 Enlaces de prueba:</h2>";
echo "<a href='login.html' target='_blank'>🔐 Login</a><br>";
echo "<a href='dashboard.html' target='_blank'>📊 Dashboard</a><br>";
echo "<a href='api.php?action=get_documents' target='_blank'>📡 API Documents</a><br>";
echo "<a href='auth.php?action=check' target='_blank'>🔑 Auth Check</a><br>";
?>

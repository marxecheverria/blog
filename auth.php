<?php
/**
 * Sistema de Autenticación
 * Gestión de login, registro y sesiones
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$pdo = getDBConnection();

try {
    switch ($action) {
        case 'login':
            login($pdo);
            break;
        
        case 'register':
            register($pdo);
            break;
        
        case 'logout':
            logout();
            break;
        
        case 'check':
            checkSession();
            break;
        
        case 'profile':
            getProfile($pdo);
            break;
        
        case 'update_theme':
            updateTheme($pdo);
            break;
        
        default:
            sendJSON(['success' => false, 'message' => 'Acción no válida'], 400);
    }
} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
}

/**
 * Iniciar sesión
 */
function login($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        sendJSON(['success' => false, 'message' => 'Email y contraseña requeridos'], 400);
    }
    
    $email = trim($data['email']);
    $password = $data['password'];
    
    try {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            sendJSON(['success' => false, 'message' => 'Credenciales incorrectas'], 401);
        }
        
        // Verificar contraseña
        if (!password_verify($password, $usuario['password'])) {
            sendJSON(['success' => false, 'message' => 'Credenciales incorrectas'], 401);
        }
        
        // Actualizar último acceso
        $updateSql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = :id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute(['id' => $usuario['id_usuario']]);
        
        // Crear sesión
        $_SESSION['user_id'] = $usuario['id_usuario'];
        $_SESSION['user_name'] = $usuario['nombre'];
        $_SESSION['user_email'] = $usuario['email'];
        $_SESSION['user_theme'] = $usuario['tema_preferido'];
        
        sendJSON([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'user' => [
                'id' => $usuario['id_usuario'],
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email'],
                'avatar' => $usuario['avatar'],
                'tema' => $usuario['tema_preferido']
            ]
        ]);
    } catch (PDOException $e) {
        sendJSON(['success' => false, 'message' => 'Error en el servidor'], 500);
    }
}

/**
 * Registrar nuevo usuario
 */
function register($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['password'])) {
        sendJSON(['success' => false, 'message' => 'Todos los campos son requeridos'], 400);
    }
    
    $nombre = trim($data['nombre']);
    $email = trim($data['email']);
    $password = $data['password'];
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJSON(['success' => false, 'message' => 'Email no válido'], 400);
    }
    
    // Validar contraseña
    if (strlen($password) < 6) {
        sendJSON(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
    }
    
    try {
        // Verificar si el email ya existe
        $checkSql = "SELECT id_usuario FROM usuarios WHERE email = :email";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(['email' => $email]);
        
        if ($checkStmt->fetch()) {
            sendJSON(['success' => false, 'message' => 'El email ya está registrado'], 400);
        }
        
        // Hashear contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar usuario
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'password' => $passwordHash
        ]);
        
        $userId = $pdo->lastInsertId();
        
        // Crear sesión automáticamente
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $nombre;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_theme'] = 'light';
        
        sendJSON([
            'success' => true,
            'message' => 'Registro exitoso',
            'user' => [
                'id' => $userId,
                'nombre' => $nombre,
                'email' => $email,
                'tema' => 'light'
            ]
        ], 201);
    } catch (PDOException $e) {
        sendJSON(['success' => false, 'message' => 'Error al registrar usuario'], 500);
    }
}

/**
 * Cerrar sesión
 */
function logout() {
    session_destroy();
    sendJSON(['success' => true, 'message' => 'Sesión cerrada exitosamente']);
}

/**
 * Verificar sesión activa
 */
function checkSession() {
    if (!isset($_SESSION['user_id'])) {
        sendJSON(['success' => false, 'authenticated' => false], 401);
    }
    
    sendJSON([
        'success' => true,
        'authenticated' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'tema' => $_SESSION['user_theme'] ?? 'light'
        ]
    ]);
}

/**
 * Obtener perfil completo del usuario
 */
function getProfile($pdo) {
    if (!isset($_SESSION['user_id'])) {
        sendJSON(['success' => false, 'message' => 'No autenticado'], 401);
    }
    
    try {
        // Obtener datos del usuario y estadísticas
        $sql = "SELECT * FROM estadisticas_usuario WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $perfil = $stmt->fetch();
        
        if (!$perfil) {
            sendJSON(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }
        
        sendJSON([
            'success' => true,
            'perfil' => $perfil
        ]);
    } catch (PDOException $e) {
        sendJSON(['success' => false, 'message' => 'Error al obtener perfil'], 500);
    }
}

/**
 * Actualizar tema del usuario
 */
function updateTheme($pdo) {
    if (!isset($_SESSION['user_id'])) {
        sendJSON(['success' => false, 'message' => 'No autenticado'], 401);
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $tema = $data['tema'] ?? 'light';
    
    if (!in_array($tema, ['light', 'dark'])) {
        sendJSON(['success' => false, 'message' => 'Tema no válido'], 400);
    }
    
    try {
        $sql = "UPDATE usuarios SET tema_preferido = :tema WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'tema' => $tema,
            'id' => $_SESSION['user_id']
        ]);
        
        $_SESSION['user_theme'] = $tema;
        
        sendJSON([
            'success' => true,
            'message' => 'Tema actualizado',
            'tema' => $tema
        ]);
    } catch (PDOException $e) {
        sendJSON(['success' => false, 'message' => 'Error al actualizar tema'], 500);
    }
}
?>


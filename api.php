<?php
/**
 * API REST para el sistema de gestión de documentos
 */

require_once 'config.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    sendJSON(['success' => false, 'message' => 'No autenticado'], 401);
}

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'crear':
            crearDocumento($pdo, $userId);
            break;
        
        case 'listar':
            listarDocumentos($pdo, $userId);
            break;
        
        case 'ver':
            verDocumento($pdo, $userId);
            break;
        
        case 'editar':
            editarDocumento($pdo, $userId);
            break;
        
        case 'eliminar':
            eliminarDocumento($pdo, $userId);
            break;
        
        default:
            sendJSON(['success' => false, 'message' => 'Acción no válida'], 400);
    }
} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
}

/**
 * Crear documento
 */
function crearDocumento($pdo, $userId) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['titulo']) || !isset($data['contenido'])) {
        sendJSON(['success' => false, 'message' => 'Faltan datos requeridos'], 400);
    }
    
    $titulo = trim($data['titulo']);
    $contenido = $data['contenido'];
    $palabras = $data['palabras'] ?? 0;
    $caracteres = $data['caracteres'] ?? 0;
    
    if (empty($titulo)) {
        sendJSON(['success' => false, 'message' => 'El título no puede estar vacío'], 400);
    }
    
    try {
        $sql = "INSERT INTO documentos (id_usuario, titulo, contenido, palabras, caracteres) 
                VALUES (:id_usuario, :titulo, :contenido, :palabras, :caracteres)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_usuario' => $userId,
            'titulo' => $titulo,
            'contenido' => $contenido,
            'palabras' => $palabras,
            'caracteres' => $caracteres
        ]);
        
        $idDocumento = $pdo->lastInsertId();
        
        sendJSON([
            'success' => true,
            'message' => 'Documento creado exitosamente',
            'id_documento' => $idDocumento
        ], 201);
    } catch (PDOException $e) {
        sendJSON(['success' => false, 'message' => 'Error al crear documento'], 500);
    }
}

/**
 * Listar documentos
 */
function listarDocumentos($pdo, $userId) {
    try {
        $sql = "SELECT id_documento, titulo, contenido, fecha_creacion, fecha_modificacion, 
                       vistas, palabras, caracteres
                FROM documentos 
                WHERE id_usuario = :id_usuario 
                ORDER BY fecha_modificacion DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_usuario' => $userId]);
        $documentos = $stmt->fetchAll();
        
        sendJSON([
            'success' => true,
            'documentos' => $documentos,
            'total' => count($documentos)
        ]);
    } catch (PDOException $e) {
        sendJSON(['success' => false, 'message' => 'Error al listar documentos'], 500);
    }
}

/**
 * Ver documento
 */
function verDocumento($pdo, $userId) {
    $id = $_GET['id'] ?? null;
    
    if (!$id || !is_numeric($id)) {
        sendJSON(['success' => false, 'message' => 'ID de documento no válido'], 400);
    }
    
    try {
        $sql = "SELECT * FROM documentos 
                WHERE id_documento = :id AND id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'id_usuario' => $userId
        ]);
        $documento = $stmt->fetch();
        
        if (!$documento) {
            sendJSON(['success' => false, 'message' => 'Documento no encontrado'], 404);
        }
        
        sendJSON([
            'success' => true,
            'documento' => $documento
        ]);
    } catch (PDOException $e) {
        sendJSON(['success' => false, 'message' => 'Error al obtener documento'], 500);
    }
}

/**
 * Editar documento
 */
function editarDocumento($pdo, $userId) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id_documento']) || !isset($data['titulo']) || !isset($data['contenido'])) {
        sendJSON(['success' => false, 'message' => 'Faltan datos requeridos'], 400);
    }
    
    $id = $data['id_documento'];
    $titulo = trim($data['titulo']);
    $contenido = $data['contenido'];
    $palabras = $data['palabras'] ?? 0;
    $caracteres = $data['caracteres'] ?? 0;
    
    if (empty($titulo)) {
        sendJSON(['success' => false, 'message' => 'El título no puede estar vacío'], 400);
    }
    
    try {
        // Verificar que el documento pertenece al usuario
        $sqlCheck = "SELECT id_documento FROM documentos 
                     WHERE id_documento = :id AND id_usuario = :id_usuario";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            'id' => $id,
            'id_usuario' => $userId
        ]);
        
        if (!$stmtCheck->fetch()) {
            sendJSON(['success' => false, 'message' => 'Documento no encontrado'], 404);
        }
        
        // Actualizar documento
        $sql = "UPDATE documentos 
                SET titulo = :titulo, contenido = :contenido, 
                    palabras = :palabras, caracteres = :caracteres
                WHERE id_documento = :id AND id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titulo' => $titulo,
            'contenido' => $contenido,
            'palabras' => $palabras,
            'caracteres' => $caracteres,
            'id' => $id,
            'id_usuario' => $userId
        ]);
        
        sendJSON([
            'success' => true,
            'message' => 'Documento actualizado exitosamente'
        ]);
    } catch (PDOException $e) {
        sendJSON(['success' => false, 'message' => 'Error al actualizar documento'], 500);
    }
}

/**
 * Eliminar documento
 */
function eliminarDocumento($pdo, $userId) {
    $id = $_GET['id'] ?? null;
    
    if (!$id || !is_numeric($id)) {
        sendJSON(['success' => false, 'message' => 'ID de documento no válido'], 400);
    }
    
    try {
        // Verificar que el documento pertenece al usuario
        $sqlCheck = "SELECT id_documento FROM documentos 
                     WHERE id_documento = :id AND id_usuario = :id_usuario";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            'id' => $id,
            'id_usuario' => $userId
        ]);
        
        if (!$stmtCheck->fetch()) {
            sendJSON(['success' => false, 'message' => 'Documento no encontrado'], 404);
        }
        
        // Eliminar documento
        $sql = "DELETE FROM documentos 
                WHERE id_documento = :id AND id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'id_usuario' => $userId
        ]);
        
        sendJSON([
            'success' => true,
            'message' => 'Documento eliminado exitosamente'
        ]);
    } catch (PDOException $e) {
        sendJSON(['success' => false, 'message' => 'Error al eliminar documento'], 500);
    }
}
?>

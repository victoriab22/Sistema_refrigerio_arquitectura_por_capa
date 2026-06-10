<?php
session_start();
require_once __DIR__ . '/../negocio/SolicitudService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'solicitante') {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

try {
    $service = new SolicitudService();
    // Verificar que la solicitud pertenezca al usuario
    $solicitud = $service->obtenerSolicitud($id);
    if (!$solicitud || $solicitud['usuario_id'] != $_SESSION['id_usuario']) {
        throw new Exception("No puedes eliminar esta solicitud");
    }
    $service->eliminarSolicitud($id);
    echo json_encode(['success' => true, 'mensaje' => 'Solicitud eliminada correctamente']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
<?php
session_start();
require_once __DIR__ . '/../negocio/RevisionService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Acceso no permitido");
}

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'revisor') {
    exit("No autorizado");
}

$accion = $_POST['accion'] ?? '';
$idSolicitud = intval($_POST['id'] ?? 0);
$observacion = trim($_POST['comentario'] ?? '');

try {
    $service = new RevisionService();
    $usuarioId = $_SESSION['id_usuario'];

    switch ($accion) {
        case 'aprobar':
            $service->aprobar($idSolicitud, $usuarioId, $observacion);
            break;
        case 'rechazar':
            $service->rechazar($idSolicitud, $usuarioId, $observacion);
            break;
        case 'transito':
            $service->enviarTransito($idSolicitud, $usuarioId);
            break;
        case 'completar':
            $service->completar($idSolicitud, $usuarioId);
            break;
        default:
            throw new Exception("Acción inválida");
    }
    echo "OK";
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}
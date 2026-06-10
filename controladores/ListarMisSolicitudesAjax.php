<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'solicitante') {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../negocio/SolicitudService.php';
$service = new SolicitudService();
$solicitudes = $service->listarPorUsuario($_SESSION['id_usuario']);
echo json_encode($solicitudes);
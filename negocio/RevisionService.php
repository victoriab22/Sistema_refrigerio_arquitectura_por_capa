<?php
require_once __DIR__ . '/../capa_acceso/dao/SolicitudDAO.php';

class RevisionService {
    private $solicitudDAO;

    public function __construct() {
        $this->solicitudDAO = new SolicitudDAO();
    }

    public function aprobar($idSolicitud, $usuarioId) {
        return $this->solicitudDAO->actualizarEstado($idSolicitud, 2, $usuarioId, null);
    }

    public function rechazar($idSolicitud, $usuarioId, $observacion) {
        if (empty($observacion)) throw new Exception("El motivo de rechazo es obligatorio");
        return $this->solicitudDAO->actualizarEstado($idSolicitud, 3, $usuarioId, $observacion);
    }

    public function enviarTransito($idSolicitud, $usuarioId) {
        return $this->solicitudDAO->actualizarEstado($idSolicitud, 4, $usuarioId, null);
    }

    public function completar($idSolicitud, $usuarioId) {
        return $this->solicitudDAO->actualizarEstado($idSolicitud, 5, $usuarioId, null);
    }
}
<?php
require_once __DIR__ . '/../Conexion/Conexion.php';

class SolicitudDAO {
    private $Conexion;

    public function __construct() {
        $this->Conexion = (new Conexion())->conectar();
    }

    public function generarRadicado() {
        return 'RAD-' . date('YmdHis') . rand(100, 999);
    }

    public function guardar($datos) {
        $sql = "INSERT INTO solicitudes (
            numero_radicado, usuario_id, dependencia_id, estado_id,
            fecha_solicitud, tipo_servicio, justificacion, valor_total,
            fondo_id, centro_costo_id, funcion_id, disponibilidad_presupuestal,
            telefono, cargo_solicitante, nombre_evento, lugar_evento,
            cantidad_dias, fecha_inicio, fecha_fin
        ) VALUES (
            :radicado, :usuario_id, :dependencia_id, :estado_id,
            :fecha_solicitud, :tipo_servicio, :justificacion, :valor_total,
            :fondo_id, :centro_costo_id, :funcion_id, :disponibilidad_presupuestal,
            :telefono, :cargo_solicitante, :nombre_evento, :lugar_evento,
            :cantidad_dias, :fecha_inicio, :fecha_fin
        )";
        $stmt = $this->Conexion->prepare($sql);
        $stmt->execute($datos);
        return $this->Conexion->lastInsertId();
    }

    public function actualizar($datos) {
    $sql = "UPDATE solicitudes SET
                dependencia_id = :dependencia_id,
                fecha_solicitud = :fecha_solicitud,
                tipo_servicio = :tipo_servicio,
                justificacion = :justificacion,
                valor_total = :valor_total,
                fondo_id = :fondo_id,
                centro_costo_id = :centro_costo_id,
                funcion_id = :funcion_id,
                disponibilidad_presupuestal = :disponibilidad_presupuestal,
                telefono = :telefono,
                cargo_solicitante = :cargo_solicitante,
                nombre_evento = :nombre_evento,
                lugar_evento = :lugar_evento,
                cantidad_dias = :cantidad_dias,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin
            WHERE id = :id";
    $stmt = $this->Conexion->prepare($sql);
    return $stmt->execute($datos);
}

    public function listar() {
        $sql = "SELECT s.*, e.nombre AS estado_nombre, u.nombre AS solicitante_nombre 
                FROM solicitudes s
                JOIN usuarios u ON s.usuario_id = u.id
                JOIN estados_solicitud e ON s.estado_id = e.id
                ORDER BY s.id DESC";
        return $this->Conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorUsuario($usuarioId) {
        $sql = "SELECT s.*, e.nombre AS estado_nombre 
                FROM solicitudes s
                JOIN estados_solicitud e ON s.estado_id = e.id
                WHERE s.usuario_id = :usuario_id
                ORDER BY s.id DESC";
        $stmt = $this->Conexion->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener($id) {
        $sql = "SELECT s.*, e.nombre AS estado_nombre, u.nombre AS solicitante_nombre, u.apellido, u.email
                FROM solicitudes s
                JOIN usuarios u ON s.usuario_id = u.id
                JOIN estados_solicitud e ON s.estado_id = e.id
                WHERE s.id = :id";
        $stmt = $this->Conexion->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   public function actualizarEstado($id, $nuevoEstadoId, $usuarioId, $observacion = null) {
    // Obtener el estado actual antes de actualizar
    $sqlActual = "SELECT estado_id FROM solicitudes WHERE id = :id";
    $stmtActual = $this->Conexion->prepare($sqlActual);
    $stmtActual->execute(['id' => $id]);
    $estadoAnterior = $stmtActual->fetchColumn();
    
    // Actualizar la solicitud
    $sqlUpdate = "UPDATE solicitudes SET estado_id = :estado_id WHERE id = :id";
    $stmtUpdate = $this->Conexion->prepare($sqlUpdate);
    $stmtUpdate->execute(['estado_id' => $nuevoEstadoId, 'id' => $id]);
    
    // Insertar en historial_estados (siempre, aunque no haya observación)
    $sqlHist = "INSERT INTO historial_estados 
                (solicitud_id, estado_anterior_id, estado_nuevo_id, usuario_id, observacion, fecha) 
                VALUES (:sid, :anterior, :nuevo, :uid, :obs, NOW())";
    $stmtHist = $this->Conexion->prepare($sqlHist);
    $stmtHist->execute([
        'sid' => $id,
        'anterior' => $estadoAnterior ?: null,
        'nuevo' => $nuevoEstadoId,
        'uid' => $usuarioId,
        'obs' => $observacion
    ]);
    
    return true;
}

    public function eliminar($id) {
        $sql = "DELETE FROM solicitudes WHERE id = :id";
        $stmt = $this->Conexion->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
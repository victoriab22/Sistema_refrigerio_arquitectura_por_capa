<?php
require_once __DIR__ . '/../Conexion/Conexion.php';

class DetalleDAO {
    private $Conexion;

    public function __construct() {
        $this->Conexion = (new Conexion())->conectar();
    }

    public function guardar($detalle) {
        $sql = "INSERT INTO items_solicitud_refrigerio_almuerzo 
                (dia, hora, cantidad, alimentos, bebidas, tipo_solicitud, requiere_mesero, lugar_entrega, id_solicitud)
                VALUES (:dia, :hora, :cantidad, :alimentos, :bebidas, :tipo, :mesero, :lugar, :id_solicitud)";
        $stmt = $this->Conexion->prepare($sql);
        return $stmt->execute($detalle);
    }

    public function listarPorSolicitud($idSolicitud) {
        $sql = "SELECT * FROM items_solicitud_refrigerio_almuerzo WHERE id_solicitud = :id_solicitud";
        $stmt = $this->Conexion->prepare($sql);
        $stmt->execute(['id_solicitud' => $idSolicitud]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function eliminarPorSolicitud($idSolicitud) {
    $sql = "DELETE FROM items_solicitud_refrigerio_almuerzo WHERE id_solicitud = :id_solicitud";
    $stmt = $this->Conexion->prepare($sql);
    return $stmt->execute(['id_solicitud' => $idSolicitud]);
}
}
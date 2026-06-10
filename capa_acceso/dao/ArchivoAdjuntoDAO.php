<?php
require_once __DIR__ . '/../conexiondb/Conexion.php';

class ArchivoAdjuntoDAO {
    private $Conexion;
    public function __construct() {
        $this->Conexion = (new Conexion())->conectar();
    }

    public function guardar($solicitudId, $nombreOriginal, $ruta, $tipoMime, $tamano) {
        $sql = "INSERT INTO archivos_adjuntos (solicitud_id, nombre_archivo, ruta_archivo, tipo_mime, tamano_bytes)
                VALUES (:sid, :nombre, :ruta, :mime, :tamano)";
        $stmt = $this->Conexion->prepare($sql);
        return $stmt->execute([
            'sid' => $solicitudId,
            'nombre' => $nombreOriginal,
            'ruta' => $ruta,
            'mime' => $tipoMime,
            'tamano' => $tamano
        ]);
    }
}
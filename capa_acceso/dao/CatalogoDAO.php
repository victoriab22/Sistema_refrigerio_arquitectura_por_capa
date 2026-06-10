<?php
require_once __DIR__ . '/../conexion/Conexion.php';

class CatalogoDAO {
    private $db;
    public function __construct() {
        $this->db = (new Conexion())->conectar();
    }
    public function listarFondos() {
        $sql = "SELECT id, nombre FROM fondos WHERE activo = 1";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarCentrosCosto() {
        $sql = "SELECT id, nombre, codigo FROM centros_costo WHERE activo = 1";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarFunciones() {
        $sql = "SELECT id, nombre, codigo FROM funcion WHERE activo = 1";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarDependencias() {
        $sql = "SELECT id, nombre FROM dependencias WHERE activo = 1";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
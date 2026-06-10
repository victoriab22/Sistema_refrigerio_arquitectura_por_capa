<?php
require_once __DIR__ . '/../Conexion/Conexion.php';

class UsuarioDAO
{
    private $Conexion;

    public function __construct()
    {
        $this->Conexion = (new Conexion())->conectar();
    }

    /**
     * Busca un usuario por su email 
     * @return array|false
     */
    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND activo = 1";
        $stmt = $this->Conexion->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un usuario por su ID
     */
    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->Conexion->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los roles de un usuario (nombres)
     * @return array
     */
    public function obtenerRoles($usuarioId)
    {
        $sql = "SELECT r.nombre 
                FROM usuario_rol ur
                JOIN roles r ON ur.rol_id = r.id
                WHERE ur.usuario_id = :uid";
        $stmt = $this->Conexion->prepare($sql);
        $stmt->execute(['uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
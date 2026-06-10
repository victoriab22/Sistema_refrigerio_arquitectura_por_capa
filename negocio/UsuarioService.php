<?php
require_once __DIR__ . '/../capa_acceso/dao/UsuarioDAO.php';

class UsuarioService
{
    private $usuarioDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
    }

    /**
     * Autentica a un usuario por email y contraseña
     * @return array|false Datos del usuario si es válido, false en caso contrario
     */
    public function autenticar($email, $password)
    {
        $usuario = $this->usuarioDAO->buscarPorEmail($email);
        if (!$usuario) {
            return false; // Usuario no existe o inactivo
        }

        // Verificar contraseña
        if (!password_verify($password, $usuario['password_hash'])) {
            return false; // Contraseña incorrecta
        }

        // Obtener roles del usuario
        $roles = $this->usuarioDAO->obtenerRoles($usuario['id']);

        // Retornar un array con los datos necesarios para la sesión
        return [
            'id'      => $usuario['id'],
            'nombre'  => $usuario['nombre'],
            'apellido'=> $usuario['apellido'],
            'email'   => $usuario['email'],
            'cargo'   => $usuario['cargo'],
            'roles'   => $roles
        ];
    }

    /**
     * Obtiene un usuario por su ID
     */
    public function obtenerUsuario($id)
    {
        return $this->usuarioDAO->buscarPorId($id);
    }
}
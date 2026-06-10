<?php
class Conexion
{
    private $host;
    private $port;
    private $db;
    private $usuario;
    private $password;

    public function __construct()
    {
        $this->host = getenv('MYSQLHOST') ?: '127.0.0.1';
        $this->port = getenv('MYSQLPORT') ?: '3307';
        $this->db = getenv('MYSQLDATABASE') ?: 'solicitud_final';
        $this->usuario = getenv('MYSQLUSER') ?: 'root';
        $this->password = getenv('MYSQLPASSWORD') ?: '';
    }

    public function conectar()
    {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset=utf8mb4",
                $this->usuario,
                $this->password
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
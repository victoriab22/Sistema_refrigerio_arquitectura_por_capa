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
        $getEnv = function($key, $default = null) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
            return $value !== false ? $value : $default;
        };

        $this->host     = $getEnv('MYSQLHOST',     '127.0.0.1');
        $this->port     = $getEnv('MYSQLPORT',     '3306');
        $this->db       = $getEnv('MYSQLDATABASE', 'solicitud_final');
        $this->usuario  = $getEnv('MYSQLUSER',     'root');
        $this->password = $getEnv('MYSQLPASSWORD', '');
    }

   public function conectar()
{
    try {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset=utf8mb4";
        $pdo = new PDO($dsn, $this->usuario, $this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        // ⚠️ Temporal: mostrar el error real (NO hacer esto en producción)
        die("Error de conexión PDO: " . $e->getMessage());
    }
}
}
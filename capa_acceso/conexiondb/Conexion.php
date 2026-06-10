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
            $url = $_ENV['MYSQL_URL'] ?? getenv('MYSQL_URL');

            if ($url) {
                $pdo = new PDO($url);
            } else {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset=utf8mb4";
                $pdo = new PDO($dsn, $this->usuario, $this->password);
            }

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;

        } catch (PDOException $e) {
            error_log("Error de conexión a BD: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos. Contacte al administrador.");
        }
    }
}
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
        // Función auxiliar para obtener variables de entorno (de varias fuentes)
        $getEnv = function($key, $default = null) {
            // Primero en $_ENV, luego en $_SERVER, luego getenv
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
            return $value !== false ? $value : $default;
        };

        // Railway inyecta estas variables automáticamente al agregar el servicio MySQL
        $this->host = $getEnv('MYSQLHOST', '127.0.0.1');
        $this->port = $getEnv('MYSQLPORT', '3306');      // Railway usa 3306
        $this->db   = $getEnv('MYSQLDATABASE', 'solicitud_final'); // Railway usa "railway" por defecto
        $this->usuario = $getEnv('MYSQLUSER', 'root');
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
            // Registrar error en logs de Railway (importante para depurar)
            error_log("Error de conexión a BD: " . $e->getMessage());
            // Lanzar excepción para que el controlador la capture (en lugar de die())
            throw new Exception("Error de conexión a la base de datos. Contacte al administrador.");
        }
    }
}
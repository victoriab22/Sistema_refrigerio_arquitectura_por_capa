<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/capa_acceso/conexiondb/Conexion.php';

try {
    $conn = new Conexion();
    $pdo = $conn->conectar();
    echo "✅ Conexión exitosa a la base de datos.<br>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    echo "Número de usuarios: " . $stmt->fetchColumn();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Detalles de entorno:<br>";
    echo "MYSQLHOST: " . ($_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?: 'no definido') . "<br>";
    echo "MYSQLPORT: " . ($_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?: 'no definido') . "<br>";
    echo "MYSQLDATABASE: " . ($_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?: 'no definido') . "<br>";
    echo "MYSQLUSER: " . ($_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?: 'no definido') . "<br>";
    echo "MYSQLPASSWORD: " . (($_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD')) ? '****' : 'no definido') . "<br>";
}
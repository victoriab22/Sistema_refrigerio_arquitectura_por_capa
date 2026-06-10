<?php
session_start();
require_once __DIR__ . '/../negocio/UsuarioService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$email = trim($_POST['usuario'] ?? '');
$password = trim($_POST['password'] ?? '');

try {
    $service = new UsuarioService();
    $usuario = $service->autenticar($email, $password);


    if (!$usuario) {
        throw new Exception("Credenciales incorrectas");
    }

    $_SESSION['id_usuario'] = $usuario['id'];
    $_SESSION['usuario'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['cargo'] = $usuario['cargo'];
    $_SESSION['rol'] = $usuario['roles'][0] ?? 'solicitante'; // asigna el primer rol

    header('Location: ../presentacion/vistas/dashboard.php');
    exit;
} catch (Exception $e) {
    echo "<script>alert('{$e->getMessage()}'); window.location.href='../index.php';</script>";
}
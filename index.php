<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Sistema de Solicitudes</title>
    <link rel="stylesheet" href="presentacion/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
                        url('recursos/Cecar.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.25);
            max-width: 400px;
            width: 100%;
            padding: 2rem;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-logo img {
            width: 100px;
            margin-bottom: 0.5rem;
        }
        .login-logo h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
            color: #374151;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #064c2b;
            box-shadow: 0 0 0 3px rgba(6,76,43,0.1);
        }
        .btn-login {
            width: 100%;
            background: #064c2b;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: #043d22;
        }
        .footer {
            text-align: center;
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="recursos/logo-cecar.png" alt="Logo CECAR">
            <h1>Sistema de Solicitudes</h1>
            <p class="text-sm text-gray-500">Refrigerios y almuerzos</p>
        </div>

        <form action="controladores/LoginController.php" method="POST">
            <div class="form-group">
                <label for="usuario">Correo electrónico</label>
                <input type="email" name="usuario" id="usuario" required placeholder="usuario@cecar.edu.co">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>

        <div class="footer">
            © <?= date('Y') ?> · CECAR · Victoria Barrios, Yulianis Oviedo & Mauricio
        </div>
    </div>
</body>
</html>
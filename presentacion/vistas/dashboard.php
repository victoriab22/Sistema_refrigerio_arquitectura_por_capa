<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../index.php");
    exit();
}
require_once '../../negocio/SolicitudService.php';
$service = new SolicitudService();

// Datos para solicitante (solo sus solicitudes)
if ($_SESSION['rol'] === 'solicitante') {
    $solicitudes = $service->listarPorUsuario($_SESSION['id_usuario']);
    $pendientes = array_filter($solicitudes, fn($s) => $s['estado_nombre'] == 'Pendiente');
} 
// Datos para revisor (todas las solicitudes)
elseif ($_SESSION['rol'] === 'revisor') {
    $todas = $service->listarTodas();
    $totalPendientes = count(array_filter($todas, fn($s) => $s['estado_nombre'] == 'Pendiente'));
    $totalAprobados = count(array_filter($todas, fn($s) => $s['estado_nombre'] == 'Aprobado'));
    $totalEnTransito = count(array_filter($todas, fn($s) => $s['estado_nombre'] == 'En tránsito'));
    $totalCompletadas = count(array_filter($todas, fn($s) => $s['estado_nombre'] == 'Completada'));
    $totalRechazadas = count(array_filter($todas, fn($s) => $s['estado_nombre'] == 'Rechazado'));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Solicitudes</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include '../layout/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h2>Dashboard</h2>
            <p>Bienvenido al sistema de solicitudes</p>
        </div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['usuario']) ?></div>
            <div class="user-email"><?= htmlspecialchars($_SESSION['email']) ?></div>
        </div>
    </div>

    <?php if ($_SESSION['rol'] === 'solicitante'): ?>
        <!-- Estadísticas personales para el solicitante -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Solicitudes creadas</div>
                <div class="stat-number"><?= count($solicitudes) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Pendientes</div>
                <div class="stat-number"><?= count($pendientes) ?></div>
            </div>
        </div>
    <?php elseif ($_SESSION['rol'] === 'revisor'): ?>
        <!-- Estadísticas globales para el revisor -->
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="stat-card" style="border-top-color: #f59e0b;">
                <div class="stat-title">Pendientes</div>
                <div class="stat-number"><?= $totalPendientes ?></div>
            </div>
            <div class="stat-card" style="border-top-color: #10b981;">
                <div class="stat-title"> Aprobadas</div>
                <div class="stat-number"><?= $totalAprobados ?></div>
            </div>
            <div class="stat-card" style="border-top-color: #3b82f6;">
                <div class="stat-title"> En tránsito</div>
                <div class="stat-number"><?= $totalEnTransito ?></div>
            </div>
            <div class="stat-card" style="border-top-color: #8b5cf6;">
                <div class="stat-title">Completadas</div>
                <div class="stat-number"><?= $totalCompletadas ?></div>
            </div>
            <div class="stat-card" style="border-top-color: #ef4444;">
                <div class="stat-title"> Rechazadas</div>
                <div class="stat-number"><?= $totalRechazadas ?></div>
            </div>
        </div>

        <!-- Mensaje informativo adicional -->
        <div class="card" style="background: #fef9e6; border-left: 4px solid #f59e0b;">
            <div class="card-body">
                <p style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.5rem;">ℹ</span>
                    <strong>Resumen general:</strong> Actualmente hay <strong><?= $totalPendientes ?></strong> solicitudes pendientes de revisión. 
                    Revisa cada una y actualiza su estado.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Accesos rápidos (común para ambos roles) -->
    <div class="card">
        <div class="card-header">Accesos rápidos</div>
        <div class="card-body">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <?php if ($_SESSION['rol'] === 'solicitante'): ?>
                    <a href="solicitudes.php" class="btn btn-primary"> Nueva solicitud</a>
                    <a href="mis_solicitudes.php" class="btn btn-secondary"> Mis solicitudes</a>
                <?php elseif ($_SESSION['rol'] === 'revisor'): ?>
                    <a href="listar.php" class="btn btn-secondary"> Panel de revisión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

</body>
</html>
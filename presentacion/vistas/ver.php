<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../index.php");
    exit();
}
require_once '../../negocio/SolicitudService.php';
$id = intval($_GET['id'] ?? 0);
$service = new SolicitudService();
$solicitud = $service->obtenerSolicitud($id);
$detalles = $service->obtenerDetalles($id);
if (!$solicitud) die("Solicitud no encontrada");
$volver = ($_SESSION['rol'] == 'revisor') ? 'listar.php' : 'mis_solicitudes.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de solicitud</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Mejoras específicas para el detalle */
        .detail-card {
            background: #f9fafb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-item .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .detail-item .value {
            font-size: 1rem;
            font-weight: 500;
            color: #1f2937;
            word-break: break-word;
        }
        .estado-completada { color: #059669; font-weight: 600; background: #d1fae5; display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; }
        .estado-aprobado { color: #10b981; background: #d1fae5; }
        .estado-pendiente { color: #d97706; background: #fef3c7; }
        .estado-rechazado { color: #dc2626; background: #fee2e2; }
        .estado-transito { color: #3b82f6; background: #dbeafe; }
        .detalle-item-list {
            background: white;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-left: 4px solid #064c2b;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .badge-mesero {
            font-size: 0.75rem;
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
        }
    </style>
</head>
<body>

<?php include '../layout/sidebar.php'; ?>

<main class="main-content">
    <div class="card">
        <div class="card-header">
            <h2>Detalle de la solicitud #<?= htmlspecialchars($solicitud['numero_radicado']) ?></h2>
        </div>
        <div class="card-body">
            <!-- Información principal en tarjeta -->
            <div class="detail-card">
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="label">Radicado</span>
                        <span class="value"><?= htmlspecialchars($solicitud['numero_radicado']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Solicitante</span>
                        <span class="value"><?= htmlspecialchars($solicitud['solicitante_nombre'] . ' ' . ($solicitud['apellido']??'')) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Correo</span>
                        <span class="value"><?= htmlspecialchars($solicitud['email']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Teléfono</span>
                        <span class="value"><?= htmlspecialchars($solicitud['telefono']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Evento</span>
                        <span class="value"><?= htmlspecialchars($solicitud['nombre_evento']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Lugar</span>
                        <span class="value"><?= htmlspecialchars($solicitud['lugar_evento']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Fechas</span>
                        <span class="value"><?= $solicitud['fecha_inicio'] ?> al <?= $solicitud['fecha_fin'] ?> (<?= $solicitud['cantidad_dias'] ?> días)</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Valor total</span>
                        <span class="value">$<?= number_format($solicitud['valor_total'],2) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Estado</span>
                        <span class="value">
                            <?php
                            $estado = $solicitud['estado_nombre'];
                            $clase = '';
                            switch($estado){
                                case 'Pendiente': $clase = 'estado-pendiente'; break;
                                case 'Aprobado': $clase = 'estado-aprobado'; break;
                                case 'Rechazado': $clase = 'estado-rechazado'; break;
                                case 'En tránsito': $clase = 'estado-transito'; break;
                                default: $clase = 'estado-completada';
                            }
                            ?>
                            <span class="<?= $clase ?>" style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px;"><?= $estado ?></span>
                        </span>
                    </div>
                </div>
                <div class="detail-item" style="margin-top: 1rem;">
                    <span class="label">Justificación</span>
                    <div class="value" style="background: white; padding: 0.75rem; border-radius: 0.5rem; margin-top: 0.25rem;">
                        <?= nl2br(htmlspecialchars($solicitud['justificacion'])) ?>
                    </div>
                </div>
            </div>

            <!-- Detalles de alimentación -->
            <h3 style="margin: 1.5rem 0 1rem; font-size: 1.25rem; font-weight: 600;">Detalles de alimentación</h3>
            <?php if (empty($detalles)): ?>
                <p class="text-gray-500">No hay detalles registrados.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php foreach ($detalles as $det): ?>
                        <div class="detalle-item-list">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">
                                <div><strong>Día:</strong> <?= htmlspecialchars($det['dia']) ?></div>
                                <div><strong>Hora:</strong> <?= htmlspecialchars($det['hora']) ?></div>
                                <div><strong>Cantidad:</strong> <?= htmlspecialchars($det['cantidad']) ?></div>
                                <div><strong>Menú:</strong> <?= htmlspecialchars($det['alimentos']) ?></div>
                                <div><strong>Tipo:</strong> <?= htmlspecialchars($det['tipo_solicitud']) ?></div>
                                <div><strong>Meseros:</strong> <span class="badge-mesero"><?= htmlspecialchars($det['requiere_mesero']) ?></span></div>
                                <div><strong>Lugar:</strong> <?= htmlspecialchars($det['lugar_entrega']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div style="margin-top: 2rem; text-align: right;">
                <a href="<?= $volver ?>" class="btn btn-primary">Volver</a>
            </div>
        </div>
    </div>
</main>

</body>
</html>
<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'solicitante') {
    header("Location: ../../index.php");
    exit();
}
require_once '../../capa_acceso/dao/CatalogoDAO.php';
require_once '../../negocio/SolicitudService.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: mis_solicitudes.php");
    exit();
}
$service = new SolicitudService();
$solicitud = $service->obtenerSolicitud($id);
$detalles = $service->obtenerDetalles($id);
if (!$solicitud || $solicitud['usuario_id'] != $_SESSION['id_usuario']) {
    header("Location: mis_solicitudes.php");
    exit();
}

$catalogo = new CatalogoDAO();
$fondos = $catalogo->listarFondos();
$centrosCosto = $catalogo->listarCentrosCosto();
$funciones = $catalogo->listarFunciones();
$dependencias = $catalogo->listarDependencias();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar solicitud</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .item-detalle { background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1rem; }
        .detalle-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .detalle-grid .full-width { grid-column: 1 / -1; }
        .form-group-modern { margin-bottom: 0.75rem; }
        .form-group-modern label { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280; margin-bottom: 0.25rem; }
        .form-group-modern input, .form-group-modern select { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; }
        .btn-sm { padding: 0.25rem 0.75rem; font-size: 0.75rem; }
    </style>
</head>
<body>

<?php include '../layout/sidebar.php'; ?>

<main class="main-content">
    <div class="card">
        <div class="card-header">
            <h2>Editar solicitud #<?= htmlspecialchars($solicitud['numero_radicado']) ?></h2>
        </div>
        <div class="card-body">
            <form id="formEditarSolicitud" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $solicitud['id'] ?>">
                
                <!-- INFORMACIÓN DE LA SOLICITUD -->
                <div class="form-group">
                    <label>Fecha de solicitud</label>
                    <input type="date" name="fecha_solicitud" value="<?= $solicitud['fecha_solicitud'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Tipo de servicio</label>
                    <select name="tipo" required>
                        <option value="">Seleccione...</option>
                        <option value="Refrigerio" <?= $solicitud['tipo_servicio'] == 'Refrigerio' ? 'selected' : '' ?>>Refrigerio</option>
                        <option value="Almuerzo" <?= $solicitud['tipo_servicio'] == 'Almuerzo' ? 'selected' : '' ?>>Almuerzo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Valor</label>
                    <input name="valor" type="number" step="0.01" value="<?= $solicitud['valor_total'] ?>">
                </div>

                <!-- INFORMACIÓN ADMINISTRATIVA -->
                <h3 style="margin: 1.5rem 0 1rem;">Información administrativa</h3>
                <div class="form-group">
                    <label>Dependencia</label>
                    <select name="dependencia_id" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($dependencias as $dep): ?>
                            <option value="<?= $dep['id'] ?>" <?= $solicitud['dependencia_id'] == $dep['id'] ? 'selected' : '' ?>><?= htmlspecialchars($dep['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fondo</label>
                    <select name="fondo_id" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($fondos as $f): ?>
                            <option value="<?= $f['id'] ?>" <?= $solicitud['fondo_id'] == $f['id'] ? 'selected' : '' ?>><?= htmlspecialchars($f['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Centro de costos</label>
                    <select name="centro_costo_id" id="centro_costos" onchange="asignarCentro()" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($centrosCosto as $cc): ?>
                            <option value="<?= $cc['id'] ?>" data-codigo="<?= $cc['codigo'] ?>" <?= $solicitud['centro_costo_id'] == $cc['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cc['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Número centro de costos</label>
                    <input name="numero_centro_costos" id="codigo_centro" readonly value="<?= htmlspecialchars($solicitud['centro_costo_id']) ?>">
                </div>
                <div class="form-group">
                    <label>Función</label>
                    <select name="funcion_id" id="funcion" onchange="asignarFuncion()" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($funciones as $fn): ?>
                            <option value="<?= $fn['id'] ?>" data-codigo="<?= $fn['codigo'] ?>" <?= $solicitud['funcion_id'] == $fn['id'] ? 'selected' : '' ?>><?= htmlspecialchars($fn['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Número de función</label>
                    <input name="numero_funcion" id="codigo_funcion" readonly value="<?= htmlspecialchars($solicitud['funcion_id']) ?>">
                </div>
                <div class="form-group">
                    <label>Disponibilidad presupuestal</label>
                    <input type="number" name="disponibilidad" value="<?= $solicitud['disponibilidad_presupuestal'] ?>" required>
                </div>

                <!-- DATOS DEL SOLICITANTE -->
                <h3 style="margin: 1.5rem 0 1rem;">Datos del solicitante</h3>
                <div class="form-group">
                    <label>Nombre completo</label>
                    <input name="nombre" value="<?= htmlspecialchars($_SESSION['usuario']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="email" value="<?= $_SESSION['email'] ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input name="telefono" value="<?= htmlspecialchars($solicitud['telefono']) ?>">
                </div>
                <div class="form-group">
                    <label>Cargo</label>
                    <input name="cargo" value="<?= htmlspecialchars($solicitud['cargo_solicitante']) ?>">
                </div>
                <div class="form-group">
                    <label>Dependencia (texto)</label>
                    <input name="dependencia_texto">
                </div>

                <!-- INFORMACIÓN DEL EVENTO -->
                <h3 style="margin: 1.5rem 0 1rem;">Información del evento</h3>
                <div class="form-group">
                    <label>Nombre del evento</label>
                    <input name="evento" value="<?= htmlspecialchars($solicitud['nombre_evento']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Lugar del evento</label>
                    <input name="lugar" value="<?= htmlspecialchars($solicitud['lugar_evento']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Días del evento</label>
                    <select name="dias" required>
                        <option value="">Seleccione...</option>
                        <option value="1" <?= $solicitud['cantidad_dias'] == 1 ? 'selected' : '' ?>>1 día</option>
                        <option value="2" <?= $solicitud['cantidad_dias'] == 2 ? 'selected' : '' ?>>2 días</option>
                        <option value="3" <?= $solicitud['cantidad_dias'] == 3 ? 'selected' : '' ?>>3 días</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" value="<?= $solicitud['fecha_inicio'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Fecha de fin</label>
                    <input type="date" name="fecha_fin" value="<?= $solicitud['fecha_fin'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Justificación</label>
                    <textarea name="justificacion" rows="4" required><?= htmlspecialchars($solicitud['justificacion']) ?></textarea>
                </div>

                <!-- DETALLE DE ALIMENTACIÓN -->
                <h3 style="margin: 1.5rem 0 1rem;">Detalle de alimentación</h3>
                <div id="detalles">
                    <?php foreach ($detalles as $det): ?>
                    <div class="item-detalle">
                        <div class="detalle-grid">
                            <div class="form-group-modern"><label>Día</label><input name="dia[]" value="<?= htmlspecialchars($det['dia']) ?>" required></div>
                            <div class="form-group-modern"><label>Hora</label><input type="time" name="hora[]" value="<?= htmlspecialchars($det['hora']) ?>" required></div>
                            <div class="form-group-modern"><label>Cantidad</label><input type="number" name="cantidad_item[]" value="<?= $det['cantidad'] ?>" min="1" required></div>
                            <div class="form-group-modern"><label>Menú</label><input name="menu[]" value="<?= htmlspecialchars($det['alimentos']) ?>" required></div>
                            <div class="form-group-modern"><label>Tipo</label>
                                <select name="tipo_item[]" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Refrigerio" <?= $det['tipo_solicitud'] == 'Refrigerio' ? 'selected' : '' ?>>Refrigerio</option>
                                    <option value="Almuerzo" <?= $det['tipo_solicitud'] == 'Almuerzo' ? 'selected' : '' ?>>Almuerzo</option>
                                </select>
                            </div>
                            <div class="form-group-modern"><label>Meseros</label>
                                <select name="meseros_item[]" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Si" <?= $det['requiere_mesero'] == 'Si' ? 'selected' : '' ?>>Sí</option>
                                    <option value="No" <?= $det['requiere_mesero'] == 'No' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="full-width form-group-modern"><label>Lugar de entrega</label><input name="lugar_entrega[]" value="<?= htmlspecialchars($det['lugar_entrega']) ?>" required></div>
                        </div>
                        <div style="margin-top: 1rem; text-align: right;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(this)">Eliminar ítem</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="agregarDetalle()">+ Agregar ítem</button>

                <!-- DOCUMENTO DE SOPORTE (opcional) -->
                <h3 style="margin: 1.5rem 0 1rem;">Documento de soporte (opcional)</h3>
                <div class="form-group">
                    <input type="file" name="archivo" accept="application/pdf">
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">Actualizar solicitud</button>
                    <a href="mis_solicitudes.php" class="btn btn-secondary">Cancelar</a>
                </div>
                <div id="mensaje" class="form-group" style="margin-top: 1rem;"></div>
            </form>
        </div>
    </div>
</main>

<script src="../js/editar_solicitud.js"></script>
</body>
</html>
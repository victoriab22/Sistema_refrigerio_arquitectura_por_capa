<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'solicitante') {
    header("Location: ../../index.php");
    exit();
}
require_once '../../capa_acceso/dao/CatalogoDAO.php';
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
    <title>Nueva solicitud</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .item-detalle {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.2s;
        }
        .item-detalle:hover {
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .detalle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .detalle-grid .full-width {
            grid-column: 1 / -1;
        }
        .form-group-modern {
            margin-bottom: 0.75rem;
        }
        .form-group-modern label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        .form-group-modern input, .form-group-modern select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .form-group-modern input:focus, .form-group-modern select:focus {
            outline: none;
            border-color: #064c2b;
            box-shadow: 0 0 0 3px rgba(6,76,43,0.1);
        }
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

<?php include '../layout/sidebar.php'; ?>

<main class="main-content">
    <div class="card">
        <div class="card-header">
            <h2>Solicitud de Refrigerio / Almuerzo</h2>
        </div>
        <div class="card-body">
            <form id="formSolicitud" enctype="multipart/form-data">
                <!-- INFORMACIÓN DE LA SOLICITUD -->
                <div class="form-group">
                    <label>Fecha de solicitud</label>
                    <input type="date" name="fecha_solicitud" required>
                </div>
                <div class="form-group">
                    <label>Tipo de servicio</label>
                    <select name="tipo" required>
                        <option value="">Seleccione...</option>
                        <option value="Refrigerio">Refrigerio</option>
                        <option value="Almuerzo">Almuerzo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Valor</label>
                    <input name="valor" type="number" step="0.01">
                </div>

                <!-- INFORMACIÓN ADMINISTRATIVA -->
                <h3 style="margin: 1.5rem 0 1rem;">Información administrativa</h3>
                <div class="form-group">
                    <label>Dependencia</label>
                    <select name="dependencia_id" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($dependencias as $dep): ?>
                            <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fondo</label>
                    <select name="fondo_id" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($fondos as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Centro de costos</label>
                    <select name="centro_costo_id" id="centro_costos" onchange="asignarCentro()" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($centrosCosto as $cc): ?>
                            <option value="<?= $cc['id'] ?>" data-codigo="<?= $cc['codigo'] ?>"><?= htmlspecialchars($cc['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Número centro de costos</label>
                    <input name="numero_centro_costos" id="codigo_centro" readonly>
                </div>
                <div class="form-group">
                    <label>Función</label>
                    <select name="funcion_id" id="funcion" onchange="asignarFuncion()" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($funciones as $fn): ?>
                            <option value="<?= $fn['id'] ?>" data-codigo="<?= $fn['codigo'] ?>"><?= htmlspecialchars($fn['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Número de función</label>
                    <input name="numero_funcion" id="codigo_funcion" readonly>
                </div>
                <div class="form-group">
                    <label>Disponibilidad presupuestal</label>
                    <input type="number" name="disponibilidad" required>
                </div>

                <!-- DATOS DEL SOLICITANTE -->
                <h3 style="margin: 1.5rem 0 1rem;">Datos del solicitante</h3>
                <div class="form-group">
                    <label>Nombre completo</label>
                    <input name="nombre" required>
                </div>
                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="email" value="<?= $_SESSION['email'] ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input name="telefono">
                </div>
                <div class="form-group">
                    <label>Cargo</label>
                    <input name="cargo">
                </div>
                <div class="form-group">
                    <label>Dependencia (texto)</label>
                    <input name="dependencia_texto">
                </div>

                <!-- INFORMACIÓN DEL EVENTO -->
                <h3 style="margin: 1.5rem 0 1rem;">Información del evento</h3>
                <div class="form-group">
                    <label>Nombre del evento</label>
                    <input name="evento" required>
                </div>
                <div class="form-group">
                    <label>Lugar del evento</label>
                    <input name="lugar" required>
                </div>
                <div class="form-group">
                    <label>Días del evento</label>
                    <select name="dias" required>
                        <option value="">Seleccione...</option>
                        <option value="1">1 día</option>
                        <option value="2">2 días</option>
                        <option value="3">3 días</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" required>
                </div>
                <div class="form-group">
                    <label>Fecha de fin</label>
                    <input type="date" name="fecha_fin" required>
                </div>
                <div class="form-group">
                    <label>Justificación</label>
                    <textarea name="justificacion" rows="4" required></textarea>
                </div>

                <!-- DETALLE DE ALIMENTACIÓN MEJORADO -->
                <h3 style="margin: 1.5rem 0 1rem;">Detalle de alimentación</h3>
                <div id="detalles">
                    <!-- Primer ítem -->
                    <div class="item-detalle">
                        <div class="detalle-grid">
                            <div class="form-group-modern">
                                <label>Día</label>
                                <input name="dia[]" required>
                            </div>
                            <div class="form-group-modern">
                                <label>Hora</label>
                                <input type="time" name="hora[]" required>
                            </div>
                            <div class="form-group-modern">
                                <label>Cantidad</label>
                                <input type="number" name="cantidad_item[]" min="1" required>
                            </div>
                            <div class="form-group-modern">
                                <label>Menú</label>
                                <input name="menu[]" required>
                            </div>
                            <div class="form-group-modern">
                                <label>Tipo</label>
                                <select name="tipo_item[]" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Refrigerio">Refrigerio</option>
                                    <option value="Almuerzo">Almuerzo</option>
                                </select>
                            </div>
                            <div class="form-group-modern">
                                <label>Meseros</label>
                                <select name="meseros_item[]" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Si">Sí</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="full-width form-group-modern">
                                <label>Lugar de entrega</label>
                                <input name="lugar_entrega[]" required>
                            </div>
                        </div>
                        <div style="margin-top: 1rem; text-align: right;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(this)">Eliminar ítem</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="agregarDetalle()" style="margin-top: 0.5rem;">+ Agregar ítem</button>

                <!-- DOCUMENTO DE SOPORTE -->
                <h3 style="margin: 1.5rem 0 1rem;">Documento de soporte</h3>
                <div class="form-group">
                    <input type="file" name="archivo" required accept="application/pdf">
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                </div>
                <div id="mensaje" class="form-group" style="margin-top: 1rem;"></div>
            </form>
        </div>
    </div>
</main>

<script src="../js/solicitud.js"></script>
</body>
</html>
<?php
session_start();
require_once __DIR__ . '/../negocio/SolicitudService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    // Construir array con los datos de la solicitud 
    $datosSolicitud = [
        'dependencia_id'          => $_POST['dependencia_id'] ?? 1,
        'fecha_solicitud'         => $_POST['fecha_solicitud'] ?? '',
        'tipo_servicio'           => $_POST['tipo'] ?? '',
        'justificacion'           => $_POST['justificacion'] ?? '',
        'valor_total'             => $_POST['valor'] ?? 0,
        'fondo_id'                => $_POST['fondo_id'] ?? null,
        'centro_costo_id'         => $_POST['centro_costo_id'] ?? null,
        'funcion_id'              => $_POST['funcion_id'] ?? null,
        'disponibilidad_presupuestal' => $_POST['disponibilidad'] ?? null,
        'telefono'                => $_POST['telefono'] ?? '',
        'cargo_solicitante'       => $_POST['cargo'] ?? '',
        'nombre_evento'           => $_POST['evento'] ?? '',
        'lugar_evento'            => $_POST['lugar'] ?? '',
        'cantidad_dias'           => $_POST['dias'] ?? 0,
        'fecha_inicio'            => $_POST['fecha_inicio'] ?? '',
        'fecha_fin'               => $_POST['fecha_fin'] ?? '',
        'email'                   => $_POST['email'] ?? $_SESSION['email']  // solo para validar
    ];

    $detalles = [];
    if (isset($_POST['dia']) && is_array($_POST['dia'])) {
        $count = count($_POST['dia']);
        for ($i = 0; $i < $count; $i++) {
            $detalles[] = [
                'dia'      => $_POST['dia'][$i] ?? '',
                'hora'     => $_POST['hora'][$i] ?? '',
                'cantidad' => $_POST['cantidad_item'][$i] ?? 0,
                'alimentos' => $_POST['menu'][$i] ?? '',
                'bebidas'  => '',
                'tipo'     => $_POST['tipo_item'][$i] ?? '',
                'mesero'   => $_POST['meseros_item'][$i] ?? '',
                'lugar'    => $_POST['lugar_entrega'][$i] ?? ''
            ];
        }
    }

    $archivo = $_FILES['archivo'] ?? null;

    $service = new SolicitudService();
    $id = $service->crearSolicitud($datosSolicitud, $detalles, $archivo, $_SESSION['id_usuario']);

    echo json_encode(['success' => true, 'id' => $id, 'mensaje' => 'Solicitud registrada correctamente']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
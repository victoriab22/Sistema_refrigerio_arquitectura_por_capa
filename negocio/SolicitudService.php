<?php
require_once __DIR__ . '/../capa_acceso/dao/SolicitudDAO.php';
require_once __DIR__ . '/../capa_acceso/dao/DetalleDAO.php';
require_once __DIR__ . '/../capa_acceso/dao/ArchivoAdjuntoDAO.php';
require_once __DIR__ . '/../capa_acceso/dao/CatalogoDAO.php';

class SolicitudService {
    private $solicitudDAO;
    private $detalleDAO;
    private $archivoDAO;
    private $catalogoDAO;

    public function __construct() {
        $this->solicitudDAO = new SolicitudDAO();
        $this->detalleDAO = new DetalleDAO();
        $this->archivoDAO = new ArchivoAdjuntoDAO();
        $this->catalogoDAO = new CatalogoDAO();
    }

    public function crearSolicitud($datosSolicitud, $detalles, $archivo, $usuarioId) {
        // ========== VALIDACIONES DE NEGOCIO ==========
        
        // Campos obligatorios generales
        if (empty($datosSolicitud['justificacion'])) {
            throw new Exception("La justificación es obligatoria");
        }
        if ($datosSolicitud['valor_total'] <= 0) {
            throw new Exception("El valor debe ser mayor a cero");
        }
        if (empty($detalles)) {
            throw new Exception("Debe agregar al menos un detalle de alimentación");
        }

        // Validar email
        if (empty($datosSolicitud['email']) || !filter_var($datosSolicitud['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El correo electrónico no es válido");
        }

        // Validar teléfono 
        if (!empty($datosSolicitud['telefono']) && !preg_match('/^[0-9]{7,15}$/', $datosSolicitud['telefono'])) {
            throw new Exception("El teléfono debe contener solo números y tener entre 7 y 15 dígitos");
        }

        // Validar que la fecha de solicitud no sea futura
        $fechaActual = date('Y-m-d');
        if (strtotime($datosSolicitud['fecha_solicitud']) > strtotime($fechaActual)) {
            throw new Exception("La fecha de solicitud no puede ser mayor a la fecha actual");
        }

        // Validar que la fecha de inicio no sea mayor a la fecha de fin
        if (strtotime($datosSolicitud['fecha_inicio']) > strtotime($datosSolicitud['fecha_fin'])) {
            throw new Exception("La fecha de inicio no puede ser mayor a la fecha de fin");
        }

        // Validar tipo de servicio permitido
        $tiposPermitidos = ['Refrigerio', 'Almuerzo'];
        if (!in_array($datosSolicitud['tipo_servicio'], $tiposPermitidos)) {
            throw new Exception("Tipo de servicio no válido");
        }

        // Validar existencia de IDs de catálogos
        $fondosIds = array_column($this->catalogoDAO->listarFondos(), 'id');
        if (!in_array($datosSolicitud['fondo_id'], $fondosIds)) {
            throw new Exception("El fondo seleccionado no es válido");
        }
        $centrosIds = array_column($this->catalogoDAO->listarCentrosCosto(), 'id');
        if (!in_array($datosSolicitud['centro_costo_id'], $centrosIds)) {
            throw new Exception("El centro de costo seleccionado no es válido");
        }
        $funcionesIds = array_column($this->catalogoDAO->listarFunciones(), 'id');
        if (!in_array($datosSolicitud['funcion_id'], $funcionesIds)) {
            throw new Exception("La función seleccionada no es válida");
        }
        $dependenciasIds = array_column($this->catalogoDAO->listarDependencias(), 'id');
        if (!in_array($datosSolicitud['dependencia_id'], $dependenciasIds)) {
            throw new Exception("La dependencia seleccionada no es válida");
        }

        // ========== PREPARAR DATOS PARA GUARDAR ==========
        
        unset($datosSolicitud['email']);
        
        // Agregar campos calculados
        $datosSolicitud['radicado'] = $this->solicitudDAO->generarRadicado();
        $datosSolicitud['usuario_id'] = $usuarioId;
        $datosSolicitud['estado_id'] = 1; // Pendiente

        // 1. Guardar solicitud
        $solicitudId = $this->solicitudDAO->guardar($datosSolicitud);

        // 2. Guardar cada detalle 
        foreach ($detalles as $det) {
            $det['id_solicitud'] = $solicitudId;
            $this->detalleDAO->guardar($det);
        }

        // 3. Guardar archivo adjunto
        if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
            $carpeta = __DIR__ . '/../uploads/';
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }
            $nombreUnico = time() . '_' . basename($archivo['name']);
            $ruta = 'uploads/' . $nombreUnico;
            move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreUnico);
            $this->archivoDAO->guardar($solicitudId, $archivo['name'], $ruta, $archivo['type'], $archivo['size']);
        }

        return $solicitudId;
    }

    public function actualizarSolicitud($datosSolicitud, $detalles, $archivo = null) {
    // Validaciones (reutilizamos las mismas que en crear, excepto generación de radicado)
    if (empty($datosSolicitud['justificacion'])) {
        throw new Exception("La justificación es obligatoria");
    }
    if ($datosSolicitud['valor_total'] <= 0) {
        throw new Exception("El valor debe ser mayor a cero");
    }
    if (empty($detalles)) {
        throw new Exception("Debe agregar al menos un detalle de alimentación");
    }

    if (!empty($datosSolicitud['email']) && !filter_var($datosSolicitud['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo electrónico no es válido");
    }

    if (!empty($datosSolicitud['telefono']) && !preg_match('/^[0-9]{7,15}$/', $datosSolicitud['telefono'])) {
        throw new Exception("El teléfono debe contener solo números y tener entre 7 y 15 dígitos");
    }

    $fechaActual = date('Y-m-d');
    if (strtotime($datosSolicitud['fecha_solicitud']) > strtotime($fechaActual)) {
        throw new Exception("La fecha de solicitud no puede ser mayor a la fecha actual");
    }

    if (strtotime($datosSolicitud['fecha_inicio']) > strtotime($datosSolicitud['fecha_fin'])) {
        throw new Exception("La fecha de inicio no puede ser mayor a la fecha de fin");
    }

    $tiposPermitidos = ['Refrigerio', 'Almuerzo'];
    if (!in_array($datosSolicitud['tipo_servicio'], $tiposPermitidos)) {
        throw new Exception("Tipo de servicio no válido");
    }

    // Validar catálogos
    $fondosIds = array_column($this->catalogoDAO->listarFondos(), 'id');
    if (!in_array($datosSolicitud['fondo_id'], $fondosIds)) {
        throw new Exception("El fondo seleccionado no es válido");
    }
    $centrosIds = array_column($this->catalogoDAO->listarCentrosCosto(), 'id');
    if (!in_array($datosSolicitud['centro_costo_id'], $centrosIds)) {
        throw new Exception("El centro de costo seleccionado no es válido");
    }
    $funcionesIds = array_column($this->catalogoDAO->listarFunciones(), 'id');
    if (!in_array($datosSolicitud['funcion_id'], $funcionesIds)) {
        throw new Exception("La función seleccionada no es válida");
    }
    $dependenciasIds = array_column($this->catalogoDAO->listarDependencias(), 'id');
    if (!in_array($datosSolicitud['dependencia_id'], $dependenciasIds)) {
        throw new Exception("La dependencia seleccionada no es válida");
    }

    unset($datosSolicitud['email']);

    // Actualizar solicitud
    $this->solicitudDAO->actualizar($datosSolicitud);

    // Eliminar detalles antiguos y volver a insertar
    $this->detalleDAO->eliminarPorSolicitud($datosSolicitud['id']);
    foreach ($detalles as $det) {
        $det['id_solicitud'] = $datosSolicitud['id'];
        $this->detalleDAO->guardar($det);
    }

    // Si hay nuevo archivo, guardarlo (opcional)
    if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
        $carpeta = __DIR__ . '/../uploads/';
        if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);
        $nombreUnico = time() . '_' . basename($archivo['name']);
        $ruta = 'uploads/' . $nombreUnico;
        move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreUnico);
        // No se guarda en archivos_adjuntos para simplificar, pero puedes hacerlo
    }

    return true;
}

    public function listarTodas() {
        return $this->solicitudDAO->listar();
    }

    public function listarPorUsuario($usuarioId) {
        return $this->solicitudDAO->listarPorUsuario($usuarioId);
    }

    public function obtenerSolicitud($id) {
        return $this->solicitudDAO->obtener($id);
    }

    public function obtenerDetalles($idSolicitud) {
        return $this->detalleDAO->listarPorSolicitud($idSolicitud);
    }

    public function actualizarEstado($id, $estadoId, $observacion = null, $usuarioId = null) {
        return $this->solicitudDAO->actualizarEstado($id, $estadoId, $observacion, $usuarioId);
    }

    public function eliminarSolicitud($id) {
        return $this->solicitudDAO->eliminar($id);
    }
}
<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'revisor') {
    header("Location: ../../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de revisión</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .form-control {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-family: inherit;
        }
        .form-control:focus {
            outline: none;
            border-color: #064c2b;
            box-shadow: 0 0 0 3px rgba(6,76,43,0.1);
        }
        .search-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .search-container input {
            flex: 1;
            min-width: 200px;
        }
        .search-container select {
            width: 160px;
        }
    </style>
</head>
<body>

<?php include '../layout/sidebar.php'; ?>

<main class="main-content">
    <div class="card">
        <div class="card-header">
            <h2>Panel de revisión de solicitudes</h2>
        </div>
        <div class="card-body">
            <div class="search-container">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 Buscar por radicado, solicitante o evento...">
                <select id="filterEstado" class="form-control">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Aprobado">Aprobado</option>
                    <option value="Rechazado">Rechazado</option>
                    <option value="En tránsito">En tránsito</option>
                    <option value="Completada">Completada</option>
                </select>
                <button id="btnRecargar" class="btn btn-secondary btn-sm">🔄 Recargar</button>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Radicado</th><th>Solicitante</th><th>Evento</th><th>Fecha</th><th>Valor</th><th>Estado</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSolicitudes">
                        <tr><td colspan="7" class="text-center">Cargando solicitudes...</td><tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    let todasSolicitudes = [];

    async function cargarSolicitudes() {
        const tbody = document.getElementById('tablaSolicitudes');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Cargando...</td></tr>';
        try {
            const resp = await fetch('../../controladores/ListarTodasSolicitudesAjax.php');
            if (!resp.ok) throw new Error('Error del servidor');
            todasSolicitudes = await resp.json();
            aplicarFiltro();
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-red-500">Error al cargar solicitudes</td></tr>';
        }
    }

    function aplicarFiltro() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const estadoFiltro = document.getElementById('filterEstado').value;
        let filtradas = todasSolicitudes;

        if (searchTerm) {
            filtradas = filtradas.filter(s => 
                s.numero_radicado.toLowerCase().includes(searchTerm) ||
                (s.solicitante_nombre && s.solicitante_nombre.toLowerCase().includes(searchTerm)) ||
                (s.nombre_evento && s.nombre_evento.toLowerCase().includes(searchTerm))
            );
        }
        if (estadoFiltro) {
            filtradas = filtradas.filter(s => s.estado_nombre === estadoFiltro);
        }
        renderizarTabla(filtradas);
    }

    function renderizarTabla(solicitudes) {
        const tbody = document.getElementById('tablaSolicitudes');
        if (solicitudes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay solicitudes</td></tr>';
            return;
        }
        tbody.innerHTML = '';
        solicitudes.forEach(sol => {
            let colorEstado = '';
            switch(sol.estado_nombre) {
                case 'Pendiente': colorEstado = 'orange'; break;
                case 'Aprobado': colorEstado = 'green'; break;
                case 'Rechazado': colorEstado = 'red'; break;
                case 'En tránsito': colorEstado = 'blue'; break;
                default: colorEstado = 'purple';
            }
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(sol.numero_radicado)}</td>
                <td>${escapeHtml(sol.solicitante_nombre)}</td>
                <td>${escapeHtml(sol.nombre_evento || '')}</td>
                <td>${sol.fecha_solicitud}</td>
                <td>$${parseFloat(sol.valor_total).toLocaleString()}</td>
                <td><span style="font-weight:bold; color:${colorEstado}">${sol.estado_nombre}</span></td>
                <td>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <a href="ver.php?id=${sol.id}" class="btn btn-primary btn-sm">Ver</a>
                        ${sol.estado_nombre === 'Pendiente' ? `
                            <button class="btn btn-secondary btn-sm" onclick="manejarAccion('aprobar', ${sol.id})">Aprobar</button>
                            <input type="text" id="comentario_${sol.id}" placeholder="Motivo" class="input-comentario" style="width: 100px; padding: 0.25rem;">
                            <button class="btn btn-danger btn-sm" onclick="manejarAccion('rechazar', ${sol.id})">Rechazar</button>
                        ` : (sol.estado_nombre === 'Aprobado' ? `
                            <button class="btn btn-secondary btn-sm" onclick="manejarAccion('transito', ${sol.id})">En tránsito</button>
                        ` : (sol.estado_nombre === 'En tránsito' ? `
                            <button class="btn btn-secondary btn-sm" onclick="manejarAccion('completar', ${sol.id})">Completar</button>
                        ` : ''))}
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    async function manejarAccion(accion, id) {
        let comentario = '';
        if (accion === 'rechazar') {
            comentario = document.getElementById(`comentario_${id}`).value;
            if (!comentario.trim()) {
                alert('Debe ingresar un motivo de rechazo');
                return;
            }
        }
        const formData = new FormData();
        formData.append('accion', accion);
        formData.append('id', id);
        if (comentario) formData.append('comentario', comentario);
        try {
            const resp = await fetch('../../controladores/RevisionController.php', { method: 'POST', body: formData });
            if (resp.ok) {
                alert('Estado actualizado correctamente');
                cargarSolicitudes();  // Recarga la tabla sin recargar la página
            } else {
                const error = await resp.text();
                alert('Error: ' + error);
            }
        } catch (error) {
            alert('Error de conexión');
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    document.getElementById('searchInput').addEventListener('input', aplicarFiltro);
    document.getElementById('filterEstado').addEventListener('change', aplicarFiltro);
    document.getElementById('btnRecargar').addEventListener('click', cargarSolicitudes);
    cargarSolicitudes();
</script>
</body>
</html>
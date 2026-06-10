document.addEventListener("DOMContentLoaded", () => {
    const formulario = document.getElementById("formEditarSolicitud");
    if (formulario) {
        formulario.addEventListener("submit", enviarEdicion);
    }
});

async function enviarEdicion(e) {
    e.preventDefault();
    const formulario = e.target;
    const nombre = formulario.nombre.value.trim();

    if (nombre === "") {
        alert("El nombre es obligatorio");
        return;
    }

    let cantidades = document.querySelectorAll('[name="cantidad_item[]"]');
    let cantidadValida = false;
    cantidades.forEach(campo => {
        if (parseInt(campo.value) > 0) cantidadValida = true;
    });
    if (!cantidadValida) {
        alert("Debe registrar al menos una cantidad válida");
        return;
    }

    const fechaInicio = formulario.fecha_inicio.value;
    const fechaFin = formulario.fecha_fin.value;
    if (fechaInicio && fechaFin) {
        const inicio = new Date(fechaInicio);
        const fin = new Date(fechaFin);
        if (inicio > fin) {
            alert("La fecha de inicio no puede ser mayor que la fecha final");
            return;
        }
    }

    const archivo = formulario.archivo.files[0];
    if (archivo) {
        const extension = archivo.name.split(".").pop().toLowerCase();
        if (extension !== "pdf") {
            alert("Solo se permiten archivos PDF");
            return;
        }
        if (archivo.size > 2 * 1024 * 1024) {
            alert("El archivo supera los 2 MB");
            return;
        }
    }

    const datos = new FormData(formulario);
    const mensajeDiv = document.getElementById("mensaje");

    try {
        const respuesta = await fetch("../../controladores/EditarSolicitudController.php", {
            method: "POST",
            body: datos
        });
        let resultado;
        try {
            resultado = await respuesta.json();
        } catch (e) {
            throw new Error("El servidor no devolvió una respuesta válida");
        }

        if (respuesta.ok && resultado.success) {
            mensajeDiv.innerHTML = `<div style="background:#c8e6c9; color:#1b5e20; padding:10px; border-radius:8px;">✅ ${resultado.mensaje}</div>`;
            setTimeout(() => {
                window.location.href = "mis_solicitudes.php";
            }, 1500);
        } else {
            mensajeDiv.innerHTML = `<div style="background:#ffcdd2; color:#c62828; padding:10px; border-radius:8px;">❌ ${resultado.error || "Error desconocido"}</div>`;
        }
    } catch (error) {
        console.error(error);
        mensajeDiv.innerHTML = `<div style="background:#ffcdd2; color:#c62828; padding:10px; border-radius:8px;">❌ Ocurrió un error al enviar la solicitud</div>`;
        alert("Ocurrió un error al enviar la solicitud");
    }
}

function asignarCentro() {
    let select = document.getElementById("centro_costos");
    let codigo = select.options[select.selectedIndex].dataset.codigo;
    document.getElementById("codigo_centro").value = codigo || "";
}

function asignarFuncion() {
    let select = document.getElementById("funcion");
    let codigo = select.options[select.selectedIndex].dataset.codigo;
    document.getElementById("codigo_funcion").value = codigo || "";
}

function agregarDetalle() {
    const div = document.createElement("div");
    div.classList.add("item-detalle");
    div.innerHTML = `
        <div class="detalle-grid">
            <div class="form-group-modern"><label>Día</label><input name="dia[]" required></div>
            <div class="form-group-modern"><label>Hora</label><input type="time" name="hora[]" required></div>
            <div class="form-group-modern"><label>Cantidad</label><input type="number" name="cantidad_item[]" min="1" required></div>
            <div class="form-group-modern"><label>Menú</label><input name="menu[]" required></div>
            <div class="form-group-modern"><label>Tipo</label>
                <select name="tipo_item[]" required>
                    <option value="">Seleccione...</option>
                    <option value="Refrigerio">Refrigerio</option>
                    <option value="Almuerzo">Almuerzo</option>
                </select>
            </div>
            <div class="form-group-modern"><label>Meseros</label>
                <select name="meseros_item[]" required>
                    <option value="">Seleccione...</option>
                    <option value="Si">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
            <div class="full-width form-group-modern"><label>Lugar de entrega</label><input name="lugar_entrega[]" required></div>
        </div>
        <div style="margin-top: 1rem; text-align: right;">
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(this)">Eliminar ítem</button>
        </div>
    `;
    document.getElementById("detalles").appendChild(div);
}

function eliminarDetalle(boton) {
    const item = boton.closest('.item-detalle');
    if (item && document.querySelectorAll('.item-detalle').length > 1) {
        item.remove();
    } else {
        alert("Debe haber al menos un ítem de detalle.");
    }
}
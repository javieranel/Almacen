<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Salidas</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="../css/style.css">

</head>

<style>
.table {
    width: 100% !important;
}

.dataTables_wrapper {
    overflow-x: auto;
}

table.dataTable td {
    vertical-align: middle;
    white-space: nowrap; /* 🔥 evita que se rompa el diseño */
}

td {
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
}

</style>



<?php include '../includes/navbar.php'; ?>

<body>

    <div class="container mt-4">

        <h4>📋 Historial de Salidas de Herramientas</h4>

        <div class="card p-3 table-responsive">
            <table id="tabla" class="table table-striped ">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Retira</th>
                        <th>Firma</th>
                        <th>Autoriza</th>
                        <th>Firma</th>
                        <th>Herramientas</th>
                        <th>Proyecto</th>
                        <th>Estado</th>
                        <th>Fecha Dev.</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

    <!-- MODAL FIRMA -->
    <div class="modal fade" id="modalFirma" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">

                <div class="modal-header">
                    <h5 class="modal-title">Firma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <img id="imgFirmaGrande" style="width:100%;">
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL HERRAMIENTAS -->
<div class="modal fade" id="modalHerramientas" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detalle de Herramientas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Herramienta</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody id="detalleHerramientas"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

    <!-- LIBRERÍAS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let tabla;

        $(document).ready(function() {

            tabla = $('#tabla').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                    order: [[0, 'desc']],
                    responsive: true,
                    scrollX: true
            });

            cargar();
        });

       // 🔄 CARGAR DATOS
function cargar() {

    fetch("../ajax/historial_salidas_Herramientas.php")
        .then(res => res.json())
        .then(data => {

            tabla.clear();

            data.forEach(s => {

                tabla.row.add([
                    s.fecha || '',
                    s.retira || '',

                    s.firma_retira ?
                    `<img src="${s.firma_retira}" width="80" style="cursor:pointer" onclick="verFirma('${s.firma_retira}')">` :
                    '',

                    s.autoriza || '',

                    s.firma_autoriza ?
                    `<img src="${s.firma_autoriza}" width="80" style="cursor:pointer" onclick="verFirma('${s.firma_autoriza}')">` :
                    '',

                    // 🔘 BOTÓN MODAL
                    `<button class="btn btn-sm btn-primary" 
                        onclick="verHerramientas('${s.herramienta}', '${s.cantidad}')">
                        Ver detalle
                    </button>`,

                    s.proyecto || '',

                    // ESTADO
                    s.status === 'Devuelto' ?
                    `<span class="badge bg-success">Devuelto</span>` :
                    `<span class="badge bg-danger">Prestado</span>`,

                    // FECHA DEV
                    s.fecha_devolucion ?
                    s.fecha_devolucion :
                    '<span class="text-muted">Pendiente</span>',

                    // BOTÓN DEVOLVER
                    (s.status === 'Prestado') ?
                    `<button class="btn btn-sm btn-success" onclick="devolver(${s.id})">Devolver</button>` :
                    `<span class="text-muted">OK</span>`
                ]);

            });

            tabla.draw();
        });
}

// 👁 VER FIRMA
function verFirma(src) {
    document.getElementById("imgFirmaGrande").src = src;
    new bootstrap.Modal(document.getElementById('modalFirma')).show();
}

// 🔍 VER HERRAMIENTAS
function verHerramientas(herramientas, cantidades) {

    let listaH = herramientas ? herramientas.split(', ') : [];
    let listaC = cantidades ? cantidades.split(', ') : [];

    let html = '';

    for (let i = 0; i < listaH.length; i++) {
        html += `
            <tr>
                <td>${listaH[i]}</td>
                <td>${listaC[i] || ''}</td>
            </tr>
        `;
    }

    document.getElementById('detalleHerramientas').innerHTML = html;

    new bootstrap.Modal(document.getElementById('modalHerramientas')).show();
}

// 🔁 DEVOLVER
function devolver(id) {

    Swal.fire({
        title: '¿Marcar como devuelto?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, devolver'
    }).then((result) => {

        if (result.isConfirmed) {

            fetch('../ajax/devolver_herramienta.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id: id })
            })
            .then(res => res.json())
            .then(resp => {

                if (resp.success) {
                    Swal.fire('Listo', 'Herramienta devuelta', 'success');
                    cargar();
                } else {
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }

            });

        }

    });
}
</script>

</body>

</html>
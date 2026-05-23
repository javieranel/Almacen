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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="../css/style.css">



</head>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">

    <h4>Historial de Salidas</h4>

    <div class="card p-3">
        <table id="tabla" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Retira</th>
                    <th>Firma</th>
                    <th>Autoriza</th>
                    <th>Firma</th>
                    <th>Peachtree</th>
                    <th>Info</th>
                    <th>Estado</th>

                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="modalDetalle">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Detalle de Salida</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Insumo</th>
                            <th>Cantidad</th>
                            <th>Proyecto</th>
                        </tr>
                    </thead>
                    <tbody id="detalle"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="./js/script.js"> </script>

<script>
    let tabla;

    $(document).ready(function() {

        tabla = $('#tabla').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });

        cargar();
    });

    // CARGAR HISTORIAL
    function cargar() {

        fetch("../ajax/historial_salidas.php")
            .then(res => res.json())
            .then(data => {

                tabla.clear();

                data.forEach(s => {

                    let estadoBadge = '';

                    if (s.estado === 'pendiente') {
                        estadoBadge = `<span class="badge bg-warning text-dark">Pendiente</span>`;
                    } else {
                        estadoBadge = `<span class="badge bg-success">Procesado</span>`;
                    }

                    let botonProcesar = '';

                    if (s.estado === 'pendiente') {
                        botonProcesar = `
                        <button class="btn btn-success btn-sm" onclick="procesar(${s.id})">
                            ✔ Procesar
                        </button>
                    `;
                    } else {
                        botonProcesar = `<span class="badge bg-secondary">Procesado</span>`;
                    }

                    // Validar firmas
                    let firmaRetira = s.firma_retira ?
                        `<img src="${s.firma_retira}" width="80" style="cursor:pointer" onclick="verFirma('${s.firma_retira}')">` :
                        'Sin firma';

                    let firmaAutoriza = s.firma_autoriza ?
                        `<img src="${s.firma_autoriza}" width="80" style="cursor:pointer" onclick="verFirma('${s.firma_autoriza}')">` :
                        'Sin firma';

                    tabla.row.add([
                        s.id,
                        s.fecha,
                        s.retira,
                        firmaRetira,
                        s.autoriza,
                        firmaAutoriza,
                        estadoBadge,
                        `<button class="btn btn-info btn-sm" onclick="verDetalle(${s.id})">Ver</button>`,
                        botonProcesar
                    ]);

                });

                tabla.draw();

            });
    }

    // VER DETALLE
    function verDetalle(id) {

        fetch(`../ajax/detalle_salida.php?id=${id}`)
            .then(res => res.json())
            .then(data => {

                let html = "";

                data.forEach(d => {
                    html += `
<tr>
<td>${d.nombre}</td>
<td>${d.cantidad}</td>
<td>${d.proyecto}</td>
</tr>`;
                });

                document.getElementById("detalle").innerHTML = html;

                new bootstrap.Modal(document.getElementById('modalDetalle')).show();

            });
    }



    function procesar(id) {

        Swal.fire({
            title: '¿Confirmar?',
            text: '¿Ya registraste esto en Peachtree?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, procesar'
        }).then((result) => {

            if (result.isConfirmed) {

                fetch("../ajax/procesar_salida.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            id
                        })
                    })
                    .then(res => res.json())
                    .then(r => {
                        console.log(r); // 👈 DEBUG
                        if (r.ok) {
                            Swal.fire('Procesado', '', 'success');
                            cargar();
                        } else {
                            Swal.fire('Error', r.error, 'error');
                        }
                    });

            }

        });
    }


    function verFirma(src) {
        document.getElementById("imgFirmaGrande").src = src;
        new bootstrap.Modal(document.getElementById('modalFirma')).show();
    }
</script>

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


</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Herramientas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- css -->
    <link rel="stylesheet" href="../css/style.css">


</head>

<?php include '../includes/navbar.php'; ?>

<!-- CONTENIDO -->
<div class="container mt-4">

    <h4 class="mb-3">Gestión de Herramientas</h4>

    <!-- FORM -->
    <div class="card p-3 mb-3">
        <div class="row g-3 align-items-end">

            <div class="col-md-4">
                <label class="form-label">Cantidad</label>
                <input type="number" id="cantidad" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Nombre de la Herramienta</label>
                <input type="text" id="nombre" class="form-control" placeholder="Ej: Taladro">
            </div>

            <div class="col-md-4">
                <label class="form-label">Estado de Herramienta</label>
                <select id="estado" class="form-select">
                    <option value="">Seleccione estado</option>
                    <option value="Disponible">🟢 Disponible</option>
                    <option value="En Uso">🟡 En Uso</option>
                    <option value="Mantenimiento">🔵 Mantenimiento</option>
                    <option value="Dañada">🔴 Dañada</option>
                    <option value="Fuera de Servicio">⚫ Fuera de Servicio</option>
                </select>
            </div>

            <div class="col-md-4">
                <button class="btn btn-success w-100" onclick="guardar()">Guardar</button>
            </div>

        </div>
    </div>

    <!-- TABLA -->
    <div class="card p-3">
        <table id="tablaHerramientas" class="table table-striped">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Herramientas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" id="edit_cantidad" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre de la Herramienta</label>
                    <input type="text" id="edit_nombre" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Estado de Herramienta</label>
                    <select id="edit_estado" class="form-select">
                        <option value="">Seleccione estado</option>
                        <option value="Disponible">🟢 Disponible</option>
                        <option value="En Uso">🟡 En Uso</option>
                        <option value="Mantenimiento">🔵 Mantenimiento</option>
                        <option value="Dañada">🔴 Dañada</option>
                        <option value="Fuera de Servicio">⚫ Fuera de Servicio</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-success" onclick="actualizar()">Actualizar</button>
            </div>

        </div>
    </div>
</div>

<!-- JS LIBRERÍAS (ORDEN IMPORTANTE) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    let tabla;
    let idEditar = null;

    // INIT
    $(document).ready(function() {

        tabla = $('#tablaHerramientas').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });

        cargar();
    });

    // CARGAR
    function cargar() {

        fetch("../ajax/herramientas.php?accion=listar")
            .then(res => res.json())
            .then(data => {

                tabla.clear();

                data.forEach(i => {
                    tabla.row.add([
                        i.cantidad,
                        i.nombre,
                        i.estado,
                        `
                <button class="btn btn-warning btn-sm"
                onclick='editar(${i.id}, ${JSON.stringify(i.cantidad)}, ${JSON.stringify(i.nombre)}, ${JSON.stringify(i.estado)})'>
                Editar</button>

                <button class="btn btn-danger btn-sm"
                onclick="eliminar(${i.id})">
                Eliminar</button>
                `
                    ]);
                });

                tabla.draw();
            });
    }

    // GUARDAR
    function guardar() {


        let cantidad = document.getElementById("cantidad").value;
        let nombre = document.getElementById("nombre").value;
        let estado = document.getElementById("estado").value;

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'warning');
            return;
        }

        if (!estado) {
            Swal.fire('Error', 'Colocar el estado de la herramienta', 'warning');
            return;
        }

        Swal.fire({
            title: 'Guardando...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch("../ajax/herramientas.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    accion: "crear",
                    cantidad: cantidad,
                    nombre: nombre,
                    estado: estado
                })
            })
            .then(res => res.json())
            .then(data => {

                Swal.close();

                if (data.ok) {
                    Swal.fire('Éxito', 'Guardado correctamente', 'success');
                    cargar();
                    limpiar();
                } else {
                    Swal.fire('Error', 'No se pudo guardar', 'error');
                }
            });
    }

    // EDITAR
    function editar(id, cantidad, nombre, estado) {

        idEditar = id;
        document.getElementById("edit_cantidad").value = cantidad;
        document.getElementById("edit_nombre").value = nombre;
        document.getElementById("edit_estado").value = estado;

        new bootstrap.Modal(document.getElementById('modalEditar')).show();
    }

    // ACTUALIZAR
    function actualizar() {

        let cantidad = document.getElementById("edit_cantidad").value;
        let nombre = document.getElementById("edit_nombre").value;
        let estado = document.getElementById("edit_estado").value;

        fetch("../ajax/herramientas.php", {
                method: "POST",
                body: JSON.stringify({
                    accion: "editar",
                    id: idEditar,
                    cantidad: cantidad,
                    nombre: nombre,
                    estado: estado
                })
            })
            .then(res => res.json())
            .then(data => {

                if (data.ok) {
                    Swal.fire('Actualizado', '', 'success');
                    cargar();
                }

                bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
            });
    }

    // ELIMINAR
    function eliminar(id) {

        Swal.fire({
            title: '¿Eliminar?',
            icon: 'warning',
            showCancelButton: true
        }).then((result) => {

            if (result.isConfirmed) {

                fetch("../ajax/herramientas.php", {
                        method: "POST",
                        body: JSON.stringify({
                            accion: "eliminar",
                            id: id
                        })
                    })
                    .then(() => {
                        Swal.fire('Eliminado', '', 'success');
                        cargar();
                    });

            }
        });
    }

    // LIMPIAR
    function limpiar() {
        document.getElementById("cantidad").value = "";
        document.getElementById("nombre").value = "";
        document.getElementById("estado").value = "";
    }

    // LIMPIAR MODAL
    document.getElementById('modalEditar').addEventListener('hidden.bs.modal', function() {
        document.getElementById("edit_cantidad").value = "";
        document.getElementById("edit_nombre").value = "";
        document.getElementById("edit_estado").value = "";
    });
</script>


<?php include "../includes/footer.php"; ?>
</body>

</html>
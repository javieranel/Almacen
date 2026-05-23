<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Insumos</title>
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

    <h4 class="mb-3">Gestión de Insumos</h4>

    <!-- FORM -->
    <div class="card p-3 mb-3">
        <div class="row g-2">

            <div class="col-md-4">
                <input type="text" id="nombre" class="form-control" placeholder="Nombre del insumo">
            </div>

            <div class="col-md-4">
                <input type="text" id="descripcion" class="form-control" placeholder="Descripción">
            </div>

            <div class="col-md-4">
                <button class="btn btn-success w-100" onclick="guardar()">Guardar</button>
            </div>

        </div>
    </div>

    <!-- TABLA -->
    <div class="card p-3">
        <table id="tablaInsumos" class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
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
        <h5 class="modal-title">Editar Insumo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="text" id="edit_nombre" class="form-control mb-2">
        <input type="text" id="edit_descripcion" class="form-control">
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
$(document).ready(function(){

    tabla = $('#tablaInsumos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    cargar();
});

// CARGAR
function cargar(){

    fetch("../ajax/insumos.php?accion=listar")
    .then(res => res.json())
    .then(data => {

        tabla.clear();

        data.forEach(i => {
            tabla.row.add([
                i.nombre,
                i.descripcion,
                `
                <button class="btn btn-warning btn-sm"
                onclick='editar(${i.id}, ${JSON.stringify(i.nombre)}, ${JSON.stringify(i.descripcion)})'>
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
function guardar(){

    let nombre = document.getElementById("nombre").value;
    let descripcion = document.getElementById("descripcion").value;

    if(!nombre){
        Swal.fire('Error', 'El nombre es obligatorio', 'warning');
        return;
    }

    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch("../ajax/insumos.php", {
        method: "POST",
        body: JSON.stringify({
            accion: "crear",
            nombre: nombre,
            descripcion: descripcion
        })
    })
    .then(res => res.json())
    .then(data => {

        Swal.close();

        if(data.ok){
            Swal.fire('Éxito', 'Guardado correctamente', 'success');
            cargar();
            limpiar();
        } else {
            Swal.fire('Error', 'No se pudo guardar', 'error');
        }
    });
}

// EDITAR
function editar(id, nombre, descripcion){

    idEditar = id;

    document.getElementById("edit_nombre").value = nombre;
    document.getElementById("edit_descripcion").value = descripcion;

    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}

// ACTUALIZAR
function actualizar(){

    let nombre = document.getElementById("edit_nombre").value;
    let descripcion = document.getElementById("edit_descripcion").value;

    fetch("../ajax/insumos.php", {
        method: "POST",
        body: JSON.stringify({
            accion: "editar",
            id: idEditar,
            nombre: nombre,
            descripcion: descripcion
        })
    })
    .then(res => res.json())
    .then(data => {

        if(data.ok){
            Swal.fire('Actualizado', '', 'success');
            cargar();
        }

        bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
    });
}

// ELIMINAR
function eliminar(id){

    Swal.fire({
        title: '¿Eliminar?',
        icon: 'warning',
        showCancelButton: true
    }).then((result) => {

        if(result.isConfirmed){

            fetch("../ajax/insumos.php", {
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
function limpiar(){
    document.getElementById("nombre").value = "";
    document.getElementById("descripcion").value = "";
}

// LIMPIAR MODAL
document.getElementById('modalEditar').addEventListener('hidden.bs.modal', function () {
    document.getElementById("edit_nombre").value = "";
    document.getElementById("edit_descripcion").value = "";
});

</script>


<?php include "../includes/footer.php"; ?>
</body>
</html>
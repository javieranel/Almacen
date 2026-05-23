<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Personas</title>
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

    <h4 class="mb-3">Gestión de Personas</h4>

    <!-- FORM -->
    <div class="card p-3 mb-3">
        <div class="row g-2">

            <div class="col-md-5">
                <input type="text" id="nombre" class="form-control" placeholder="Nombre">
            </div>

            <div class="col-md-4">
                <select id="tipo" class="form-control">
                    <option value="retira">Retira</option>
                    <option value="autoriza">Autoriza</option>
                    <option value="ambos">Ambos</option>
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-success w-100" onclick="guardar()">Guardar</button>
            </div>

        </div>
    </div>

    <!-- TABLA -->
    <div class="card p-3">
        <table id="tablaPersonas" class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
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
        <h5 class="modal-title">Editar Persona</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="text" id="edit_nombre" class="form-control mb-2">
        <select id="edit_tipo" class="form-control">
            <option value="retira">Retira</option>
            <option value="autoriza">Autoriza</option>
            <option value="ambos">Ambos</option>
        </select>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success" onclick="actualizar()">Actualizar</button>
      </div>

    </div>
  </div>
</div>

<!-- JS -->
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

    tabla = $('#tablaPersonas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    cargar();
});

// CARGAR
function cargar(){

    fetch("../ajax/personas.php?accion=listar")
    .then(res => res.json())
    .then(data => {

        tabla.clear();

        data.forEach(p => {
            tabla.row.add([
                p.nombre,
                p.tipo,
                `
                <button class="btn btn-warning btn-sm"
                onclick='editar(${p.id}, ${JSON.stringify(p.nombre)}, ${JSON.stringify(p.tipo)})'>
                Editar</button>

                <button class="btn btn-danger btn-sm"
                onclick="eliminar(${p.id})">
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
    let tipo = document.getElementById("tipo").value;

    if(!nombre){
        Swal.fire('Error', 'El nombre es obligatorio', 'warning');
        return;
    }

    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch("../ajax/personas.php", {
        method: "POST",
        body: JSON.stringify({
            accion: "crear",
            nombre: nombre,
            tipo: tipo
        })
    })
    .then(res => res.json())
    .then(data => {

        Swal.close();

        if(data.ok){
            Swal.fire('Éxito', 'Guardado correctamente', 'success');
            cargar();
            limpiar();
        }
    });
}

// EDITAR
function editar(id, nombre, tipo){

    idEditar = id;

    document.getElementById("edit_nombre").value = nombre;
    document.getElementById("edit_tipo").value = tipo;

    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}

// ACTUALIZAR
function actualizar(){

    let nombre = document.getElementById("edit_nombre").value;
    let tipo = document.getElementById("edit_tipo").value;

    fetch("../ajax/personas.php", {
        method: "POST",
        body: JSON.stringify({
            accion: "editar",
            id: idEditar,
            nombre: nombre,
            tipo: tipo
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

            fetch("../ajax/personas.php", {
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
}

// LIMPIAR MODAL
document.getElementById('modalEditar').addEventListener('hidden.bs.modal', function () {
    document.getElementById("edit_nombre").value = "";
});

</script>

<?php include "../includes/footer.php"; ?>

</body>
</html>
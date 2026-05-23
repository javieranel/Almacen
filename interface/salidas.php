<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Salida</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="../css/style.css">

<style>
.signature-pad {
    width: 100%;
    height: 200px;
    border: 1px solid #000;
    touch-action: none;
}
</style>
</head>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">

    <!-- ENCABEZADO -->
    <div class="card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <img src="../img/logo.png" class="logo">
            <h4 class="text-center flex-grow-1">Control de Salida de Insumos</h4>
        </div>
    </div>

    <!-- DATOS -->
    <div class="card p-3 mb-3">
        <div class="row">

            <div class="col-md-4">
                <label>Fecha</label>
                <input type="date" id="fecha" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Persona que Retira</label>
                <input type="text" id="retira_input" class="form-control" placeholder="Buscar...">
                <input type="hidden" id="retira_id">
                <div id="retira_lista" class="list-group"></div>
            </div>

            <div class="col-md-4">
                <label>Persona que Autoriza</label>
                <input type="text" id="autoriza_input" class="form-control" placeholder="Buscar...">
                <input type="hidden" id="autoriza_id">
                <div id="autoriza_lista" class="list-group"></div>
            </div>

        </div>
    </div>

    <!-- TABLA -->
    <div class="card p-3 mb-3">
        <h5>Detalle de Insumos</h5>

        <table class="table table-bordered" id="tabla">
            <thead class="table-light">
                <tr>
                    <th>Insumo</th>
                    <th>Cantidad</th>
                    <th>Proyecto</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button class="btn btn-primary" onclick="agregarFila()">+ Agregar</button>
    </div>

    <!-- FIRMAS -->
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3">
                <h6>Firma Retira</h6>
                <canvas id="firma1" class="signature-pad"></canvas>
                <button class="btn btn-danger btn-sm mt-2" onclick="limpiarFirma('firma1')">Limpiar</button>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3">
                <h6>Firma Autoriza</h6>
                <canvas id="firma2" class="signature-pad"></canvas>
                <button class="btn btn-danger btn-sm mt-2" onclick="limpiarFirma('firma2')">Limpiar</button>
            </div>
        </div>
    </div>

    <div class="text-end mt-3">
        <button class="btn btn-success" onclick="guardar()">Guardar</button>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

<!-- LIBRERÍAS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SIGNATURE PAD (PRIMERO) -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>

      // AUTOCOMPLETE PERSONAS
    function activarAutocomplete(inputId, listaId, hiddenId) {

        let input = document.getElementById(inputId);
        let lista = document.getElementById(listaId);
        let hidden = document.getElementById(hiddenId);

        input.addEventListener("keyup", function() {

            let valor = this.value;

            if (valor.length < 2) {
                lista.innerHTML = "";
                return;
            }

            fetch(`../ajax/buscar_personas.php?term=${valor}`)
                .then(res => res.json())
                .then(data => {

                    lista.innerHTML = "";

                    data.forEach(item => {

                        let div = document.createElement("a");
                        div.classList.add("list-group-item", "list-group-item-action");
                        div.textContent = item.nombre;

                        div.onclick = function() {
                            input.value = item.nombre;
                            hidden.value = item.id;
                            lista.innerHTML = "";
                        }

                        lista.appendChild(div);

                    });
                });
        });
    }

    activarAutocomplete("retira_input", "retira_lista", "retira_id");
    activarAutocomplete("autoriza_input", "autoriza_lista", "autoriza_id");

    // AUTOCOMPLETE INSUMOS
    function activarAutocompleteInsumos() {

        document.querySelectorAll(".insumo_input").forEach(input => {

            input.addEventListener("keyup", function() {

                let valor = this.value;
                let fila = this.closest("td");
                let lista = fila.querySelector(".lista_insumos");
                let hidden = fila.querySelector(".insumo_id");

                if (valor.length < 2) {
                    lista.innerHTML = "";
                    return;
                }

                fetch(`../ajax/buscar_insumos.php?term=${valor}`)
                    .then(res => res.json())
                    .then(data => {

                        lista.innerHTML = "";

                        data.forEach(item => {

                            let div = document.createElement("a");
                            div.classList.add("list-group-item", "list-group-item-action");
                            div.textContent = item.nombre;

                            div.onclick = function() {
                                input.value = item.nombre;
                                hidden.value = item.id;
                                lista.innerHTML = "";
                            }

                            lista.appendChild(div);

                        });
                    });
            });
        });
    }

    // AGREGAR FILA
    function agregarFila() {

        let fila = `
<tr>
<td>
<input type="text" class="form-control insumo_input" placeholder="Buscar...">
<input type="hidden" class="insumo_id">
<div class="list-group lista_insumos"></div>
</td>
<td><input type="number" class="form-control cantidad"></td>
<td><input type="text" class="form-control proyecto"></td>
<td><button class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
</tr>`;

        document.querySelector("#tabla tbody").insertAdjacentHTML("beforeend", fila);

        activarAutocompleteInsumos();
    }

    let pads = {};

    // Ajustar canvas (CLAVE para tablet)
    function resizeCanvas(canvas) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
    }

    // Inicializar firmas
    function iniciarFirmas() {
        document.querySelectorAll('.signature-pad').forEach(canvas => {
            resizeCanvas(canvas);
            pads[canvas.id] = new SignaturePad(canvas);
        });
    }

    // Limpiar firma
    function limpiarFirma(id) {
        if (pads[id]) {
            pads[id].clear();
        }
    }

    // INICIAR AL CARGAR
    window.addEventListener("load", iniciarFirmas);


    // ===============================
    // MODIFICA TU FUNCIÓN GUARDAR
    // ===============================
    function guardar() {

        let retira = document.getElementById("retira_id").value;
        let autoriza = document.getElementById("autoriza_id").value;
        let fecha = document.getElementById("fecha").value;

        // 👇 AQUÍ ESTÁ EL CAMBIO IMPORTANTE
        let firma1 = pads["firma1"] && !pads["firma1"].isEmpty() ? pads["firma1"].toDataURL() : "";
        let firma2 = pads["firma2"] && !pads["firma2"].isEmpty() ? pads["firma2"].toDataURL() : "";

        if (!retira || !autoriza) {
            Swal.fire('Error', 'Selecciona personas', 'warning');
            return;
        }

        if (!firma1 || !firma2) {
            Swal.fire('Error', 'Debe firmar ambas personas', 'warning');
            return;
        }

        let detalle = [];

        document.querySelectorAll("#tabla tbody tr").forEach(f => {

            let insumo = f.querySelector(".insumo_id").value;
            let cantidad = f.querySelector(".cantidad").value;
            let proyecto = f.querySelector(".proyecto").value;

            if (insumo && cantidad) {
                detalle.push({
                    insumo,
                    cantidad,
                    proyecto
                });
            }
        });

        if (detalle.length === 0) {
            Swal.fire('Error', 'Agrega insumos', 'warning');
            return;
        }

        Swal.fire({
            title: 'Guardando...',
            didOpen: () => Swal.showLoading()
        });

        fetch("../ajax/salidas.php", {
                method: "POST",
                body: JSON.stringify({
                    fecha,
                    retira,
                    autoriza,
                    firma1,
                    firma2,
                    detalle
                })
            })
            .then(res => res.text())
            .then(r => {

                console.log("RESPUESTA:", r);

                try {
                    let data = JSON.parse(r);

                    Swal.close();

                    if (data.ok) {
                        Swal.fire('Guardado', '', 'success');
                        limpiarFormulario();
                    } else {
                        Swal.fire('Error', 'No se guardó', 'error');
                    }

                } catch (e) {
                    Swal.fire('Error', 'Respuesta inválida del servidor', 'error');
                }
            });
    }


    function limpiarFormulario() {

    document.getElementById("fecha").value = "";
    document.getElementById("retira_input").value = "";
    document.getElementById("retira_id").value = "";
    document.getElementById("autoriza_input").value = "";
    document.getElementById("autoriza_id").value = "";

    document.querySelector("#tabla tbody").innerHTML = "";

    if (pads["firma1"]) pads["firma1"].clear();
    if (pads["firma2"]) pads["firma2"].clear();
}

document.getElementById("fecha").valueAsDate = new Date();
</script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
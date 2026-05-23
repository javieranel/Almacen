
document.querySelector(".btn-success").addEventListener("click", function(){

    let fecha = document.querySelector("input[type='date']").value;

    let retira = document.querySelectorAll("input")[1].value;
    let autoriza = document.querySelectorAll("input")[2].value;

    let firma1 = document.getElementById("firma1").toDataURL();
    let firma2 = document.getElementById("firma2").toDataURL();

    let filas = document.querySelectorAll("#tabla tbody tr");

    let detalle = [];

    filas.forEach(fila => {
        let inputs = fila.querySelectorAll("input");

        detalle.push({
            descripcion: inputs[0].value,
            cantidad: inputs[1].value,
            proyecto: inputs[2].value,
            id_insumo: 1 // luego lo conectamos a BD
        });
    });

    fetch("guardar.php", {
        method: "POST",
        body: JSON.stringify({
            fecha: fecha,
            retira: retira,
            autoriza: autoriza,
            firma1: firma1,
            firma2: firma2,
            detalle: detalle
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === "ok"){
            alert("Guardado correctamente");
            location.reload();
        }else{
            alert("Error al guardar");
        }
    });

});

function activarAutocomplete(inputId, listaId, hiddenId){

    let input = document.getElementById(inputId);
    let lista = document.getElementById(listaId);
    let hidden = document.getElementById(hiddenId);

    input.addEventListener("keyup", function(){

        let valor = this.value;

        if(valor.length < 2){
            lista.innerHTML = "";
            return;
        }

        fetch("../ajax/buscar_personas.php?term=" + valor)
        .then(res => res.json())
        .then(data => {

            lista.innerHTML = "";

            data.forEach(item => {

                let div = document.createElement("a");
                div.classList.add("list-group-item", "list-group-item-action");
                div.textContent = item.nombre;

                div.onclick = function(){
                    input.value = item.nombre;
                    hidden.value = item.id;
                    lista.innerHTML = "";
                }

                lista.appendChild(div);
            });

        });

    });

}

// ACTIVAR LOS DOS CAMPOS
activarAutocomplete("retira_input", "retira_lista", "retira_id");
activarAutocomplete("autoriza_input", "autoriza_lista", "autoriza_id");


webix.ui({
    view:"datatable",
    css:"my_style"
});



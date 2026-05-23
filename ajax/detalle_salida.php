<?php
include "../conn/conexion.php";

header('Content-Type: application/json');

$id = $_GET['id'];

$sql = "SELECT 
            i.nombre,
            d.cantidad,
            d.proyecto
        FROM salidas_detalle d
        LEFT JOIN insumos i ON i.id = d.insumo_id
        WHERE d.salida_id = '$id'";

$result = $con->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
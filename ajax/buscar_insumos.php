<?php
include "../conn/conexion.php";

$term = $_GET['term'];

$sql = "SELECT id, nombre FROM insumos 
        WHERE nombre LIKE '%$term%' 
        AND estado = 1 
        LIMIT 10";

$result = $con->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
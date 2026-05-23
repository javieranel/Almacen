<?php
include "../conn/conexion.php";

$term = $_GET['term'];

$sql = "SELECT id, nombre FROM herramientass 
        WHERE nombre LIKE '%$term%' 
        LIMIT 10";

$result = $con->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
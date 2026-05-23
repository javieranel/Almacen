<?php
include "../conn/conexion.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['id'])){
    echo json_encode(["success" => false, "error" => "ID no recibido"]);
    exit;
}

$id = $data['id'];

// 🔥 ACTUALIZA ESTADO
$sql = "UPDATE salidas_herramientas 
        SET status='Devuelto', fecha_procesado=NOW()
        WHERE id='$id'";

if(mysqli_query($con, $sql)){
    echo json_encode(["success" => true]);
}else{
    echo json_encode([
        "success" => false,
        "error" => mysqli_error($con)
    ]);
}
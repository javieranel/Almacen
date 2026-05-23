<?php
include "../conn/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['id'])){
    echo json_encode([
        "ok"=>false,
        "error"=>"ID no recibido"
    ]);
    exit;
}

$id = $data['id'];

$ok = $con->query("
    UPDATE salidas 
    SET estado='procesado', fecha_procesado=NOW()
    WHERE id=$id
");

if($ok){
    echo json_encode(["ok"=>true]);
}else{
    echo json_encode([
        "ok"=>false,
        "error"=>$con->error
    ]);
}
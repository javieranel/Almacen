<?php
include "../conn/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$accion = $_GET['accion'] ?? $data['accion'] ?? '';

// LISTAR
if($accion == "listar"){
    $res = $con->query("SELECT * FROM herramientass");
    $datos = [];

    while($row = $res->fetch_assoc()){
        $datos[] = $row;
    }

    echo json_encode($datos);
}

// CREAR
if($accion == "crear"){
    $_cantidad = $data['cantidad'];
    $nombre = $data['nombre'];
    $estado = $data['estado'];

    if($con->query("INSERT INTO herramientass (nombre,estado,cantidad) VALUES('$nombre','$estado','$_cantidad')")){
        echo json_encode(["ok"=>true]);
    } else {
        echo json_encode(["ok"=>false, "error"=>$con->error]);
    }
}

// EDITAR
if($accion == "editar"){
    $id = $data['id'];
    $_cantidad = $data['cantidad'];
    $nombre = $data['nombre'];
    $estado = $data['estado'];

    if($con->query("UPDATE herramientass SET nombre='$nombre', estado='$estado', cantidad='$_cantidad' WHERE id=$id")){
        echo json_encode(["ok"=>true]);
    } else {
        echo json_encode(["ok"=>false, "error"=>$con->error]);
    }
}

// ELIMINAR
if($accion == "eliminar"){
    $id = $data['id'];

    if($con->query("DELETE FROM herramientass WHERE id=$id")){
        echo json_encode(["ok"=>true]);
    } else {
        echo json_encode(["ok"=>false, "error"=>$con->error]);
    }
}
<?php
include "../conn/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$accion = $_GET['accion'] ?? $data['accion'] ?? '';

if($accion == "listar"){
    $res = $con->query("SELECT * FROM insumos WHERE estado = 1");
    $datos = [];

    while($row = $res->fetch_assoc()){
        $datos[] = $row;
    }

    echo json_encode($datos);
}

if($accion == "crear"){
    $nombre = $data['nombre'];
    $descripcion = $data['descripcion'];
    $con->query("INSERT INTO insumos(nombre,descripcion) VALUES('$nombre','$descripcion')");
    echo json_encode(["ok"=>true]);
}

if($accion == "editar"){
    $id = $data['id'];
    $nombre = $data['nombre'];
    $descripcion = $data['descripcion'];

    $con->query("UPDATE insumos SET nombre='$nombre', descripcion='$descripcion' WHERE id=$id");
    echo json_encode(["ok"=>true]);
}

if($accion == "eliminar"){
    $id = $data['id'];
    $con->query("UPDATE insumos SET estado=0 WHERE id=$id");
    echo json_encode(["ok"=>true]);
}
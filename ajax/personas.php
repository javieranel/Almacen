<?php
include "../conn/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$accion = $_GET['accion'] ?? $data['accion'] ?? '';

if($accion == "listar"){
    $res = $con->query("SELECT * FROM personas WHERE estado = 1");
    $datos = [];

    while($row = $res->fetch_assoc()){
        $datos[] = $row;
    }

    echo json_encode($datos);
}

// CREAR
if($accion == "crear"){
    $nombre = $data['nombre'];
    $tipo = $data['tipo'];

    $con->query("INSERT INTO personas(nombre,tipo) VALUES('$nombre','$tipo')");
    echo json_encode(["ok"=>true]);
}

// EDITAR
if($accion == "editar"){
    $id = $data['id'];
    $nombre = $data['nombre'];
    $tipo = $data['tipo'];

    $con->query("UPDATE personas SET nombre='$nombre', tipo='$tipo' WHERE id=$id");
    echo json_encode(["ok"=>true]);
}

// ELIMINAR (soft delete)
if($accion == "eliminar"){
    $id = $data['id'];
    $con->query("UPDATE personas SET estado=0 WHERE id=$id");
    echo json_encode(["ok"=>true]);
}
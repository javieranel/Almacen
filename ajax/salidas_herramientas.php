<?php
include "../conn/conexion.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["ok"=>false, "error"=>"No llegaron datos"]);
    exit;
}

$fecha = $data['fecha'] ?? null;
$retira = $data['retira'] ?? null;
$autoriza = $data['autoriza'] ?? null;
$firma1 = $data['firma1'] ?? null;
$firma2 = $data['firma2'] ?? null;
$detalle = $data['detalle'] ?? [];

if(!$fecha || !$retira || !$autoriza){
    echo json_encode(["ok"=>false, "error"=>"Datos incompletos"]);
    exit;
}

$con->begin_transaction();

try {

    $sql = "INSERT INTO salidas_herramientas 
    (fecha, persona_retira_id, persona_autoriza_id, firma_retira, firma_autoriza)
    VALUES ('$fecha','$retira','$autoriza','$firma1','$firma2')";

    if(!$con->query($sql)){
        throw new Exception($con->error);
    }

    $salida_id = $con->insert_id;

    foreach($detalle as $d){

        $insumo = $d['insumo'];
        $cantidad = $d['cantidad'];
        $proyecto = $d['proyecto'];

        $sqlDetalle = "INSERT INTO salidas_detalle_herramientas
        (salida_id, insumo_id, cantidad, proyecto)
        VALUES ('$salida_id','$insumo','$cantidad','$proyecto')";

        if(!$con->query($sqlDetalle)){
            throw new Exception($con->error);
        }
    }

    $con->commit();

    echo json_encode(["ok"=>true]);

} catch (Exception $e){

    $con->rollback();

    echo json_encode([
        "ok"=>false,
        "error"=>$e->getMessage()
    ]);
}
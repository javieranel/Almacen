<?php
include "../conn/conexion.php";

header('Content-Type: application/json');

// ===== KPI =====
$total_salidas = $con->query("SELECT COUNT(*) total FROM salidas")->fetch_assoc()['total'];
$total_insumos = $con->query("SELECT COUNT(*) total FROM insumos")->fetch_assoc()['total'];

// ===== SALIDAS POR DIA =====
$res = $con->query("
SELECT fecha, COUNT(*) total 
FROM salidas 
GROUP BY fecha 
ORDER BY fecha ASC
");

$salidas_dia = [];

while($row = $res->fetch_assoc()){
    $salidas_dia[] = $row;
}

// ===== TOP INSUMOS =====
$res2 = $con->query("
SELECT i.nombre, SUM(d.cantidad) total
FROM salidas_detalle d
INNER JOIN insumos i ON d.insumo_id = i.id
GROUP BY i.nombre
ORDER BY total DESC
LIMIT 5
");

$top_insumos = [];

while($row = $res2->fetch_assoc()){
    $top_insumos[] = $row;
}

// ===== ULTIMAS =====
$res3 = $con->query("
SELECT s.fecha, i.nombre
FROM salidas s
INNER JOIN salidas_detalle d ON s.id = d.salida_id
INNER JOIN insumos i ON d.insumo_id = i.id
ORDER BY s.id DESC
LIMIT 5
");

$ultimas = [];

while($row = $res3->fetch_assoc()){
    $ultimas[] = $row;
}

// ===== RESPUESTA =====
echo json_encode([
    "total_salidas"=>$total_salidas,
    "total_insumos"=>$total_insumos,
    "salidas_dia"=>$salidas_dia,
    "top_insumos"=>$top_insumos,
    "ultimas"=>$ultimas
]);
<?php
include "../conn/conexion.php";

header('Content-Type: application/json');

// Consulta corregida
$sql = "SELECT 
    s.id,
    s.fecha,
    COALESCE(s.status, 'Prestado') AS status,
    s.fecha_procesado AS fecha_devolucion,
    p1.nombre AS retira,
    p2.nombre AS autoriza,

    GROUP_CONCAT(h.nombre SEPARATOR ', ') AS herramientas,
    GROUP_CONCAT(d.cantidad SEPARATOR ', ') AS cantidades,
    GROUP_CONCAT(d.proyecto SEPARATOR ' | ') AS proyectos,

    s.firma_retira,
    s.firma_autoriza

FROM salidas_herramientas s

LEFT JOIN salidas_detalle_herramientas d 
    ON s.id = d.salida_id

LEFT JOIN herramientass h 
    ON d.insumo_id = h.id

LEFT JOIN personas p1 
    ON s.persona_retira_id = p1.id

LEFT JOIN personas p2 
    ON s.persona_autoriza_id = p2.id

GROUP BY 
    s.id,
    s.fecha,
    s.status,
    s.fecha_procesado,
    p1.nombre,
    p2.nombre,
    s.firma_retira,
    s.firma_autoriza

ORDER BY s.id DESC;";

$res = $con->query($sql);

if(!$res){
    echo json_encode([
        "success" => false,
        "error" => $con->error
    ]);
    exit;
}

$data = [];

while($row = $res->fetch_assoc()){
    $data[] = [
        "id" => $row['id'],
        "fecha" => $row['fecha'],
        "retira" => $row['retira'] ?? '',
        "autoriza" => $row['autoriza'] ?? '',
        "herramienta" => $row['herramientas'] ?? '',
        "cantidad" => $row['cantidades'] ?? '',
        "proyecto" => $row['proyectos'] ?? '',
        "firma_retira" => $row['firma_retira'] ?? '',
        "firma_autoriza" => $row['firma_autoriza'] ?? '',
        "status" => $row['status'],
        "fecha_devolucion" => $row['fecha_devolucion']
    ];
}

echo json_encode($data);
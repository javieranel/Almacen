<?php
include "../conn/conexion.php";

header('Content-Type: application/json');

$sql = "SELECT 
    s.id,
    s.fecha,
    pr.nombre AS retira,
    pa.nombre AS autoriza,
    s.firma_retira,
    s.firma_autoriza,
    s.estado
FROM salidas s
LEFT JOIN personas pr ON pr.id = s.persona_retira_id
LEFT JOIN personas pa ON pa.id = s.persona_autoriza_id
ORDER BY s.id DESC";

$result = $con->query($sql);

$data = [];

while($row = $result->fetch_assoc()){

    // Validar que no venga null
    $row['firma_retira'] = $row['firma_retira'] ?? '';
    $row['firma_autoriza'] = $row['firma_autoriza'] ?? '';

    $data[] = $row;
}

echo json_encode($data);
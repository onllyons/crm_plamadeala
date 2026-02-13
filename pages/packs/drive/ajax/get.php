<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
header('Content-Type: application/json');

// Selectăm toate fișierele din tabelul "drive"
$stmt = $conMain->prepare("
    SELECT id, titlu, file, DATE_FORMAT(data_adaugarii, '%d.%m.%Y %H:%i:%s') as data_adaugarii
    FROM drive
    ORDER BY data_adaugarii ASC
");

$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);

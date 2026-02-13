<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";

$client_id = $_GET['client_id'] ?? null;
$data = [];

function employeesDisplayOnly(?string $employees): string {
    if (!$employees) return '';
    return preg_replace('/\s*\(.*?\)\s*/', '', $employees);
}


if ($client_id) {
    $res = $conMain->query("SELECT * FROM projects WHERE client_id = " . intval($client_id) . " ORDER BY id DESC");
    while ($row = $res->fetch_assoc()) {
        $row['employees'] = employeesDisplayOnly($row['employees']);
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);

$conMain->close();
?>

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
header('Content-Type: application/json');

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
if ($project_id <= 0) { echo json_encode([]); exit; }

$sql = "SELECT id, fields_json, created_at FROM project_contracts WHERE project_id = ? ORDER BY id DESC";
$stmt = $conMain->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$res = $stmt->get_result();

$list = [];
while ($row = $res->fetch_assoc()) $list[] = $row;

echo json_encode($list);
$stmt->close();
$conMain->close();
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
header('Content-Type: application/json');

$project_id  = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
$fields_json = $_POST['fields_json'] ?? '';

if ($project_id <= 0 || !$fields_json) {
  echo json_encode(['success' => false, 'error' => 'Date lipsă']);
  exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$user_id = $_SESSION["crm_user"]["id"] ?? null;

$stmt = $conMain->prepare("INSERT INTO project_contracts (project_id, user_id, fields_json) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $project_id, $user_id, $fields_json);
$ok = $stmt->execute();

echo json_encode(['success' => $ok]);
$stmt->close();
$conMain->close();
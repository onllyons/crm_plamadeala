<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";

$project_id = $_GET['project_id'] ?? null;

if (!$project_id) {
  echo json_encode([]);
  exit;
}

$stmt = $conMain->prepare("
  SELECT n.note_text, n.created_at, n.user_id, n.files, u.username
  FROM project_notes n
  LEFT JOIN users_crm u ON u.id = n.user_id
  WHERE n.project_id = ?
  ORDER BY n.id DESC
");


$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

$notes = [];
while ($row = $result->fetch_assoc()) {
  $notes[] = $row;
}

$stmt->close();
$conMain->close();

header('Content-Type: application/json');
echo json_encode($notes);
?>

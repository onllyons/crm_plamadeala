<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";

$project_id = $_GET["project_id"] ?? null;
if (!$project_id) {
    http_response_code(400);
    echo json_encode([]);
    exit;
}

$sql = "
  SELECT n.id, n.project_id, n.user_id, n.note_text, n.created_at, u.username
  FROM project_simple_notes n
  LEFT JOIN users_crm u ON n.user_id = u.id
  WHERE n.project_id = ?
  ORDER BY n.created_at DESC
";

$stmt = $conMain->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$notes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($notes);
?>
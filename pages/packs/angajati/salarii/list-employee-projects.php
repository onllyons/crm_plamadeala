<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
header('Content-Type: application/json');

$employee_id = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : 0;
if ($employee_id <= 0) { echo json_encode([]); exit; }

$like = "%[" . $employee_id . "]%";

$stmt = $conMain->prepare("
  SELECT 
    p.id, 
    p.client_id, 
    p.title, 
    c.last_name_first_name AS client_name
  FROM projects p
  LEFT JOIN clienti c ON c.id = p.client_id
  WHERE p.employees LIKE BINARY ?
  ORDER BY p.created_at DESC
");
$stmt->bind_param("s", $like);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($data);
?>
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";

$id = $_GET['id'] ?? null;
$data = null;

if ($id) {
    $stmt = $conMain->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($data);
$conMain->close();
?>

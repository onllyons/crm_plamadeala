<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM clienti WHERE id = ?";
$stmt = $conMain->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'client' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Not found']);
}

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/db.php";

// Asigură output strict JSON
header('Content-Type: application/json');

// Permite doar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['status' => 'false', 'message' => 'Access denied']);
    exit;
}

// Verificăm dacă ID este valid
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['status' => 'false', 'message' => 'Invalid ID']);
    exit;
}

$user_id = (int) $_POST['id'];

// Ștergem rândul direct
$stmt = $conMain->prepare("DELETE FROM clienti WHERE id = ?");
$stmt->bind_param("i", $user_id);
$query = $stmt->execute();

if ($query) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'failed', 'message' => 'DB delete failed']);
}
?>

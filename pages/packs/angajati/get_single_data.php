<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/db.php";

// Permitem doar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['status' => 'false', 'message' => 'Access denied']);
    exit;
}

// Verificăm dacă ID-ul e setat și e numeric
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['status' => 'false', 'message' => 'Invalid ID']);
    exit;
}

$id = (int) $_POST['id']; // Asigurăm că e un int

// Pregătim query-ul pentru protecție SQL injection
$stmt = $conMain->prepare("SELECT * FROM angajati WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    echo json_encode($row);
} else {
    echo json_encode(['status' => 'false', 'message' => 'Not found']);
}
?>

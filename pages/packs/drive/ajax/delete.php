<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID invalid']);
    exit;
}

// 🔹 1. Găsim fișierul
$stmt = $conMain->prepare("SELECT file FROM drive WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Fișier inexistent']);
    exit;
}

// 🔹 2. Ștergem fișierul fizic (dacă există)
$filePath = $_SERVER['DOCUMENT_ROOT'] . "/crm/pages/packs/drive/file/" . $row['file'];
if (file_exists($filePath)) {
    unlink($filePath);
}

// 🔹 3. Ștergem din DB
$stmt = $conMain->prepare("DELETE FROM drive WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);

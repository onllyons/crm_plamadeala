<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
header('Content-Type: application/json');

// ✅ Verificare autentificare
if (!isset($_SESSION["crm_user"]["id"])) {
    echo json_encode(['success' => false, 'message' => 'Utilizator neautentificat']);
    exit;
}

$user_id = (int)$_SESSION["crm_user"]["id"];
$titlu = trim($_POST['titlu'] ?? '');

if (!$titlu || !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'Date lipsă']);
    exit;
}

$file = $_FILES['file'];
if ($file['error'] !== 0) {
    echo json_encode(['success' => false, 'message' => 'Eroare la upload']);
    exit;
}

// 🔐 Verificare extensie permisă (opțional, dar recomandat)
$allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'svg', 'xlsx', 'ppt', 'pptx', 'txt', 'heic'];
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'Unsupported file type']);
    exit;
}

// ✅ Generare nume unic și mutare fișier
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/crm/pages/packs/drive/file/";
$nameWithoutExt = pathinfo($file['name'], PATHINFO_FILENAME);
$cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nameWithoutExt);

$filename = $cleanName . '.' . $extension;
$counter = 1;
while (file_exists($uploadDir . $filename)) {
    $filename = $cleanName . '_' . $counter . '.' . $extension;
    $counter++;
}

$targetFile = $uploadDir . $filename;
if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
    echo json_encode(['success' => false, 'message' => 'Nu s-a putut salva fișierul']);
    exit;
}

// ✅ Salvare în DB
$stmt = $conMain->prepare("
    INSERT INTO drive (user_id, titlu, file, data_adaugarii)
    VALUES (?, ?, ?, NOW())
");
$stmt->bind_param("iss", $user_id, $titlu, $filename);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'file' => $filename, 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Eroare la salvarea în bază de date']);
}

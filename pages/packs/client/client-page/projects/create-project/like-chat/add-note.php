<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔹 doar verificăm că utilizatorul e logat
if (!isset($_SESSION["crm_user"]["id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["crm_user"]["id"];
$project_id = $_POST['project_id'] ?? null;
$note_text = trim($_POST['note_text'] ?? '');
$uploadedFiles = $_FILES['files'] ?? null;

if (!$project_id || ($note_text === '' && !$uploadedFiles)) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Date invalide"]);
  exit;
}

$uploadDir = __DIR__ . "/project_notes/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$filePaths = [];

if ($uploadedFiles && isset($uploadedFiles['name'])) {
  for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
    $name = basename($uploadedFiles['name'][$i]);
    $tmp = $uploadedFiles['tmp_name'][$i];
    $target = $uploadDir . time() . "_" . $name;

    if (move_uploaded_file($tmp, $target)) {
      $filePaths[] = time() . "_" . $name;
    }
  }
}

$filesJson = !empty($filePaths) ? json_encode($filePaths) : null;

$stmt = $conMain->prepare("
  INSERT INTO project_notes (project_id, user_id, note_text, files)
  VALUES (?, ?, ?, ?)
");
$stmt->bind_param("iiss", $project_id, $user_id, $note_text, $filesJson);
$stmt->execute();
$stmt->close();

echo json_encode(["success" => true, "files" => $filePaths]);

?>

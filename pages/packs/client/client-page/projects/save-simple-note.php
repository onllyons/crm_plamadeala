<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["crm_user"]["id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["crm_user"]["id"];
$project_id = $_POST["project_id"] ?? null;
$text = trim($_POST["text"] ?? '');

if (!$project_id || $text === '') {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

$stmt = $conMain->prepare("
    INSERT INTO project_simple_notes (project_id, user_id, note_text)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iis", $project_id, $user_id, $text);
$ok = $stmt->execute();
$stmt->close();

echo json_encode(["success" => $ok]);
?>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

if (!isset($_GET['employee_id']) || empty($_GET['employee_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing ID"]);
    exit;
}

$id = intval($_GET['employee_id']);

$lang = $_GET['lang'] ?? 'ro';

switch ($lang) {
    case 'ru': $table = 'contentBlogSettings_ru'; break;
    case 'en': $table = 'contentBlogSettings_en'; break;
    default:   $table = 'contentBlogSettings';
}

$sql = "SELECT * FROM $table WHERE id = ?";

$stmt = $conMain->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Record not found"]);
    exit;
}

$row = $result->fetch_assoc();

$stmt->close();
$conMain->close();

echo json_encode($row);
?>

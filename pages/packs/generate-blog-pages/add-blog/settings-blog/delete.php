<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

if (!isset($_GET['employee_id']) || empty($_GET['employee_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'The row ID is not set or is empty.'
    ]);
    exit;
}

$employeeId = filter_var($_GET['employee_id'], FILTER_SANITIZE_NUMBER_INT);

// detect language
$lang = $_GET['lang'] ?? 'ro';

switch ($lang) {
    case 'ru': $table = 'contentBlogSettings_ru'; break;
    case 'en': $table = 'contentBlogSettings_en'; break;
    default:   $table = 'contentBlogSettings';
}

$sql = "DELETE FROM $table WHERE id = ?";
$stmt = $conMain->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error preparing SQL: ' . $conMain->error
    ]);
    exit;
}

$stmt->bind_param("i", $employeeId);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Row has been deleted.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error executing SQL: ' . $stmt->error
    ]);
}

$stmt->close();
$conMain->close();
?>

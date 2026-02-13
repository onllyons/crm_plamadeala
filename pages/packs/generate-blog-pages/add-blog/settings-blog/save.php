<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

if (!isset($_POST['managementForm'], $_POST['managementTitle']) ||
    empty($_POST['managementForm']) || empty($_POST['managementTitle'])) {

    echo json_encode([
        'status' => 'error',
        'message' => 'The input data is not set or is empty.'
    ]);
    exit;
}

$managementForm = trim(strip_tags($_POST['managementForm']));
$managementTitle = trim(strip_tags($_POST['managementTitle']));

// language
$lang = $_POST['lang'] ?? 'ro';

switch ($lang) {
    case 'ru': $table = 'contentBlogSettings_ru'; break;
    case 'en': $table = 'contentBlogSettings_en'; break;
    default:   $table = 'contentBlogSettings';
}

// unique ID only for category
$managementUniqid = ($managementForm === 'category') ? uniqid() : '';

$sql = "INSERT INTO $table (managementForm, managementTitle, managementUniqid) VALUES (?, ?, ?)";
$stmt = $conMain->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error preparing SQL statement: ' . $conMain->error
    ]);
    exit;
}

$stmt->bind_param("sss", $managementForm, $managementTitle, $managementUniqid);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Row has been created.'
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

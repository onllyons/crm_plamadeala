<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

if (
    !isset($_POST['id'], $_POST['managementForm'], $_POST['managementTitle']) ||
    empty($_POST['id']) || empty($_POST['managementForm']) || empty($_POST['managementTitle'])
) {
    echo json_encode([
        'status' => 'error',
        'message' => 'The input data is not set or is empty.'
    ]);
    exit;
}

$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
$managementForm = trim(strip_tags($_POST['managementForm']));
$managementTitle = trim(strip_tags($_POST['managementTitle']));

// language
$lang = $_POST['lang'] ?? 'ro';

switch ($lang) {
    case 'ru': $table = 'contentBlogSettings_ru'; break;
    case 'en': $table = 'contentBlogSettings_en'; break;
    default:   $table = 'contentBlogSettings';
}

$sql = "UPDATE $table SET managementForm = ?, managementTitle = ? WHERE id = ?";
$stmt = $conMain->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error preparing SQL: ' . $conMain->error
    ]);
    exit;
}

$stmt->bind_param("ssi", $managementForm, $managementTitle, $id);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Row has been updated.'
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

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

$id  = filter_input(INPUT_GET, 'blogID', FILTER_VALIDATE_INT);
$lang = $_GET['lang'] ?? 'ro';

if ($id === false) {
    die(json_encode(['error' => 'ID invalid']));
}

switch ($lang) {
    case 'ru': $table = 'contentBlog_ru'; break;
    case 'en': $table = 'contentBlog_en'; break;
    default:   $table = 'contentBlog';
}

$stmt = mysqli_prepare(
    $conMain,
    "SELECT blogTitle, blogTopTag, blogAuthor, blogURL, blogCategory, blogImage, blogContent 
     FROM $table WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Blog not found']);
}

mysqli_stmt_close($stmt);
mysqli_close($conMain);
?>

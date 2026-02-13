<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

$lang = $_GET['lang'] ?? 'ro';

switch ($lang) {
    case 'ru': $table = 'contentBlog_ru'; break;
    case 'en': $table = 'contentBlog_en'; break;
    default:   $table = 'contentBlog';
}

$query = "SELECT id, blogTitle, blogImage FROM $table ORDER BY id DESC";
$stmt = mysqli_prepare($conMain, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $data = ['error' => 'No blog posts found.'];
}

header('Content-Type: application/json');
echo json_encode($data);

mysqli_free_result($result);
mysqli_stmt_close($stmt);
mysqli_close($conMain);
?>

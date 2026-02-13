<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing ID']);
        exit;
    }

    $id = intval($_POST['id']);
    $lang = $_POST['lang'] ?? 'ro';

    switch ($lang) {
        case 'ru': $table = 'contentBlog_ru'; break;
        case 'en': $table = 'contentBlog_en'; break;
        default:   $table = 'contentBlog';
    }

    // get image
    $query = "SELECT blogImage FROM $table WHERE id = ?";
    $stmt = mysqli_prepare($conMain, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // delete row
    $query = "DELETE FROM $table WHERE id = ?";
    $stmt = mysqli_prepare($conMain, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        // Delete image
        $filename = "/web-site/packs/view-blog/images/" . $row['blogImage'];
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $filename)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $filename);
        }

        echo json_encode(['success' => true, 'message' => 'Blog deleted.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting blog.']);
    }
}

mysqli_close($conMain);
?>

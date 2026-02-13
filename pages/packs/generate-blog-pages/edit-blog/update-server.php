<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id  = filter_input(INPUT_POST, 'blogID', FILTER_VALIDATE_INT);
    $lang = $_POST['lang'] ?? 'ro';

    if ($id === false) {
        die('Invalid ID.');
    }

    switch ($lang) {
        case 'ru': $table = 'contentBlog_ru'; break;
        case 'en': $table = 'contentBlog_en'; break;
        default:   $table = 'contentBlog';
    }

    $blogTitle    = mysqli_real_escape_string($conMain, $_POST['blogTitle']);
    $blogTopTag   = mysqli_real_escape_string($conMain, $_POST['blogTopTag']);
    $blogAuthor   = mysqli_real_escape_string($conMain, $_POST['blogAuthor']);
    $blogURL      = mysqli_real_escape_string($conMain, $_POST['blogURL']);
    $blogCategory = mysqli_real_escape_string($conMain, $_POST['blogCategory']);
    $blogContent  = htmlspecialchars($_POST['blogContent'], ENT_QUOTES);

    // If image uploaded
    if (!empty($_FILES['blogImage']['name'])) {

        $newImage = $_FILES['blogImage']['name'];
        $tmpFile  = $_FILES['blogImage']['tmp_name'];

        $targetDir = "/web-site/packs/view-blog/images/";
        $targetFile = $_SERVER['DOCUMENT_ROOT'] . $targetDir . $newImage;

        // delete old image
        $old = mysqli_fetch_assoc(mysqli_query($conMain, "SELECT blogImage FROM $table WHERE id = $id"));
        if ($old && file_exists($_SERVER['DOCUMENT_ROOT'] . $targetDir . $old['blogImage'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $targetDir . $old['blogImage']);
        }

        move_uploaded_file($tmpFile, $targetFile);

        $stmt = mysqli_prepare(
            $conMain,
            "UPDATE $table SET blogTitle=?, blogTopTag=?, blogAuthor=?, blogURL=?, blogCategory=?, blogImage=?, blogContent=? WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, "sssssssi",
            $blogTitle, $blogTopTag, $blogAuthor, $blogURL, $blogCategory, $newImage, $blogContent, $id
        );
    } else {
        // no new image
        $stmt = mysqli_prepare(
            $conMain,
            "UPDATE $table SET blogTitle=?, blogTopTag=?, blogAuthor=?, blogURL=?, blogCategory=?, blogContent=? WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, "ssssssi",
            $blogTitle, $blogTopTag, $blogAuthor, $blogURL, $blogCategory, $blogContent, $id
        );
    }

    if (mysqli_stmt_execute($stmt)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conMain);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conMain);
}
?>

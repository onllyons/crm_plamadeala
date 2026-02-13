<?php

// Check that the input data is set and not empty
if (
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['blogTitle'], $_POST['blogUrl'], $_POST['blogContent']) &&
    !empty($_POST['blogTitle']) &&
    !empty($_POST['blogUrl']) &&
    !empty($_POST['blogContent'])
) {
    
    $lang = $_POST['blogLang'] ?? 'ro';

    switch ($lang) {
        case 'ru':
            $table = 'contentBlog_ru';
            break;
        case 'en':
            $table = 'contentBlog_en';
            break;
        default:
            $table = 'contentBlog';
    }


    define('TABLE_NAME', $table);
    define('COLUMN_BLOG_TITLE', 'blogTitle');
    define('COLUMN_BLOG_TAG', 'blogTopTag');
    define('COLUMN_BLOG_AUT', 'blogAuthor');
    define('COLUMN_BLOG_URL', 'blogURL');
    define('COLUMN_BLOG_CATEGORY', 'blogCategory');
    define('COLUMN_BLOG_IMAGE', 'blogImage');
    define('COLUMN_BLOG_CONTENT', 'blogContent');
    define('COLUMN_BLOG_DATE', 'blogDate');
    define('SALT', 'q3wji12klmfl83nl92s');

    require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

    // Filter the input data
    $blogTitle    = mysqli_real_escape_string($conMain, trim(strip_tags($_POST['blogTitle'])));
    $blogTopTag   = mysqli_real_escape_string($conMain, trim(strip_tags($_POST['blogTopTag'])));
    $blogAuthor   = mysqli_real_escape_string($conMain, trim(strip_tags($_POST['blogAuthor'])));
    $blogUrl      = mysqli_real_escape_string($conMain, filter_var($_POST['blogUrl'], FILTER_SANITIZE_URL));
    $blogCategory = mysqli_real_escape_string($conMain, trim(strip_tags($_POST['blogCategory'])));
    $blogContent  = htmlspecialchars($_POST['blogContent'], ENT_QUOTES, 'UTF-8');

    $blogDate = date('Y-m-d H:i:s');

    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    // $targetDir = '../images-blog/';
    $targetDir = '../../../../../web-site/packs/view-blog/images/';
    $targetFile = $targetDir . basename($_FILES['blogImage']['name']);
    $maxFileSize = 5 * 1024 * 1024;

    if (!isset($_FILES['blogImage']) || $_FILES['blogImage']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'Upload error: ' . ($_FILES['blogImage']['error'] ?? 'no file')
        ]);
        exit;
    }


    // Salvare fisier
    if (!move_uploaded_file($_FILES['blogImage']['tmp_name'], $targetFile)) {
        $data = [
            'success' => false,
            'message' => 'Error: The file could not be saved.',
        ];
        echo json_encode($data);
        exit();
    }

    $blogImage = basename($targetFile);



    // Check if blogURL already exists
    $query = "SELECT COUNT(*) FROM contentBlog WHERE blogURL = ?";
    $stmt = mysqli_prepare($conMain, $query);
    mysqli_stmt_bind_param($stmt, 's', $blogUrl);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // If the blogURL already exists, return an error
    if ($count > 0) {
        $data = [
            'success' => false,
            'message' => 'Error: The URL already exists.',
        ];
        echo json_encode($data);
        exit();
    }



    $stmt = mysqli_prepare(
        $conMain,
        "INSERT INTO " .
            TABLE_NAME .
            " (" .
            COLUMN_BLOG_TITLE .
            ", " .
            COLUMN_BLOG_TAG .
            ", " .
            COLUMN_BLOG_AUT .
            ", " .
            COLUMN_BLOG_URL .
            ", " .
            COLUMN_BLOG_CATEGORY .
            ", " .
            COLUMN_BLOG_IMAGE .
            ", " .
            COLUMN_BLOG_CONTENT .
            ", " .
            COLUMN_BLOG_DATE .
            ") VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        $data = [
            'success' => false,
            'message' => 'There was an error preparing the statement: ' . mysqli_error($conMain),
        ];
        echo json_encode($data);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssssssss", $blogTitle, $blogTopTag, $blogAuthor, $blogUrl, $blogCategory, $blogImage, $blogContent, $blogDate);

    if (!mysqli_stmt_execute($stmt)) {
        $data = [
            'success' => false,
            'message' => 'There was an error executing the statement: ' . mysqli_error($conMain),
        ];
        echo json_encode($data);
        exit();
    }

    $data = [
        'success' => true,
        'message' => 'Blog successfully created!',
    ];

    mysqli_stmt_close($stmt);
    mysqli_close($conMain);

    // Returnarea raspunsului
    echo json_encode($data);
}

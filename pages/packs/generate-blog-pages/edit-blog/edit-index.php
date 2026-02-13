<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
checkAuth();

if (!isset($_SESSION['crm_user']) || (int)$_SESSION['crm_user']['level'] !== 0) {
    header("Location: /crm/pages/index.php");
    exit;
}
?>

<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Blog</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.rtl.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
        <!-- multiselect -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
        <!-- style design form blog -->
        <link rel="stylesheet" type="text/css" href="../add-blog/style.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.rtl.min.css" />
    </head>
<body>

<div class="flex-content-center">

    <form id="create-blog-content" enctype="multipart/form-data" autocomplete="off">
        <div class="centered-content">
            <h1 class="mb-3">Editați proiectul <a style="font-size: 15px;" href="../add-blog/index-blog.php">(Adăugați proiecte)</a>
            </h1>
            <select id="blog-lang" class="form-select w-auto mb-3" style="display: none;">
                <option value="ro">Română</option>
                <option value="ru">Русский</option>
                <option value="en">English</option>
            </select>


            <input required type="text" class="form-control v--vr--1" placeholder="Blog title" id="blog-title">
            <input required type="text" class="form-control v--vr--1" placeholder="Blog top tag" id="blog-blogTopTag">
            <input required type="text" class="form-control v--vr--1" placeholder="Blog Author" id="blog-blogAuthor">
            <input required type="text" class="form-control v--vr--1" placeholder="Blog url" id="blog-url">
            
<select class="form-select" id="blog-category" data-placeholder="Choose anything">
    <?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

    // luam id + limba
    $id = intval($_GET['id']);
    $lang = $_GET['lang'] ?? 'ro';
    $lang = in_array($lang, ['ro','ru','en']) ? $lang : 'ro';


    // alegem tabela corecta pentru categorii
    switch ($lang) {
        case "ru":  $tableSettings = "contentBlogSettings_ru"; break;
        case "en":  $tableSettings = "contentBlogSettings_en"; break;
        default:    $tableSettings = "contentBlogSettings";
    }

    // luam blogul curent (pentru selected)
    switch ($lang) {
        case "ru": $tableBlog = "contentBlog_ru"; break;
        case "en": $tableBlog = "contentBlog_en"; break;
        default:   $tableBlog = "contentBlog";
    }

    $sqlBlog = "SELECT blogCategory FROM `$tableBlog` WHERE id = $id LIMIT 1";
    $currentCategory = mysqli_fetch_assoc(mysqli_query($conMain, $sqlBlog))['blogCategory'] ?? "";

    // luam categoriile din limba corecta
    $sql = "SELECT managementTitle, managementUniqid 
            FROM `$tableSettings`
            WHERE managementForm = 'category'";
    $result = mysqli_query($conMain, $sql);

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {

            $selected = ($row["managementUniqid"] === $currentCategory) ? "selected" : "";

            echo '<option value="' . $row["managementUniqid"] . '" ' . $selected . '>'
               . $row["managementTitle"] .
               '</option>';
        }
    } else {
        echo "<option>Nu s-au găsit categorii.</option>";
    }
    ?>
</select>

            <label class="drop-container">
                <span class="drop-title">Drop files here</span>or
                <input required type="file" accept="image/*" title="Blog image" class="drop--image" id="blog-image">
            </label>
            
        </div>
        <textarea required id="blog-content" name="blog-content"></textarea>

        <button type="submit" id="idbtn" class="btn-create">Edit blog</button>
    </form>


</div>

<div id="upload-loader" class="upload-loader">
    <div class="loader-box">
        <div class="spinner"></div>
        <div class="progress-text">0%</div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<!-- multiselect2 script js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!-- toastr alert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript" src="edit-blog-ajax.js"></script>
</body>
</html>


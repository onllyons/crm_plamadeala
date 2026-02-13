<?php
  // require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
  // checkAuth()
?>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
    checkAuth();

    if (!isset($_SESSION['crm_user']) || (int)$_SESSION['crm_user']['level'] !== 0) {
        header("Location: /crm/pages/index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
    <head>
        <?php include '../../../../assets/components/links.php' ?>
        <link rel="stylesheet" type="text/css" href="/adm/vendors/glightbox/glightbox.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet" />
    </head>
    <body>
      <main class="main" id="top">
    <div class="container" data-layout="container">
        <?php include '../../../../assets/components/nav-left.php' ?>

        <div class="content">
            <?php include '../../../../assets/components/nav-top.php' ?>
            <h1 class="h1-title">Vezi toate proiectele <a style="font-size: 15px;" href="/crm/pages/packs/generate-blog-pages/add-blog/index-blog.php">(Adăugați proiecte)</a></h1>

            <select id="blog-lang" class="form-select mb-3" style="width:200px;">
                <option value="ro" selected>Română</option>
                <option value="ru">Русский</option>
                <option value="en">English</option>
            </select>


            <div id="viewBlogCards" class="row g-4"></div>
            <?php include '../../../../assets/components/footer.php' ?>
        </div>
    </div>
</main>

<?php include '../../../../assets/components/off-canvas-design.php' ?>
<?php include '../../../../assets/components/scripts.php' ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript" src="view-blog-ajax.js"></script>

    </body>
</html>

<?php
// Definirea unei constante pentru salt
define('SALT', 'q3wji12klmfl83nl92s');

// Conectarea la baza de date
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

// Obținerea valorii parametrului "category" din cererea AJAX și validarea sa
$category = filter_var($_GET["category"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

// Selectarea tuturor rândurilor din tabelul "contentBlogSettings" unde coloana "managementForm" este egală cu "category" și coloana "managementUniqid" este egală cu valoarea parametrului "category"
$sql = "SELECT * FROM contentBlogSettings WHERE managementForm = 'category' AND managementUniqid = ?";
$stmt = mysqli_prepare($conMain, $sql);
mysqli_stmt_bind_param($stmt, "s", $category);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Verificarea dacă interogarea a avut succes și extragerea datelor din rânduri
if (mysqli_num_rows($result) > 0) {
    // Extragerea valorii coloanei "managementTitle" din primul rând
    $title = mysqli_fetch_assoc($result)["managementTitle"];

    // Selectarea tuturor rândurilor din tabelul "contentBlog" unde coloana "blogCategory" este egală cu valoarea parametrului "category"
    $sql = "SELECT blogTitle, blogURL, blogImage, blogDate FROM contentBlog WHERE blogCategory = ? ORDER BY blogDate DESC LIMIT 100";
    $stmt = mysqli_prepare($conMain, $sql);
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Verificarea dacă interogarea a avut succes și extragerea datelor din rânduri
    if (mysqli_num_rows($result) > 0) {
        // Începerea construirii conținutului sortat
        $content = "<h3 class='h3'>" . mb_substr($title, 0, 50) . "</h3>";

        while ($row = mysqli_fetch_assoc($result)) {
            // Adăugarea rândului curent la conținutul sortat
            $content .= "<a class='col-sm-2' href='/snippets/generate-blog-pages/view-blog/store/" . mb_substr($row["blogURL"], 0, 50) . "'>
                            <h2 class='blog-title'>" . mb_substr($row["blogTitle"], 0, 50) . "</h2>
                            <img src='/snippets/generate-blog-pages/images-blog/" . mb_substr($row["blogImage"], 0, 255) . "'>
                            <p class='blog-title'>" . mb_substr($row["blogDate"], 0, 50) . "</p>
                        </a>";
        }

        // Returnarea HTML-ului conținutului sortat
        if (!empty($content)) {
            echo json_encode(array('data' => $content));
        } else {
            $message = "No rows found.";
            echo json_encode(array('message' => $message));
        }
    } else {
        $message = "No rows found.";
        echo json_encode(array('message' => $message));
    }
} else {
    $message = "No rows found.";
    echo json_encode(array('message' => $message));
}

// Eliberarea rezultatului interogării
mysqli_free_result($result);

// Închiderea conexiunii la baza de date
mysqli_close($conMain);
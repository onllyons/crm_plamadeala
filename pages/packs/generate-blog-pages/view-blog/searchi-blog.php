<?php
// Conectarea la baza de date
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

// Obținerea valorii căutate din URL și validarea sa
$searchTerm = filter_input(INPUT_GET, 'searchi', FILTER_SANITIZE_STRING);

// Construirea interogării SQL folosind o declarație pregătită și executarea sa
$stmt = mysqli_prepare($conMain, "SELECT blogTitle, blogURL, blogImage, blogDate FROM contentBlog WHERE blogTitle LIKE ? LIMIT 100");
$searchTerm = "%$searchTerm%";
mysqli_stmt_bind_param($stmt, "s", $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Verificarea dacă s-au găsit rânduri în baza de date și construirea conținutului sortat
if (mysqli_num_rows($result) > 0) {
  $content = '';

  while ($row = mysqli_fetch_assoc($result)) {
    $content .= "<a class='col-sm-2' href='/snippets/generate-blog-pages/view-blog/store/" . mb_substr($row["blogURL"], 0, 50) . "'>
                    <h2 class='blog-title'>" . mb_substr($row["blogTitle"], 0, 50) . "</h2>
                    <img src='/snippets/generate-blog-pages/images-blog/" . mb_substr($row["blogImage"], 0, 255) . "'>
                    <p class='blog-title'>" . mb_substr($row["blogDate"], 0, 50) . "</p>
                </a>";
  }

  // Construirea și afișarea răspunsului JSON
  $response = array('content' => $content);
  echo json_encode($response);
} else {
  $message = "No rows found.";
  $response = array('message' => $message);
  echo json_encode($response);
}
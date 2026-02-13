<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

// preiați valoarea parametrului url din URL
$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);

// efectuați o interogare parametrizată pentru a prelua datele din baza de date
$stmt = mysqli_prepare($conMain, "SELECT cb.blogTitle, cb.blogURL, cbs.managementTitle, cb.blogImage, cb.blogHashtag, cb.blogContent FROM contentBlog cb JOIN contentBlogSettings cbs ON cb.blogCategory = cbs.managementUniqid WHERE cb.blogURL = ?");
mysqli_stmt_bind_param($stmt, "s", $url);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// verificați dacă interogarea a avut succes și returnați datele în format JSON
if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_array($result, MYSQLI_BOTH);
  $data = [
    'blogTitle' => $row['blogTitle'],
    'blogURL' => $row['blogURL'],
    'blogCategory' => $row['managementTitle'],
    'blogImage' => $row['blogImage'],
    'blogHashtag' => $row['blogHashtag'],
    'blogContent' => $row['blogContent']
  ];

  // setați antetul Content-Type la application/json
  header('Content-Type: application/json');

  echo json_encode($data);
} else {
  echo 'No row ' . $id;
}

// închideți conexiunea la baza de date
mysqli_close($conMain);

?>

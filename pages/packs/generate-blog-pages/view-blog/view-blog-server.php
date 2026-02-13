<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

// Obține datele din tabel folosind o declarație pregătită
$query = "SELECT * FROM contentBlog ORDER BY blogDate DESC"; 
$stmt = mysqli_prepare($conMain, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Verifică dacă interogarea a avut succes și dacă există rânduri
if (mysqli_num_rows($result) > 0) {
    // Stochează datele într-un array asociativ
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    // Dacă nu există rânduri în tabel, afișează un mesaj de eroare
    $data = array('error' => 'No blog posts found.');
}

// Transformă datele în format JSON și trimite-le către client
header('Content-Type: application/json');
echo json_encode($data);

// Eliberează rezultatul interogării și închide conexiunea la baza de date
mysqli_free_result($result);
mysqli_stmt_close($stmt);
mysqli_close($conMain);
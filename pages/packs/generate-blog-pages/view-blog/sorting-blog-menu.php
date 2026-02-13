<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

// Selectați toate categoriile distincte din tabelul "contentBlogSettings" unde coloana "managementForm" este "category"
$sql = "SELECT DISTINCT managementTitle, managementUniqid FROM contentBlogSettings WHERE managementForm='category' LIMIT 100";
$result = mysqli_query($conMain, $sql);

// Verificați dacă interogarea a avut succes și extrageți datele din rânduri
if ($result) {
    $buttons = '';

    // Începeți construirea butoanelor
    while ($row = mysqli_fetch_assoc($result)) {
        // Protejați datele împotriva injectării de cod
        $category = htmlspecialchars($row['managementUniqid'], ENT_QUOTES);
        $title = htmlspecialchars($row['managementTitle'], ENT_QUOTES);

        // Creați un buton pentru categoria curentă
        $buttons .= '<button class="sort-button btn btn-primary me-2" data-category="' . $category . '">' . $title . '</button>';
    }

    // Returnați HTML-ul butoanelor
    echo $buttons;
} else {
    // În cazul în care interogarea nu a avut succes, afișați un mesaj de eroare
    echo 'No rows found.';
}

// Închideți conexiunea la baza de date
mysqli_close($conMain);
?>

<?php
	// include config file
    require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

    $lang = $_GET['lang'] ?? 'ro';
	switch ($lang) {
	    case 'ru': $table = 'contentBlogSettings_ru'; break;
	    case 'en': $table = 'contentBlogSettings_en'; break;
	    default:   $table = 'contentBlogSettings';
	}

	$sql = "SELECT * FROM $table";

	// Process the query
	$results = $conMain->query($sql);

	// Fetch Associative array
	$row = array();
	while ($data = $results->fetch_assoc()) {
	    $row[] = $data;
	}

	// Free result set
	$results->free_result();

	// Close the connection after using it
	$conMain->close();

	// Encode array into json format
	echo json_encode($row);
?>
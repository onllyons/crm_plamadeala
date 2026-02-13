<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT id, client_id, title, date_received, date_deadline 
        FROM projects 
        WHERE date_received IS NOT NULL 
        ORDER BY date_received ASC";

$result = mysqli_query($conMain, $sql);

$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Eveniment verde – începutul proiectului
    if (!empty($row['date_received'])) {
        $events[] = [
            'title' => $row['title'] . ' – start',
            'start' => $row['date_received'],
            'url' => '/crm/pages/client-page.php?slug=' . urlencode($row['client_id']),
            'color' => '#04A6A5', // verde-teal
            'textColor' => '#fff'
        ];
    }

    // Eveniment roșu – deadline-ul proiectului
    if (!empty($row['date_deadline'])) {
        $events[] = [
            'title' => $row['title'] . ' – deadline',
            'start' => $row['date_deadline'],
            'url' => '/crm/pages/client-page.php?slug=' . urlencode($row['client_id']),
            'color' => '#dc3545', // roșu Bootstrap danger
            'textColor' => '#fff'
        ];
    }
}

echo json_encode($events);
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";

$title = $_POST['title'] ?? '';
$stage = $_POST['stage'] ?? '';
$surface = $_POST['surface'] ?? '';
$price_per_m2 = $_POST['price_per_m2'] ?? '';
$total_price = $_POST['total_price'] ?? '';
$currency = $_POST['currency'] ?? '';
$advance = $_POST['advance'] ?? '';
$remainder = $_POST['remainder'] ?? '';
$date_received = $_POST['date_received'] ?? '';
$date_technical = $_POST['date_technical'] ?? '';
$date_3d = $_POST['date_3d'] ?? '';
$date_deadline = $_POST['date_deadline'] ?? '';
$employees = $_POST['employees'] ?? '';
$client_id = $_POST['client_id'] ?? null;

if (is_array($employees)) {
    $employees = implode(', ', $employees);
}

// inserăm proiectul
$stmt = $conMain->prepare("INSERT INTO projects 
(client_id, title, stage, surface, price_per_m2, total_price, currency, advance, remainder, date_received, date_technical, date_3d, date_deadline, employees)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssssssssss", $client_id, $title, $stage, $surface, $price_per_m2, $total_price, $currency, $advance, $remainder, $date_received, $date_technical, $date_3d, $date_deadline, $employees);
$stmt->execute();
$stmt->close();

// selectăm toate proiectele clientului
$data = [];
if ($client_id) {
    $res = $conMain->query("SELECT * FROM projects WHERE client_id = " . intval($client_id) . " ORDER BY id DESC");
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
}
$conMain->close();

// returnăm JSON
header('Content-Type: application/json');
echo json_encode($data);
?>

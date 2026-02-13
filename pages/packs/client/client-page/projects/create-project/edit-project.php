<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$stage = $_POST['stage'] ?? '';
$surface = $_POST['surface'] ?? '';
$price_per_m2 = $_POST['price_per_m2'] ?? '';
$currency = $_POST['currency'] ?? '';
$advance = $_POST['advance'] ?? '';
$remainder = $_POST['remainder'] ?? '';
$date_received = $_POST['date_received'] ?? '';
$date_technical = $_POST['date_technical'] ?? '';
$date_3d = $_POST['date_3d'] ?? '';
$date_deadline = $_POST['date_deadline'] ?? '';
$employees = $_POST['employees'] ?? '';
$total_price = $_POST['total_price'] ?? '';

// 🔹 conversie array → text ("Sergiu, Victor, Ion")
if (is_array($employees)) {
    $employees = implode(', ', $employees);
}

if ($id) {
    $stmt = $conMain->prepare("UPDATE projects SET 
        title=?, stage=?, surface=?, price_per_m2=?, total_price=?, currency=?, advance=?, remainder=?, 
        date_received=?, date_technical=?, date_3d=?, date_deadline=?, employees=? 
        WHERE id=?");
    $stmt->bind_param(
        "sssssssssssssi",
        $title,
        $stage,
        $surface,
        $price_per_m2,
        $total_price,
        $currency,
        $advance,
        $remainder,
        $date_received,
        $date_technical,
        $date_3d,
        $date_deadline,
        $employees,
        $id
    );
    $stmt->execute();
    $stmt->close();
}

$conMain->close();
echo 'success';
?>

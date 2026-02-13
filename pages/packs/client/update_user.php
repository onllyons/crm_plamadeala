
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['status' => 'false', 'message' => 'Access denied']);
    exit;
}

// Funcție de curățare
function sanitize_input($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

$id = (int) $_POST['id'];
$last_name_first_name          = sanitize_input($_POST['last_name_first_name']);
$adresa_client                  = sanitize_input($_POST['adresa_client']);
$phone_number                   = sanitize_input($_POST['phone_number']);
$user_email_field      = sanitize_input($_POST['user_email_field']);
$nr_identitate      = sanitize_input($_POST['nr_identitate']);

$dateAdded              = sanitize_input($_POST['dateAdded']);


$sql = "UPDATE `clienti` SET 
               `last_name_first_name`=?,
               `adresa_client`=?,
               `phone_number`=?,
               `user_email_field`=?,
               `nr_identitate`=?,
               `dateAdded`=?
        WHERE id=?";

$stmt = mysqli_prepare($conMain, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ssssssi",
    $last_name_first_name,
    $adresa_client,
    $phone_number,
    $user_email_field,
    $nr_identitate,
    $dateAdded,
    $id
);



$query = mysqli_stmt_execute($stmt);
if ($query == true) {
    $data = [
        'status' => 'true',
    ];
    echo json_encode($data);
} else {
    $data = [
        'status' => 'false',
    ];
    echo json_encode($data);
}

?>

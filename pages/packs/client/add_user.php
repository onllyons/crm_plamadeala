<?php require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(403);
  echo json_encode(["status" => "false", "message" => "Access denied"]);
  exit();
}
function sanitize_input($value)
{
  if ($value === null) {
    return "";
  }
  return trim($value);
}
$last_name_first_name = sanitize_input($_POST["last_name_first_name"]);
$adresa_client = sanitize_input($_POST["adresa_client"]);
$phone_number = sanitize_input($_POST["phone_number"]);
$user_email_field = sanitize_input($_POST["user_email_field"]);
$nr_identitate = sanitize_input($_POST["nr_identitate"]);
$dateAdded = sanitize_input($_POST["dateAdded"]);
$stmt = $conMain->prepare(
  "INSERT INTO clienti ( last_name_first_name, adresa_client, phone_number, user_email_field, nr_identitate, dateAdded ) VALUES ( ?, ?, ?, ?, ?, ? ) ",
);
$stmt->bind_param(
  "ssssss",
  $last_name_first_name,
  $adresa_client,
  $phone_number,
  $user_email_field,
  $nr_identitate,
  $dateAdded,
);
$stmt->execute();
if ($stmt->affected_rows == 1) {
  $data = ["status" => "true"];
  echo json_encode($data);
} else {
  $data = ["status" => "false"];
  echo json_encode($data);
} ?>

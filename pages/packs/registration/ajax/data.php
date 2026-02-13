<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

$query = mysqli_query($conMain, "SELECT id, username, email, phone, name, level FROM users_crm ORDER BY id DESC");

$data = [];

while ($row = mysqli_fetch_assoc($query)) {
    $row["level"] = $row["level"] == 0 ? "Administrator" : "Proiectant";
    $data[] = $row;
}

echo json_encode(["data" => $data]);

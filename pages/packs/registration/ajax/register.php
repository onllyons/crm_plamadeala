<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

if (!isAuth() || $_SESSION["crm_user"]["level"] !== 0) {
    return_answer(["success" => false, "message" => "Access denied."]);
}

$name = $_POST["name"] ?? "";
$phone = $_POST["phone"] ?? "";
$email = $_POST["email"] ?? "";
$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";
$passwordConfirm = $_POST["password-confirm"] ?? "";
$level = (int) ($_POST["level"] ?? 1);

if (!$name || !$username || !$password || !$passwordConfirm) {
    return_answer(["success" => false, "message" => "Please fill in all fields."]);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return_answer(["success" => false, "message" => "Invalid email address."]);
}
if ($password !== $passwordConfirm) {
    return_answer(["success" => false, "message" => "Passwords do not match."]);
}

$stmt = mysqli_prepare($conMain, "SELECT id FROM users_crm WHERE username = ? OR email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $username, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    return_answer(["success" => false, "message" => "User already exists."]);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conMain, "INSERT INTO users_crm (username, email, phone, name, level, password) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $phone, $name, $level, $passwordHash);
$success = mysqli_stmt_execute($stmt);

if ($success) {
    return_answer(["success" => true, "message" => "Account successfully created!"]);
} else {
    return_answer(["success" => false, "message" => "Error saving data."]);
}
?>

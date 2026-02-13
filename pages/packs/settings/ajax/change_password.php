<?php

// require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

if (!isAuth()) return_answer(["success" => false, "message" => "You are not logged in"]);

$oldPassword = $_POST["old-password"] ?? "";
$password = $_POST["password"] ?? "";
$passwordConfirm = $_POST["password-confirm"] ?? "";

if (!$oldPassword || !$password || !$passwordConfirm) return_answer(["success" => false, "message" => "Fill in all fields"]);
if ($password !== $passwordConfirm) return_answer(["success" => false, "message" => "Passwords don't match"]);

$password = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conMain, "SELECT `password` FROM `users_crm` WHERE `id` = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $_SESSION["crm_user"]["id"]);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) return_answer(["success" => false, "action" => "reload"]);
if (!password_verify($oldPassword, $user["password"])) return_answer(["success" => false, "message" => "The old password is wrong"]);

$stmt = mysqli_prepare($conMain, "UPDATE `users_crm` SET `password` = ? WHERE `id` = ?");
mysqli_stmt_bind_param($stmt, "si", $password, $_SESSION["crm_user"]["id"]);
mysqli_stmt_execute($stmt);

return_answer(["success" => true, "message" => "Password changed"]);
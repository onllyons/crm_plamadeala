<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/db.php";

$responseLog = [];  // colectăm pașii de debug

$responseLog[] = "start";

$login = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";
$rememberMe = (bool)($_POST["remember-me"] ?? false);

$responseLog[] = "inputs read";
$responseLog[] = ["email" => $login, "password_length" => strlen($password)];

if (isAuth()) {
    $responseLog[] = "You’re already logged in.";
    return_answer([
        "success" => false,
        "action" => "reload",
        "log" => $responseLog
    ]);
}

if (!$login || !$password) {
    $responseLog[] = "Please fill in both email and password.";
    return_answer([
        "success" => false,
        "message" => "Please complete all required fields",
        "log" => $responseLog
    ]);
}

// verificăm conexiunea
if (!$conMain) {
    $responseLog[] = "Could not connect to the database. Please try again later.";
    return_answer([
        "success" => false,
        "message" => "Unable to connect to the database. Please try again later.",
        "log" => $responseLog
    ]);
}
$responseLog[] = "DB connected";

$stmt = mysqli_prepare($conMain, "SELECT * FROM `users_crm` WHERE `username` = ? OR `email` = ? LIMIT 1");
if (!$stmt) {
    $responseLog[] = "query preparation failed: " . mysqli_error($conMain);
    return_answer([
        "success" => false,
        "message" => "There was an error preparing the database query",
        "log" => $responseLog
    ]);
}

$responseLog[] = "query prepared";

mysqli_stmt_bind_param($stmt, "ss", $login, $login);
if (!mysqli_stmt_execute($stmt)) {
    $responseLog[] = "query execution failed: " . mysqli_error($conMain);
    return_answer([
        "success" => false,
        "message" => "There was an error executing the database query",
        "log" => $responseLog
    ]);
}

$responseLog[] = "query executed";

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $responseLog[] = "no user found";
    return_answer([
        "success" => false,
        "message" => "No account matches the provided credentials.",
        "log" => $responseLog
    ]);
}

$responseLog[] = "user found";
$responseLog[] = ["user_id" => $user["id"]];

if (!password_verify($password, $user["password"])) {
    $responseLog[] = "Incorrect password. Please try again.";
    return_answer([
        "success" => false,
        "message" => "Incorrect password. Please try again.",
        "log" => $responseLog
    ]);
}

$responseLog[] = "password verified";

$_SESSION["crm_user"] = [
    "id" => $user["id"],
    "username" => $user["username"],
    "email" => $user["email"],
    "phone" => $user["phone"],
    "name" => $user["name"],
    "specialty" => $user["specialty"],
    "level" => $user["level"]
];

$responseLog[] = "session set";

if ($rememberMe) {
    $tokens = generateTokens($user["id"], $user["password"]);
    setcookie("ACCESS_USER_TOKEN", $tokens["accessToken"], time() + (86400 * 365), "/");
    setcookie("ACCESS_REFRESH_USER_TOKEN", $tokens["refreshToken"], time() + (86400 * 365), "/");
    $responseLog[] = "rememberMe cookies set";
}

return_answer([
    "success" => true,
    "redirect" => "/crm/pages/index.php",
    "log" => $responseLog
]);


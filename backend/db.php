<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (headers_sent($file, $line)) {
    error_log("⚠️ Headers already sent in $file on line $line");
}

if (session_status() === PHP_SESSION_NONE) {
    $cookieDomain = getenv("CRM_COOKIE_DOMAIN");
    if ($cookieDomain === false || $cookieDomain === "") {
        $cookieDomain = "bd24af2b.sitepreview.org";
    }

    $cookieSecureEnv = getenv("CRM_COOKIE_SECURE");
    $cookieSecure = ($cookieSecureEnv === false || $cookieSecureEnv === "")
        ? true
        : filter_var($cookieSecureEnv, FILTER_VALIDATE_BOOLEAN);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $cookieDomain,
        'secure' => $cookieSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

const MAIN_PAGE_LINK = "/crm/pages/login.php";

require_once __DIR__ . "/data.php";
require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/token_functions.php";

// Check tokens
if (!isAuth() && isset($_COOKIE["ACCESS_USER_TOKEN"]) && isset($_COOKIE["ACCESS_REFRESH_USER_TOKEN"])) {
    // Check for request from mobile app
    $result = checkAndUpdateToken($_COOKIE["ACCESS_USER_TOKEN"], $_COOKIE["ACCESS_REFRESH_USER_TOKEN"]);
    $userId = -1;

    if (($result["status"] === TOKEN_STATUS_OK || $result["status"] === TOKEN_STATUS_REFRESH) && isset($result["password"])) {
        $userId = $result["userId"];

        $stmt = mysqli_prepare($conMain, "SELECT * FROM `users_crm` WHERE `id` = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_array(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        if ($user && $user["password"] === $result["password"]) {
            $_SESSION["crm_user"] = [
                "id" => $user["id"],
                "username" => $user["username"],
                "email" => $user["email"],
                "phone" => $user["phone"],
                "name" => $user["name"],
                "level" => $user["level"]
            ];
        }
    }
}

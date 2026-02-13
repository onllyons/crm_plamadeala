<?php
ob_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/db.php";

unset($_SESSION["crm_user"]);

setcookie("ACCESS_USER_TOKEN", "", time() - 3600, "/");
setcookie("ACCESS_REFRESH_USER_TOKEN", "", time() - 3600, "/");

header("Location: /crm/pages/login.php");
exit;

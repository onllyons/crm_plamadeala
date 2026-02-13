<?php

define("DB_MAIN_USERNAME", getenv("DB_MAIN_USERNAME") ?: "studiosp_design");
define("DB_MAIN_NAME", getenv("DB_MAIN_NAME") ?: "studiosp_design");
define("DB_HOST", getenv("DB_HOST") ?: "localhost");
define("DB_PASS", getenv("DB_PASS") ?: "9&+5Vx%Si@e.");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conMain = null;
$lastExceptionMessage = "";
$attempts = 20;
$sleepMicroseconds = 500000;

for ($i = 0; $i < $attempts; $i++) {
    try {
        $conMain = mysqli_connect(DB_HOST, DB_MAIN_USERNAME, DB_PASS, DB_MAIN_NAME);
        if ($conMain) {
            break;
        }
    } catch (mysqli_sql_exception $e) {
        $lastExceptionMessage = $e->getMessage();
    }

    usleep($sleepMicroseconds);
}

if (!$conMain) {
    die("Err: DB connection failed after retries. " . $lastExceptionMessage);
}

if (!mysqli_set_charset($conMain, "utf8mb4")) {
    die("Setarea charset-ului a eșuat: " . mysqli_error($conMain));
}

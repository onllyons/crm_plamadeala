<?php

function return_answer($data)
{
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode($data);
    exit();
}


function isAuth()
{
    return isset($_SESSION["crm_user"]);
}

function checkAuth($needLevel = 0)
{
    if (!isset($_SESSION["crm_user"])) {
        header("Location: " . MAIN_PAGE_LINK);
        exit();
    }
}

function getIp()
{
    $keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR'
    ];

    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $arr = explode(',', $_SERVER[$key]);
            $ip = trim(end($arr));

            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
}

function randomHash($length = 32)
{
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($length / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
    } else {
        // Fallback: use a less secure method
        $bytes = '';
        while (strlen($bytes) < ceil($length / 2)) {
            $bytes .= chr(mt_rand(0, 255));
        }
    }

    return substr(bin2hex($bytes), 0, $length);
} 
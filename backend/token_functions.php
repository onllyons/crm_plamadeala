<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/vendors/Firebase_JWT/JWTExceptionWithPayloadInterface.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/vendors/Firebase_JWT/SignatureInvalidException.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/vendors/Firebase_JWT/ExpiredException.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/vendors/Firebase_JWT/BeforeValidException.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/vendors/Firebase_JWT/JWT.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/vendors/Firebase_JWT/Key.php";

use Firebase\JWT\JWTExceptionWithPayloadInterface;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

const ACCESS_TOKEN_KEY = "a3f19b7c29d74c1e981b2064fd7bfae238a67cc1b125e60e9ac4598ed9d74b52";
const ACCESS_TOKEN_REFRESH_KEY = "a3f19b7c29d74c1e981b2064fd7bfae238a67cc1b125e60e9ac4598ed9d74bee";

const TOKEN_STATUS_OK = 1;
const TOKEN_STATUS_REFRESH = 2;
const TOKEN_STATUS_EXPIRED_REFRESH = 3;
const TOKEN_STATUS_ERROR = 4;

/**
 * @param string $accessToken
 * @param string $refreshToken
 *
 * @return array
 */
function checkAndUpdateToken($accessToken, $refreshToken)
{
    try {
        $decodedAccessToken = JWT::decode($accessToken, new Key(ACCESS_TOKEN_KEY, "HS512"));

        return [
            "status" => TOKEN_STATUS_OK,
            "token" => $accessToken,
            "userId" => $decodedAccessToken->userId,
            "password" => $decodedAccessToken->password ?? "",
            "byGoogle" => $decodedAccessToken->byGoogle ?? 0
        ];
    } catch (ExpiredException $e) {
        try {
            $decodedRefreshToken = JWT::decode($refreshToken, new Key(ACCESS_TOKEN_REFRESH_KEY, "HS512"));

            $newTokens = generateTokens($decodedRefreshToken->userId, $decodedRefreshToken->data ?? "");

            return [
                "status" => TOKEN_STATUS_REFRESH,
                "token" => $newTokens["accessToken"],
                "userId" => $decodedRefreshToken->userId,
                "password" => $decodedRefreshToken->password ?? "",
                "byGoogle" => $decodedRefreshToken->byGoogle ?? 0
            ];
        } catch (ExpiredException $e) {
            return ["status" => TOKEN_STATUS_EXPIRED_REFRESH];
        }
    } catch (BeforeValidException|SignatureInvalidException|Exception $e) {
        return ["status" => TOKEN_STATUS_ERROR];
    }
}

/**
 * @param int $userId
 *
 * @return array
 */
function generateTokens($userId, $password)
{
    $accessTokenExpiration = time() + 604800; // 7 days
    $refreshTokenExpiration = time() + 2629056; // Month

    $accessTokenPayload = [
        "userId" => (int)$userId,
        "exp" => $accessTokenExpiration,
        "password" => $password
    ];

    $refreshTokenPayload = [
        "userId" => (int)$userId,
        "exp" => $refreshTokenExpiration,
        "password" => $password
    ];

    $accessToken = JWT::encode($accessTokenPayload, ACCESS_TOKEN_KEY, "HS512");
    $refreshToken = JWT::encode($refreshTokenPayload, ACCESS_TOKEN_REFRESH_KEY, "HS512");

    return ["accessToken" => $accessToken, "refreshToken" => $refreshToken];
}
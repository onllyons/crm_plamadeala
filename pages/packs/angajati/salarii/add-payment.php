<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
header('Content-Type: application/json');

$response = ["success" => false];

try {
    if (!isset($_SESSION["crm_user"]["id"])) throw new Exception("not_logged_in");

    $created_by  = (int)$_SESSION["crm_user"]["id"];
    $employee_id = (int)($_POST['employee_id'] ?? 0);
    $project_id  = (int)($_POST['project_id'] ?? 0);
    $amount      = (float)($_POST['amount'] ?? 0);
    $currency    = trim($_POST['currency'] ?? 'USD');
    $note        = trim($_POST['note'] ?? '');

    if ($employee_id <= 0 || $project_id <= 0 || $amount <= 0)
        throw new Exception("invalid_params");

    $stmt = $conMain->prepare("
        INSERT INTO employee_payments (employee_id, project_id, amount, currency, note, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) throw new Exception("Prepare failed: ".$conMain->error);

    $stmt->bind_param("iidssi", $employee_id, $project_id, $amount, $currency, $note, $created_by);
    if (!$stmt->execute()) throw new Exception("Execute failed: ".$stmt->error);

    $response["success"] = true;
    $response["message"] = "Payment inserted";
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
    error_log("❌ add-payment.php error: ".$e->getMessage());
}

echo json_encode($response);
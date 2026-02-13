<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/payment_logic.php";
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
header('Content-Type: application/json');

$response = ["success" => false];

try {
    if (!isset($_SESSION["crm_user"]["id"])) throw new Exception("not_logged_in");

    $created_by  = (int)$_SESSION["crm_user"]["id"];
    $employee_id = (int)($_POST['employee_id'] ?? 0);
    $project_id  = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
    $amount      = (float)($_POST['amount'] ?? 0);
    $currency    = strtoupper(trim($_POST['currency'] ?? 'EUR'));
    $paymentType = normalizePaymentType($_POST['payment_type'] ?? 'project');
    $note        = trim($_POST['note'] ?? '');

    if ($employee_id <= 0 || $amount <= 0)
        throw new Exception("invalid_params");

    if (!in_array($currency, ["EUR", "MDL", "USD"], true)) {
        throw new Exception("invalid_currency");
    }

    $insertProjectId = null;
    $remaining = null;
    $transactionStarted = false;

    mysqli_begin_transaction($conMain);
    $transactionStarted = true;

    if (isProjectPaymentType($paymentType)) {
        if ($project_id <= 0) {
            throw new Exception("project_required");
        }

        $stmt = $conMain->prepare("SELECT id, title, surface, employees FROM projects WHERE id = ? FOR UPDATE");
        if (!$stmt) throw new Exception("Prepare failed: " . $conMain->error);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $project = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$project) {
            throw new Exception("project_not_found");
        }

        $employeesText = (string)($project["employees"] ?? "");
        $token = "[" . $employee_id . "]";
        if (strpos($employeesText, $token) === false) {
            throw new Exception("employee_not_in_project");
        }

        $stmt = $conMain->prepare("SELECT pret_m2 FROM angajati WHERE id = ?");
        if (!$stmt) throw new Exception("Prepare failed: " . $conMain->error);
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $stmt->bind_result($rate);
        $stmt->fetch();
        $stmt->close();

        $rate = (float)($rate ?? 0);
        $surface = (float)($project["surface"] ?? 0);
        $due = extractEmployeeDue($employee_id, $employeesText, $rate, $surface);

        if ($due <= 0) {
            throw new Exception("project_due_not_found");
        }

        $stmt = $conMain->prepare("
            SELECT COALESCE(SUM(amount),0)
            FROM employee_payments
            WHERE employee_id = ?
              AND project_id = ?
              AND (payment_type = 'project' OR payment_type IS NULL)
        ");
        if (!$stmt) throw new Exception("Prepare failed: " . $conMain->error);
        $stmt->bind_param("ii", $employee_id, $project_id);
        $stmt->execute();
        $stmt->bind_result($alreadyPaid);
        $stmt->fetch();
        $stmt->close();

        $alreadyPaid = (float)$alreadyPaid;
        $remaining = max(0.0, round($due - $alreadyPaid, 2));

        if ($amount - $remaining > 0.00001) {
            throw new Exception("amount_exceeds_remaining");
        }

        $insertProjectId = $project_id;
    }

    $stmt = $conMain->prepare("
        INSERT INTO employee_payments (employee_id, project_id, payment_type, amount, currency, note, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) throw new Exception("Prepare failed: ".$conMain->error);

    $stmt->bind_param("iisdssi", $employee_id, $insertProjectId, $paymentType, $amount, $currency, $note, $created_by);
    if (!$stmt->execute()) throw new Exception("Execute failed: ".$stmt->error);
    $stmt->close();

    mysqli_commit($conMain);

    $response["success"] = true;
    $response["message"] = "Payment inserted";
    $response["payment_type"] = $paymentType;
    if ($remaining !== null) {
        $response["remaining_before"] = $remaining;
        $response["remaining_after"] = max(0.0, round($remaining - $amount, 2));
    }
} catch (Exception $e) {
    if (!empty($transactionStarted)) {
        mysqli_rollback($conMain);
    }
    $response["error"] = $e->getMessage();
    if (isset($remaining)) {
        $response["remaining"] = $remaining;
    }
    error_log("❌ add-payment.php error: ".$e->getMessage());
}

echo json_encode($response);

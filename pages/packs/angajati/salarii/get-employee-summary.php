<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/payment_logic.php";
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

header('Content-Type: application/json');

$employee_id = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : 0;
if ($employee_id <= 0) {
  echo json_encode(["success" => false, "error" => "employee_id invalid"]);
  exit;
}

/** 1) tarif real (pret_m2) din angajati */
$stmt = $conMain->prepare("SELECT pret_m2 FROM angajati WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($pret_m2);
$stmt->fetch();
$stmt->close();
$pret_m2 = (float)($pret_m2 ?? 0);

/** 2) proiecte unde apare angajatul */
$like = "%[" . $employee_id . "]%";
$stmt = $conMain->prepare("SELECT id, title, surface, employees FROM projects WHERE employees LIKE ?");
$stmt->bind_param("s", $like);
$stmt->execute();
$res = $stmt->get_result();

$projects = [];
$total_due = 0.0;

/** 2.1) total plăți proiecte pe fiecare proiect */
$stmtPaidMap = $conMain->prepare("
  SELECT project_id, COALESCE(SUM(amount),0) AS paid_total
  FROM employee_payments
  WHERE employee_id = ?
    AND project_id IS NOT NULL
    AND (payment_type = 'project' OR payment_type IS NULL)
  GROUP BY project_id
");
$stmtPaidMap->bind_param("i", $employee_id);
$stmtPaidMap->execute();
$paidRes = $stmtPaidMap->get_result();

$paidByProject = [];
while ($pr = $paidRes->fetch_assoc()) {
  $paidByProject[(int)$pr["project_id"]] = (float)$pr["paid_total"];
}
$stmtPaidMap->close();

while ($r = $res->fetch_assoc()) {
  $projectId = (int)$r["id"];
  $surface = (float)($r["surface"] ?? 0);
  $due = extractEmployeeDue($employee_id, (string)($r["employees"] ?? ""), $pret_m2, $surface);
  $paid = (float)($paidByProject[$projectId] ?? 0);
  $remaining = max(0.0, round($due - $paid, 2));

  $total_due += $due;

  $projects[] = [
    "id" => $projectId,
    "title" => $r["title"],
    "surface" => $surface,
    "due_total" => $due,
    "paid_total" => $paid,
    "remaining" => $remaining
  ];
}
$stmt->close();

/** 3) total plătit pe proiecte */
$stmt = $conMain->prepare("
  SELECT COALESCE(SUM(amount),0)
  FROM employee_payments
  WHERE employee_id = ?
    AND (payment_type = 'project' OR payment_type IS NULL)
");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($total_paid_projects);
$stmt->fetch();
$stmt->close();

/** 3.1) total plătit extra (avans/bonus/extra) */
$stmt = $conMain->prepare("
  SELECT COALESCE(SUM(amount),0)
  FROM employee_payments
  WHERE employee_id = ?
    AND payment_type IN ('advance', 'bonus', 'extra')
");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($total_paid_extras);
$stmt->fetch();
$stmt->close();

$total_paid_projects = (float)$total_paid_projects;
$total_paid_extras = (float)$total_paid_extras;
$total_paid_all = $total_paid_projects + $total_paid_extras;
$balance = round($total_due - $total_paid_projects, 2);

/** 4) istoric plăți */
$stmt = $conMain->prepare("
  SELECT ep.id, ep.project_id, ep.payment_type, p.title AS project_title, ep.amount, ep.currency, ep.note, ep.created_by, ep.created_at
  FROM employee_payments ep
  LEFT JOIN projects p ON p.id = ep.project_id
  WHERE ep.employee_id = ?
  ORDER BY ep.created_at DESC
  LIMIT 50
");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$hist = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/** 5) răspuns final */
echo json_encode([
  "success" => true,
  "employee_id" => $employee_id,
  "rate" => $pret_m2,
  "total_due" => round($total_due, 2),
  "total_paid_projects" => round($total_paid_projects, 2),
  "total_paid_extras" => round($total_paid_extras, 2),
  "total_paid" => round($total_paid_all, 2),
  "balance" => $balance,
  "projects" => $projects,
  "payments" => $hist
]);

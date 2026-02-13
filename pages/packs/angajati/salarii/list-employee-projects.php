<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/payment_logic.php";
header('Content-Type: application/json');

$employee_id = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : 0;
if ($employee_id <= 0) { echo json_encode([]); exit; }

$like = "%[" . $employee_id . "]%";

/** tarif angajat */
$stmtRate = $conMain->prepare("SELECT pret_m2 FROM angajati WHERE id = ?");
$stmtRate->bind_param("i", $employee_id);
$stmtRate->execute();
$stmtRate->bind_result($pret_m2);
$stmtRate->fetch();
$stmtRate->close();
$pret_m2 = (float)($pret_m2 ?? 0);

/** proiecte asociate */
$stmtProjects = $conMain->prepare("
  SELECT 
    p.id, 
    p.client_id, 
    p.title, 
    p.surface,
    p.employees,
    c.last_name_first_name AS client_name
  FROM projects p
  LEFT JOIN clienti c ON c.id = p.client_id
  WHERE p.employees LIKE BINARY ?
  ORDER BY p.created_at DESC
");
$stmtProjects->bind_param("s", $like);
$stmtProjects->execute();
$res = $stmtProjects->get_result();

/** plăți deja făcute pe proiecte */
$stmtPaid = $conMain->prepare("
  SELECT project_id, COALESCE(SUM(amount),0) AS paid_total
  FROM employee_payments
  WHERE employee_id = ?
    AND project_id IS NOT NULL
    AND (payment_type = 'project' OR payment_type IS NULL)
  GROUP BY project_id
");
$stmtPaid->bind_param("i", $employee_id);
$stmtPaid->execute();
$paidRes = $stmtPaid->get_result();

$paidByProject = [];
while ($row = $paidRes->fetch_assoc()) {
  $paidByProject[(int)$row["project_id"]] = (float)$row["paid_total"];
}
$stmtPaid->close();

$data = [];
while ($r = $res->fetch_assoc()) {
  $projectId = (int)$r["id"];
  $surface = (float)($r["surface"] ?? 0);
  $due = extractEmployeeDue($employee_id, (string)($r["employees"] ?? ""), $pret_m2, $surface);
  $paid = (float)($paidByProject[$projectId] ?? 0);
  $remaining = max(0.0, round($due - $paid, 2));

  $data[] = [
    "id" => $projectId,
    "client_id" => (int)$r["client_id"],
    "title" => $r["title"],
    "client_name" => $r["client_name"],
    "due_total" => $due,
    "paid_total" => $paid,
    "remaining" => $remaining
  ];
}
$stmtProjects->close();

echo json_encode($data);
?>

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

header('Content-Type: application/json');

$employee_id = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : 0;
if ($employee_id <= 0) {
  echo json_encode(["success" => false, "error" => "employee_id invalid"]);
  exit;
}

/** 1) pret_m2 din angajati */
$stmt = $conMain->prepare("SELECT pret_m2 FROM angajati WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($pret_m2);
$stmt->fetch();
$stmt->close();
$pret_m2 = (float)($pret_m2 ?? 0);

/** 2) proiecte unde apare angajatul (employees e text cu [id] - Nume) */
$like = "%[" . $employee_id . "]%";
$stmt = $conMain->prepare("SELECT id, title, employees FROM projects WHERE employees LIKE ?");
$stmt->bind_param("s", $like);
$stmt->execute();
$res = $stmt->get_result();

$projects = [];
$total_due = 0.0;

while ($r = $res->fetch_assoc()) {
  $employees_text = $r['employees'] ?? '';
  $sum_for_emp = 0.0;

  // caută valoarea de după „×” în expresia "(x × y)"
  if (preg_match('/\[' . $employee_id . '\][^\(]*\(([^×]+)×\s*([^)]+)\)/u', $employees_text, $m)) {
    $sum_for_emp = (float)trim(str_replace(',', '.', $m[2])); // a doua valoare = suma reală
  }

  $total_due += $sum_for_emp;

  $projects[] = [
    "id" => (int)$r['id'],
    "title" => $r['title'],
    "sum_employee" => $sum_for_emp
  ];
}
$stmt->close();

/** 3) total plătit până acum */
$stmt = $conMain->prepare("SELECT COALESCE(SUM(amount),0) FROM employee_payments WHERE employee_id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($total_paid);
$stmt->fetch();
$stmt->close();

$total_paid = (float)$total_paid;
$balance = $total_due - $total_paid;

/** 4) istoric plăți (ultimele 50) */
$stmt = $conMain->prepare("
  SELECT ep.id, ep.project_id, p.title AS project_title, ep.amount, ep.currency, ep.note, ep.created_by, ep.created_at
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
  "rate" => $pret_m2,         // tarif din angajati
  "total_due" => $total_due,  // total calculat din textul employees
  "total_paid" => $total_paid,
  "balance" => $balance,
  "projects" => $projects,
  "payments" => $hist
]);
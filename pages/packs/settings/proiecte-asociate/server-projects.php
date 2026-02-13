<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
header('Content-Type: application/json');

function removeDiacritics($s){$m=['ă'=>'a','â'=>'a','î'=>'i','ș'=>'s','ş'=>'s','ț'=>'t','ţ'=>'t','Ă'=>'A','Â'=>'A','Î'=>'I','Ș'=>'S','Ş'=>'S','Ț'=>'T','Ţ'=>'T'];return strtr($s,$m);}
function createSlug($name,$id){$name=removeDiacritics($name);$name=strtolower($name);$name=preg_replace('/[^a-z0-9]+/','-',$name);$name=trim($name,'-');return "{$name}-{$id}";}

if (!isset($_GET['employee_id'])) { echo json_encode(['success'=>false]); exit; }

$id = (int)$_GET['employee_id'];
$like1 = "%[$id] -%";
$like2 = "%, [$id] -%";

/* 1) Angajat */
$employee = null;
$empStmt = $conMain->prepare("SELECT id, last_name_first_name, pret_m2 FROM angajati WHERE id = ?");
$empStmt->bind_param("i", $id);
$empStmt->execute();
if ($er = $empStmt->get_result()->fetch_assoc()) {
  $employee = [
    'id' => (int)$er['id'],
    'name' => $er['last_name_first_name'],
    'pret_m2' => (float)$er['pret_m2']
  ];
}
$empStmt->close();

/* 2) Proiectele */
$sql = "
SELECT 
  p.id, p.client_id, c.last_name_first_name AS client_name,
  p.title, p.stage, p.surface, p.price_per_m2, p.total_price, p.currency,
  p.date_received, p.date_technical, p.date_3d, p.date_deadline,
  p.employees, p.created_at
FROM projects p
LEFT JOIN clienti c ON p.client_id = c.id
WHERE p.employees LIKE ? OR p.employees LIKE ?
ORDER BY p.date_received DESC
";
$stmt = $conMain->prepare($sql);
$stmt->bind_param("ss", $like1, $like2);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
// while ($row = $result->fetch_assoc()) {
//   $row['client_slug'] = $row['client_name'] ? createSlug($row['client_name'], $row['client_id']) : null;

//   // 🔹 suma reală pentru acest angajat în acest proiect: valoarea de DUPĂ „×”
//   $employees_text = $row['employees'] ?? '';
//   $sum_for_emp = 0.0;
//   if (preg_match('/\[' . $id . '\][^\(]*\(([^×]+)×\s*([^)]+)\)/u', $employees_text, $m)) {
//     $sum_for_emp = (float)trim(str_replace(',', '.', $m[2]));
//   }
//   $row['sum_employee'] = $sum_for_emp;

//   $projects[] = $row;
// }

function employeesDisplayOnly(?string $employees): string {
    if (!$employees) return '';
    return preg_replace('/\s*\(.*?\)\s*/', '', $employees);
}

while ($row = $result->fetch_assoc()) {
  $row['client_slug'] = $row['client_name']
    ? createSlug($row['client_name'], $row['client_id'])
    : null;

  // 1️⃣ calcule pe text ORIGINAL
  $employees_text = $row['employees'] ?? '';
  $sum_for_emp = 0.0;

  if (preg_match('/\[' . $id . '\][^\(]*\(([^×]+)×\s*([^)]+)\)/u', $employees_text, $m)) {
    $sum_for_emp = (float) trim(str_replace(',', '.', $m[2]));
  }

  $row['sum_employee'] = $sum_for_emp;

  // 2️⃣ curățare pentru frontend
  $row['employees'] = employeesDisplayOnly($row['employees']);

  $projects[] = $row;
}


$stmt->close();

/* 3) Istoric plăți pe proiect */
$paymentsStmt = $conMain->prepare("
  SELECT id, employee_id, project_id, amount, currency, note, created_by, created_at 
  FROM employee_payments 
  WHERE project_id = ? AND employee_id = ?
  ORDER BY created_at DESC
");
for ($i = 0; $i < count($projects); $i++) {
  $paymentsStmt->bind_param("ii", $projects[$i]['id'], $id);
  $paymentsStmt->execute();
  $res2 = $paymentsStmt->get_result();
  $projects[$i]['payments'] = [];
  while ($pay = $res2->fetch_assoc()) {
    $projects[$i]['payments'][] = $pay;
  }
}
$paymentsStmt->close();

/* 4) Grupare pe lună */
$grouped = [];
foreach ($projects as $p) {
  $month = $p['date_received'] ? date('Y-m', strtotime($p['date_received'])) : 'Fără dată';
  $grouped[$month][] = $p;
}

/* 5) Răspuns */
echo json_encode([
  'success' => true,
  'employee' => $employee,
  'projects_grouped' => $grouped
]);
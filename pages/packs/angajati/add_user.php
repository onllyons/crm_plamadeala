<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/db.php";
header('Content-Type: application/json');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(403);
  echo json_encode(['status' => 'error', 'message' => 'Access denied']);
  exit;
}

function clean($v){ return trim($v ?? ''); }

try {
  // ------------------- PORNIM TRANZACȚIA PRIMA -------------------
  $conMain->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

  // ------------------- INPUT -------------------
  $last_name_first_name = clean($_POST['last_name_first_name'] ?? '');
  $position_function    = clean($_POST['position_function'] ?? '');
  $phone_number         = clean($_POST['phone_number'] ?? '');
  $user_email_field     = clean($_POST['user_email_field'] ?? '');
  $pret_m2              = clean($_POST['pret_m2'] ?? '');
  $user_status_field    = clean($_POST['user_status_field'] ?? '');
  $dateAdded            = clean($_POST['dateAdded'] ?? date('Y-m-d'));

  // Profil
  $username_profile = clean($_POST['username_profile'] ?? '');
  $password_profile = clean($_POST['password_profile'] ?? '');
  $password_repeat  = clean($_POST['password_repeat_profile'] ?? '');
  $level_profile    = clean($_POST['level_profile'] ?? 'Employed');
  $isProfileActive  = ($username_profile !== '' || $password_profile !== '' || $password_repeat !== '');

  // ------------------- VALIDĂRI PROFIL -------------------
  if ($isProfileActive) {
    if ($password_profile !== $password_repeat) {
      throw new Exception('Parolele nu coincid.');
    }

    $conds = []; $types = ''; $vals = [];
    if ($username_profile !== '') { $conds[] = 'username = ?'; $types .= 's'; $vals[] = $username_profile; }
    if ($user_email_field  !== '') { $conds[] = 'email = ?';    $types .= 's'; $vals[] = $user_email_field; }

    if ($conds) {
      $sql = "SELECT id FROM users_crm WHERE " . implode(' OR ', $conds) . " LIMIT 1";
      $stmt = $conMain->prepare($sql);
      $stmt->bind_param($types, ...$vals);
      $stmt->execute();
      $dup = $stmt->get_result();
      if ($dup && $dup->num_rows > 0) {
        throw new Exception('Acest utilizator sau email există deja.');
      }
    }
  }

  // ------------------- INSERARE ANGAJAT -------------------
  $stmt = $conMain->prepare("
    INSERT INTO angajati
      (last_name_first_name, position_function, phone_number, user_email_field, pret_m2, user_status_field, dateAdded)
    VALUES
      (?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->bind_param("sssssss",
    $last_name_first_name,
    $position_function,
    $phone_number,
    $user_email_field,
    $pret_m2,
    $user_status_field,
    $dateAdded
  );
  $stmt->execute();

  // ------------------- INSERARE PROFIL -------------------
  if ($isProfileActive) {
    $password_hash = password_hash($password_profile, PASSWORD_BCRYPT);
    $stmt = $conMain->prepare("
      INSERT INTO users_crm (username, email, phone, name, level, password)
      VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssss",
      $username_profile,
      $user_email_field,
      $phone_number,
      $last_name_first_name,
      $level_profile,
      $password_hash
    );
    $stmt->execute();
  }

  // ------------------- COMMIT -------------------
  $conMain->commit();
  echo json_encode(['status' => 'success', 'profile' => $isProfileActive ? 'created' : 'skipped']);
}
catch (Throwable $e) {
  try { $conMain->rollback(); } catch (Throwable $ignored) {}
  echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'rolled_back' => true]);
}
?>

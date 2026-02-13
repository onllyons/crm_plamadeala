<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
$id = $_POST['id'] ?? null;
if ($id) {
  $stmt = $conMain->prepare("DELETE FROM projects WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
}
$conMain->close();
echo 'success';
?>

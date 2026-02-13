<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/db.php";
header('Content-Type: application/json');

$page = intval($_GET['page'] ?? 1);
$limit = 12;
$offset = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');

$where = "";
$params = [];
$types = "";

if ($search !== "") {
  $where = "WHERE (pn.files LIKE ? OR u.username LIKE ?)";
  $like = "%$search%";
  $params = [$like, $like];
  $types = "ss";
}

$sql = "
  SELECT pn.id, pn.project_id, pn.user_id, pn.files, pn.created_at, u.username
  FROM project_notes pn
  LEFT JOIN users_crm u ON u.id = pn.user_id
  $where
  ORDER BY pn.id DESC
  LIMIT ?, ?
";

$types .= "ii";
$params[] = $offset;
$params[] = $limit;

$stmt = $conMain->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$files = [];

while ($r = $res->fetch_assoc()) {
    $decoded = [];
    if (!empty($r['files']) && $r['files'] !== 'null') {
        $decoded = json_decode($r['files'], true);
    }
    if (!is_array($decoded)) continue;

    $basePath = "/crm/pages/packs/client/client-page/projects/create-project/like-chat/project_notes/";

    foreach ($decoded as $filePath) {
        $filePath = trim($filePath);
        if (!$filePath) continue;

        // 🔹 adaugăm prefixul corect
        $fileUrl = $basePath . ltrim($filePath, '/');

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $fileName = basename($filePath);

        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $thumb = "<img src='$fileUrl' alt='thumb' class='file-thumb'>";
            $isImage = true;
        } elseif ($ext === 'pdf') {
            $thumb = "<div class='d-flex align-items-center justify-content-center file-thumb'>
                        <i class='fas fa-file-pdf file-icon text-danger'></i>
                      </div>";
            $isImage = false;
        } elseif (in_array($ext, ['zip','rar','7z'])) {
            $thumb = "<div class='d-flex align-items-center justify-content-center file-thumb'>
                        <i class='fas fa-file-archive file-icon text-warning'></i>
                      </div>";
            $isImage = false;
        } elseif (in_array($ext, ['doc','docx','odt','txt','xlsx','xls'])) {
            $thumb = "<div class='d-flex align-items-center justify-content-center file-thumb'>
                        <i class='fas fa-file-word file-icon text-primary'></i>
                      </div>";
            $isImage = false;
        } else {
            $thumb = "<div class='d-flex align-items-center justify-content-center file-thumb'>
                        <i class='fas fa-file file-icon text-secondary'></i>
                      </div>";
            $isImage = false;
        }

        $files[] = [
            'note_id' => $r['id'],
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'username' => $r['username'] ?: '-',
            'created_at' => date('Y-m-d', strtotime($r['created_at'])),
            'thumb' => $thumb,
            'is_image' => $isImage,
        ];
    }

}


$countRes = $conMain->query("SELECT COUNT(*) AS c FROM project_notes");
$total = (int)$countRes->fetch_assoc()['c'];
$hasMore = ($offset + $limit < $total);

echo json_encode(['success'=>true, 'files'=>$files, 'has_more'=>$hasMore]);

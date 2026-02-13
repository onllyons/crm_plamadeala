<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/db.php";

$user_level = isset($_SESSION["crm_user"]["level"]) ? (int)$_SESSION["crm_user"]["level"] : 1;

$output = [];
$sql = "SELECT * FROM angajati";
$totalQuery = mysqli_query($conMain, $sql);
$total_all_rows = mysqli_num_rows($totalQuery);
$columns = [
    0 => 'id',
    1 => 'last_name_first_name',
    3 => 'position_function',
    5 => 'phone_number',
    6 => 'user_email_field',
    6 => 'pret_m2',
    6 => 'user_status_field',
    7 => 'dateAdded',
];
if (isset($_POST['search']['value']) or isset($_POST['startDate']) or isset($_POST['endDate'])) {
    $sql .= " WHERE ";
}
if (isset($_POST['search']['value'])) {
    if (strlen($_POST['search']['value']) > 0) {
        $search_value = $_POST['search']['value'];
        $sql .= "nume last_name_first_name '%" . $search_value . "%'";
        $sql .= "OR position_function like '%" . $search_value . "%'";
        $sql .= "OR phone_number like '%" . $search_value . "%'";
        $sql .= "OR user_email_field like '%" . $search_value . "%'";
        $sql .= "OR pret_m2 like '%" . $search_value . "%'";
        $sql .= "OR user_status_field like '%" . $search_value . "%'";
        $sql .= "OR dateAdded like '%" . $search_value . "%' AND";
    }
}


if (isset($_POST['data_ajax_position_function'])) {
    if (is_array($_POST['data_ajax_position_function']) and count($_POST['data_ajax_position_function']) != 0) {
        $search = implode("','", $_POST['data_ajax_position_function']);
        $sql .= " position_function IN ('{$search}') AND ";
    }
}


if (isset($_POST['data_ajax_user_status_field'])) {
    if (is_array($_POST['data_ajax_user_status_field']) and count($_POST['data_ajax_user_status_field']) != 0) {
        $search = implode("','", $_POST['data_ajax_user_status_field']);
        $sql .= " user_status_field IN ('{$search}') AND ";
    }
}


if (isset($_POST['data_ajax_phone_number'])) {
    if (is_array($_POST['data_ajax_phone_number']) and count($_POST['data_ajax_phone_number']) != 0) {
        $search = implode("','", $_POST['data_ajax_phone_number']);
        $sql .= " phone_number IN ('{$search}') AND ";
    }
}

if (isset($_POST['startDate']) and isset($_POST['endDate'])) {
    $start = $_POST['startDate'] > 0 ? $_POST['startDate'] : '1900-01-01';
    $end = $_POST['endDate'] > 0 ? $_POST['endDate'] : '2100-01-01';

    $sql .= " dateAdded >= '{$start}' and dateAdded <='{$end}'";
}
if (isset($_POST['order'])) {
    $column_name = $_POST['order'][0]['column'];
    $order = $_POST['order'][0]['dir'];
    $sql .= " ORDER BY " . $columns[$column_name] . " " . $order . "";
} else {
    $sql .= " ORDER BY id desc";
}

if ($_POST['length'] != -1) {
    $start = $_POST['start'];
    $length = $_POST['length'];
    $sql .= " LIMIT  " . $start . ", " . $length;
}
$query = mysqli_query($conMain, $sql);
$count_rows = mysqli_num_rows($query);
$data = [];

function removeDiacritics($string) {
    $diacritics = [
        'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ş' => 's', 'ț' => 't', 'ţ' => 't',
        'Ă' => 'A', 'Â' => 'A', 'Î' => 'I', 'Ș' => 'S', 'Ş' => 'S', 'Ț' => 'T', 'Ţ' => 'T',
    ];
    return strtr($string, $diacritics);
}

function createSlug($name, $id) {
    $name = removeDiacritics($name);
    $name = strtolower($name);
    $name = preg_replace('/[^a-z0-9]+/', '-', $name);
    $name = trim($name, '-');
    return "{$name}-{$id}";
}
    
while ($row = mysqli_fetch_assoc($query)) {
    $sub_array = [];

    $slug = createSlug($row['last_name_first_name'], $row['id']);
    $linkName = '<a target="_blank" href="/crm/pages/angajati-page.php?slug=' . $slug . '">' . htmlspecialchars($row['last_name_first_name']) . '</a>';

    $sub_array[] = $row['id'];
    $sub_array[] = $linkName;
    $sub_array[] = htmlspecialchars($row['position_function']);
    $sub_array[] = htmlspecialchars($row['phone_number']);
    $sub_array[] = htmlspecialchars($row['user_email_field']);

    if ($user_level === 0) {
        $sub_array[] = htmlspecialchars($row['pret_m2']);
    } else {
        $sub_array[] = '—';
    }

    $sub_array[] = htmlspecialchars($row['user_status_field']);
    $sub_array[] = htmlspecialchars($row['dateAdded']);

    $editBtn = '';
    $deleteBtn = '';
    if ($user_level === 0) {
        $editBtn = '<a href="javascript:void(0);" data-id="' . $row['id'] . '" class="dsh68 editbtn"><i class="fas fa-edit"></i></a>';
        $deleteBtn = ' <a href="javascript:void(0);" data-id="' . $row['id'] . '" class="dsh68 deleteBtn"><i class="fas fa-trash"></i></a>';
    }

    $sub_array[] = $editBtn . $deleteBtn;
    $data[] = $sub_array;
}


$output = [
    'draw' => intval($_POST['draw']),
    'recordsTotal' => $count_rows,
    'recordsFiltered' => $total_all_rows,
    'data' => $data,
];

echo json_encode($output);


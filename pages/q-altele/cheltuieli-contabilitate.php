<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
checkAuth();

$userLevel = isset($_SESSION["crm_user"]["level"]) ? (int)$_SESSION["crm_user"]["level"] : 0;
if ($userLevel !== 0) {
    header("Location: /crm/pages/index.php");
    exit;
}

function esc($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function paymentTypeLabel($type)
{
    $type = strtolower(trim((string)$type));
    switch ($type) {
        case 'advance':
            return 'Avans';
        case 'bonus':
            return 'Bonus';
        case 'extra':
            return 'Munca extra';
        default:
            return 'Plata proiect';
    }
}

function removeDiacritics($text)
{
    $map = [
        'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ş' => 's', 'ț' => 't', 'ţ' => 't',
        'Ă' => 'A', 'Â' => 'A', 'Î' => 'I', 'Ș' => 'S', 'Ş' => 'S', 'Ț' => 'T', 'Ţ' => 'T'
    ];
    return strtr((string)$text, $map);
}

function createSlug($name, $id)
{
    $name = removeDiacritics((string)$name);
    $name = strtolower($name);
    $name = preg_replace('/[^a-z0-9]+/', '-', $name);
    $name = trim((string)$name, '-');
    return $name . '-' . (int)$id;
}

$sql = "
    SELECT
        ep.id,
        ep.employee_id,
        ep.project_id,
        ep.payment_type,
        ep.amount,
        ep.currency,
        ep.note,
        ep.created_by,
        ep.created_at,
        a.last_name_first_name AS employee_name,
        a.position_function AS employee_position,
        p.client_id,
        p.title AS project_title,
        c.last_name_first_name AS client_name,
        u.name AS created_by_name
    FROM employee_payments ep
    LEFT JOIN angajati a ON a.id = ep.employee_id
    LEFT JOIN projects p ON p.id = ep.project_id
    LEFT JOIN clienti c ON c.id = p.client_id
    LEFT JOIN users_crm u ON u.id = ep.created_by
    ORDER BY ep.created_at DESC, ep.id DESC
";

$query = mysqli_query($conMain, $sql);
$rows = [];
$totalsByCurrency = [];

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $rows[] = $row;
        $currency = strtoupper(trim((string)($row['currency'] ?? '')));
        if ($currency === '') {
            $currency = 'N/A';
        }
        if (!isset($totalsByCurrency[$currency])) {
            $totalsByCurrency[$currency] = 0.0;
        }
        $totalsByCurrency[$currency] += (float)$row['amount'];
    }
}

krsort($totalsByCurrency);
$totalTransactions = count($rows);
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
    <?php include '../../assets/components/links.php' ?>
    <?php include '../../assets/components/style-datatables.php' ?>
    <style>
        .summary-card-accounting {
            background: var(--falcon-card-bg);
            border: 1px solid var(--falcon-border-color);
            border-radius: 5px;
            box-shadow: var(--falcon-box-shadow);
            padding: 16px;
        }
        .summary-value {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
        }
        .summary-label {
            color: var(--falcon-gray-600);
            font-size: 0.92rem;
            margin-bottom: 6px;
        }
        .badge-type {
            font-size: 0.76rem;
            font-weight: 600;
            letter-spacing: 0.2px;
        }
    </style>
</head>
<body>
<main class="main" id="top">
    <div class="container" data-layout="container">
        <?php include '../../assets/components/nav-left.php' ?>
        <div class="content">
            <?php include '../../assets/components/nav-top.php' ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 m-0">
                    Cheltuieli Contabilitate
                </h1>
            </div>

            <div class="row g-3" style="margin-bottom: 1rem;">
                <div class="col-sm-6 col-lg-3">
                    <div class="summary-card-accounting h-100">
                        <div class="summary-label">Total tranzactii</div>
                        <p class="summary-value text-primary"><?= esc(number_format($totalTransactions, 0, '.', ' ')) ?></p>
                    </div>
                </div>
                <?php foreach ($totalsByCurrency as $currency => $value): ?>
                    <div class="col-sm-6 col-lg-3">
                        <div class="summary-card-accounting h-100">
                            <div class="summary-label">Total achitat <?= esc($currency) ?></div>
                            <p class="summary-value text-success"><?= esc(number_format((float)$value, 2, '.', ' ')) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card" style="margin-top: 2rem !important;">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-accounting" class="table table-striped align-middle w-100">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Angajat</th>
                                <th>Tip plata</th>
                                <th>Proiect</th>
                                <th>Suma</th>
                                <th>Nota</th>
                                <th>Creat de</th>
                                <th>Creat la</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rows as $row): ?>
                                <?php
                                $type = strtolower((string)($row['payment_type'] ?? 'project'));
                                $badgeClass = 'bg-info';
                                if ($type === 'advance') $badgeClass = 'bg-warning';
                                if ($type === 'bonus') $badgeClass = 'bg-success';
                                if ($type === 'extra') $badgeClass = 'bg-secondary';

                                $employeeName = trim((string)($row['employee_name'] ?? ''));
                                $employeeLabel = $employeeName !== '' ? $employeeName : 'Angajat necunoscut';
                                if (($row['employee_position'] ?? '') !== '') {
                                    $employeeLabel .= ' / ' . $row['employee_position'];
                                }
                                $employeeHtml = esc($employeeLabel);
                                if ((int)$row['employee_id'] > 0 && $employeeName !== '') {
                                    $employeeSlug = createSlug($employeeName, (int)$row['employee_id']);
                                    $employeeUrl = '/crm/pages/angajati-page.php?slug=' . rawurlencode($employeeSlug);
                                    $employeeHtml = '<a class="text-decoration-none" href="' . esc($employeeUrl) . '">' . esc($employeeLabel) . '</a>';
                                }

                                $projectHtml = 'Fara proiect';
                                if (!empty($row['project_id'])) {
                                    $projectLabel = $row['project_title'] ?: 'Titlu lipsa';
                                    $clientId = (int)($row['client_id'] ?? 0);
                                    $clientName = trim((string)($row['client_name'] ?? ''));

                                    if ($clientId > 0 && $clientName !== '') {
                                        $slug = createSlug($clientName, $clientId);
                                        $projectUrl = '/crm/pages/client-page.php?slug=' . rawurlencode($slug);
                                        $projectHtml = '<a class="text-decoration-none" href="' . esc($projectUrl) . '">' . esc($projectLabel) . '</a>';
                                    } else {
                                        $projectHtml = esc($projectLabel);
                                    }
                                }

                                $createdBy = ($row['created_by_name'] ?? '') !== ''
                                    ? $row['created_by_name']
                                    : 'Utilizator necunoscut';
                                ?>
                                <tr>
                                    <td><?= (int)$row['id'] ?></td>
                                    <td><?= $employeeHtml ?></td>
                                    <td><span class="badge <?= esc($badgeClass) ?> badge-type"><?= esc(paymentTypeLabel($row['payment_type'])) ?></span></td>
                                    <td><?= $projectHtml ?></td>
                                    <td><?= esc(number_format((float)$row['amount'], 2, '.', ' ')) ?></td>
                                    <td><?= esc($row['note'] ?? '-') ?></td>
                                    <td><?= esc($createdBy) ?></td>
                                    <td><?= esc($row['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php include '../../assets/components/footer.php' ?>
        </div>
    </div>
</main>

<?php include '../../assets/components/off-canvas-design.php' ?>
<?php include '../../assets/components/scripts.php' ?>
<?php include '../../assets/components/script-datatables.php' ?>

<script>
    $(function () {
        $('#table-accounting').DataTable({
            pageLength: 25,
            order: [[7, 'desc']],
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'csv']
        });
    });
</script>
</body>
</html>

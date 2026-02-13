<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
checkAuth();

if (isset($_SESSION["crm_user"]["level"]) && $_SESSION["crm_user"]["level"] == 1) {
    header("Location: /crm/pages/index.php");
    exit;
}

function removeDiacritics($string) {
    if ($string === null) $string = '';
    $diacritics = [
        'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ş' => 's', 'ț' => 't', 'ţ' => 't',
        'Ă' => 'A', 'Â' => 'A', 'Î' => 'I', 'Ș' => 'S', 'Ş' => 'S', 'Ț' => 'T', 'Ţ' => 'T',
    ];
    return strtr($string, $diacritics);
}

function createSlug($name, $id) {
    $name = removeDiacritics($name ?? '');
    $name = strtolower($name);
    $name = preg_replace('/[^a-z0-9]+/', '-', $name);
    $name = trim($name, '-');
    return "{$name}-{$id}";
}
?>
<!DOCTYPE html>
<html lang="ro" dir="ltr">
<head>
    <?php include '../assets/components/links.php' ?>
    <?php include '../assets/components/style-datatables.php' ?>
    <style>
      .month-table tfoot tr.month-total-row {
        background: var(--falcon-gray-200) !important;
        color: var(--falcon-heading-color);
        font-weight: 600;
      }
      .month-table tfoot td {
        border-top: 1px solid var(--falcon-border-color);
      }
    </style>
</head>
<body>
<main class="main" id="top">
    <div class="container" data-layout="container">
        <?php include '../assets/components/nav-left.php' ?>
        <div class="content">
            <?php include '../assets/components/nav-top.php' ?>

            <div class="mb-3" id="notification_placeholder"></div>
            <div class="">
                <div class="">
                    <h4 class="card-title mb-4">Board Proiecte</h4>

                    <?php
                    // SQL + grupare pe lună
                    $sql = "
                        SELECT 
                            p.id, 
                            c.id AS client_id,
                            c.last_name_first_name AS client_name, 
                            p.title, 
                            p.stage, 
                            p.surface, 
                            p.price_per_m2, 
                            p.total_price,
                            p.advance,
                            p.remainder,
                            p.currency, 
                            p.date_received, 
                            p.employees
                        FROM projects p
                        LEFT JOIN clienti c ON p.client_id = c.id
                        ORDER BY p.date_received DESC, p.id DESC
                    ";

                    $result = $conMain->query($sql);
                    $luniRo = [1=>'Ianuarie',2=>'Februarie',3=>'Martie',4=>'Aprilie',5=>'Mai',6=>'Iunie',7=>'Iulie',8=>'August',9=>'Septembrie',10=>'Octombrie',11=>'Noiembrie',12=>'Decembrie'];

                    $grouped = [];
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $ts = strtotime($row['date_received']);

                            if ($ts === false) {
                                $key = 'Dată necunoscută';
                            } else {
                                $key = $luniRo[(int)date('n', $ts)] . ' ' . date('Y', $ts);
                            }

                            $row['employees'] = preg_replace('/\s*\(.*?\)\s*/', '', $row['employees']);
                            $grouped[$key][] = $row;

                        }
                    }
                    ?>

                    <div class="">
                        <?php foreach ($grouped as $month => $rows): ?>
    <?php
        // calculăm totalurile pentru fiecare lună
        $totalSuprafata = 0;
        $totalAchitata  = 0;
        $totalRest      = 0;
        $totalPret      = 0;
        foreach ($rows as $r) {
            $totalSuprafata += (float)$r['surface'];
            $totalAchitata  += (float)$r['advance'];
            $totalRest      += (float)$r['remainder'];
            $totalPret      += (float)$r['total_price'];
        }
    ?>
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary"><?= htmlspecialchars($month) ?></h5>
        </div>
        <div class="card-body">
            <table class="table display dataTable no-footer dtr-inline month-table" style="width:100%;">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Titlu</th>
                    <th>Etapă</th>
                    <th>Suprafață</th>
                    <th>Preț/m²</th>
                    <th>Achitată</th>
                    <th>Rest</th>
                    <th>Preț total</th>
                    <th>Monedă</th>
                    <th>Proiectanți</th>
                    <th>Primit</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row):
                    $slug = createSlug($row['client_name'], $row['client_id']);
                    $linkName = '<a target="_blank" href="/crm/pages/client-page.php?slug=' . $slug . '">' . htmlspecialchars($row['client_name'] ?? '') . '</a>';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= $linkName ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['stage']) ?></td>
                        <td><?= htmlspecialchars($row['surface']) ?></td>
                        <td><?= htmlspecialchars($row['price_per_m2']) ?></td>
                        <td><?= htmlspecialchars($row['advance']) ?></td>
                        <td><?= htmlspecialchars($row['remainder']) ?></td>
                        <td><?= htmlspecialchars(number_format((float)$row['total_price'], 2)) ?></td>
                        <td><?= htmlspecialchars($row['currency']) ?></td>
                        <td><?= htmlspecialchars($row['employees']) ?></td>
                        <td><?= htmlspecialchars($row['date_received']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="month-total-row">
                        <td colspan="4" class="text-end">Total lună:</td>
                        <td><?= number_format($totalSuprafata, 2) ?></td>
                        <td></td>
                        <td><?= number_format($totalAchitata, 2) ?></td>
                        <td><?= number_format($totalRest, 2) ?></td>
                        <td><?= number_format($totalPret, 2) ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endforeach; ?>

                    </div>
                </div>
            </div>

            <?php include '../assets/components/footer.php' ?>
        </div>
    </div>
</main>

<?php include '../assets/components/off-canvas-design.php' ?>
<?php include '../assets/components/scripts.php' ?>
<?php include '../assets/components/script-datatables.php' ?>

<script>
$(document).ready(function() {
    $('.month-table').each(function() {
        $(this).DataTable({
            dom: 'lBfrtip',
            lengthMenu: [[10, 25, 50, -1],[10, 25, 50, "Toate"]],
            oLanguage: {
                sSearch: "Caută:",
                sLengthMenu: "Afișează _MENU_ rânduri",
                sInfo: "Afișate _START_–_END_ din _TOTAL_ proiecte",
                sZeroRecords: "Niciun proiect găsit",
                sInfoEmpty: "0 rezultate"
            },
            buttons: [
                { extend: 'copyHtml5', text: 'Copiere', className: 'btn btn-light btn-sm' },
                { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm' },
                { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-secondary btn-sm' },
                { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-danger btn-sm', orientation: 'landscape', pageSize: 'A4' }
            ],
            responsive: true
        });
    });
});
</script>
</body>
</html>

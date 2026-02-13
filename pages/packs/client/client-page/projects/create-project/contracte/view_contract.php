<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/crm/backend/db.php";
require_once __DIR__ . '/dompdf/autoload.php';
header('Content-Type: text/html; charset=utf-8');
use Dompdf\Dompdf;
use Dompdf\Options;

$slug = $_GET['slug'] ?? null;
if (!$slug) { die("❌ Lipsă ID contract."); }

$stmt = $conMain->prepare("
  SELECT c.*, p.title AS project_title
  FROM project_contracts c
  LEFT JOIN projects p ON c.project_id = p.id
  WHERE c.id = ?
");
$stmt->bind_param("i", $slug);
$stmt->execute();
$result = $stmt->get_result();
$contract = $result->fetch_assoc();
$stmt->close();
$conMain->close();

if (!$contract) { die("❌ Contract inexistent."); }

$fields = json_decode($contract['fields_json'], true) ?: [];
$dataContract = !empty($fields['Data'])
    ? htmlspecialchars(date('d.m.Y', strtotime($fields['Data'])))
    : date('d.m.Y');
$idContract = htmlspecialchars($contract['id']);

$html = "
<!DOCTYPE html>
<html lang='ro'>
<head>
<title>Contract #{$idContract}</title>
<meta charset='UTF-8'>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css'>
<link rel='stylesheet' href='https://www.studiospacedesign.com/crm/pages/packs/client/client-page/projects/create-project/contracte/style.css'>
</head>
<body>

<table style='width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:20px;'>
  <tr>
    <td style='width:50%; vertical-align:middle;'>
      <h2 class='h2-start'>
        CONTRACT
      </h2>
    </td>

    <td style='width: 30%; vertical-align:top; text-align:right;'>
      <img src='https://www.studiospacedesign.com/crm/pages/packs/client/client-page/projects/create-project/contracte/img-contract/logo-png.png'
           style='width:160px; margin-bottom:5px; display:block;'>

      <table style='width:255px; border-collapse:collapse; table-layout:fixed; margin-left:auto;'>
        <tr>
          <td class='text-right pe-2'>Nr.</td>
          <td class='caseta b-black'>{$idContract}</td>
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
          <td class='text-right pe-2'>din data</td>
          <td class='caseta b-black'>{$dataContract}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<div class='contract-section'>
  <p class='contract-section-title'>Încheiat de către și între:</p>
  <table class='contract-table'>
    <tr>
      <td class='label'>Societatea</td>
      <td class='value'><div class='contract-boxed'>Societate cu Răspundere Limitată<br>ARTSPACE.STUDIO</div></td>
    </tr>
    <tr>
      <td class='label'>cu sediul</td>
      <td class='value'><div class='contract-boxed'>MD-2075, strada Milescu Spătaru 47<br>mun. Chișinău, Republica Moldova</div></td>
    </tr>
    <tr>
      <td class='label'></td>
      <td class='label'></td>
    </tr>
    <tr>
      <td class='label'></td>
      <td class='label'></td>
    </tr>
    <tr>
      <td class='label'></td>
      <td class='label'></td>
    </tr>
    <tr>
      <td class='label'>Număr de ordine în registrul comerțului</td>
      <td class='value'><div class='contract-boxed'>nr.129991 din 22.02.2024</div></td>
    </tr>
    <tr>
      <td class='label'>Cod de identificare fiscală</td>
      <td class='value'><div class='contract-boxed'>1024600011069</div></td>
    </tr>
    <tr>
      <td class='label'>Cont</td>
      <td class='value'><div class='contract-boxed'>MD88AG000000022515935527</div></td>
    </tr>
    <tr>
      <td class='label'>Deschis la</td>
      <td class='value'><div class='contract-boxed'>22.02.2024</div></td>
    </tr>
    <tr>
      <td class='label'>Reprezentată de</td>
      <td class='value'><div class='contract-boxed'>Arnaut Sebastian</div></td>
    </tr>
  </table>
</div>

<p class='client-title'>
  denumită în continuare <span>„Prestatorul”</span>, pe de o parte și
</p>

<table class='client-table'>
  <tr>
    <td class='label'>Nume / Prenume</td>
    <td><div class='client-box'>Beșliu Petru</div></td>
  </tr>
  <tr>
    <td class='label'>Adresa email</td>
    <td><div class='client-box'>urtep_uilseb@libero.it</div></td>
  </tr>
  <tr>
    <td class='label'>Număr de identitate</td>
    <td><div class='client-box'>2009020000308</div></td>
  </tr>
</table>

<p class='client-title'>
  denumită în continuare <span>„Clientul”</span>, pe de altă parte.
</p>

<ul>";

foreach ($fields as $key => $value) {
  if (trim($value) !== '') {
    $label = ucwords(str_replace('_', ' ', htmlspecialchars($key)));
    $val = htmlspecialchars($value);
    $html .= "<li><strong>{$label}:</strong> {$val}</li>";
  }
}

$html .= "
</ul>
<hr>
<p style='text-align:center;color:#777;font-size:12px;'>
  Generat automat la ".date('d.m.Y H:i')."
</p>
</body>
</html>
";

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('contract-'.$idContract.'.pdf', ["Attachment" => false]);
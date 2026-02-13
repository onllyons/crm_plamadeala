<?php
   require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
   checkAuth();
   
   
function extractEmployeesMap($conMain) {
    $sql = "SELECT employees FROM projects";
    $res = mysqli_query($conMain, $sql);
    $map = [];

    while ($row = mysqli_fetch_assoc($res)) {
        $empStr = $row['employees'] ?? '';
        if (preg_match_all('/\[(\d+)\]\s*-\s*([^,]+)/u', $empStr, $m, PREG_SET_ORDER)) {
            foreach ($m as $mt) {
                $id = $mt[1];
                $name = trim($mt[2]);
                $name = preg_replace('/\s*\(.*?\)\s*/', '', $name);

                if (!isset($map[$id])) {
                    $map[$id] = $name;
                }
            }
        }
    }

    return $map;
}

   
   function getEmployeeIdsFromString($employees) {
       if (!$employees) return '';
       if (preg_match_all('/\[(\d+)\]/', $employees, $m)) {
           return implode(',', $m[1]);
       }
       return '';
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
   
   // Etapele definite în ordinea dorită
   $stages = [
       'Contractare',
       'Colectare informații',
       'Proiect tehnic',
       'Vizualizare 3D',
       'Întărire proiect',
       'Finisat',
       'Realizare mobilier'
   ];
   
   // Funcție pentru a obține proiectele după etapă
   function getProjectsByStage($conMain, $stage) {
       $sql = "SELECT id, client_id, title, date_received, date_technical, date_3d, date_deadline, employees
               FROM projects 
               WHERE stage = ? 
               ORDER BY date_deadline ASC";
       $stmt = mysqli_prepare($conMain, $sql);
       mysqli_stmt_bind_param($stmt, "s", $stage);
       mysqli_stmt_execute($stmt);
       $result = mysqli_stmt_get_result($stmt);
   
       $projects = [];
       while ($row = mysqli_fetch_assoc($result)) {
           $projects[] = $row;
       }
       mysqli_stmt_close($stmt);
       return $projects;
   }
   
   // Funcție pentru calculul culorii și al textului (zile rămase)
   function getProjectColorAndDays($date_received) {
       if (!$date_received) {
           return ['bg-secondary', 'Data necunoscută'];
       }
   
       $today = new DateTime();
       $start = new DateTime($date_received);
       $diff = $today->diff($start)->days; // diferența în zile calendaristice
       $remaining = 80 - $diff; // 60 zile lucrătoare ≈ 80 calendaristice
   
       if ($diff <= 20) {
           $color = 'bg-success text-white';
       } elseif ($diff <= 40) {
           $color = 'bg-warning text-dark';
       } elseif ($diff <= 60) {
           $color = 'bg-orange text-white';
       } else {
           $color = 'bg-danger text-white';
       }
   
       if ($remaining > 0) {
           $text = "(rămase $remaining zile)";
       } else {
           $text = "(întârziat cu " . abs($remaining) . " zile)";
       }
   
       return [$color, $text];
   }
   ?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
   <head>
      <?php include '../assets/components/links.php' ?>
      <style>
         .kanban-container {
         display: flex;
         gap: 0rem;
         overflow-x: auto;
         padding-bottom: 1rem;
         }
         .kanban-column {
         flex-shrink: 0;
         width: 12.6%;
         min-width: 11.87rem;
         }
         .kanban-column .card {
         border: none;
         }
         .tc{
         text-align: center;
         color: white;
         }
         .color-white{
         color: white!important;
         }
         .bg-orange { background-color: orange !important; }
         .kanban-column::-webkit-scrollbar { height: 8px; }
         .kanban-column::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 10px; }
         .bg-success {
         background-color: #00ca75d9 !important;
         }
         .bg-danger {
         background-color: #ff0f3b !important;
         }
         .bg-orange {
         background-color: #ff5722 !important;
         }
         .bg-warning {
         background-color: #FFC107 !important;
         }
         .floating-help-btn{
         position:fixed; left:20px; bottom:20px; z-index:1080;
         width:56px; height:56px; border-radius:50%;
         display:flex; align-items:center; justify-content:center;
         }
         .badge-orange{ background:orange; }
         .np:hover{
         color: black!important;
         }
      </style>
      <style>
         .btn-employee {
         border: 1px solid var(--falcon-border-color);
         border-radius: 20px;
         padding: 6px 14px;
         background: var(--falcon-card-bg);
         color: var(--falcon-heading-color);
         font-weight: 500;
         transition: all 0.2s ease;
         box-shadow: var(--falcon-box-shadow);
         }
         .btn-employee:hover {
         background: var(--falcon-gray-200);
         color: var(--falcon-heading-color);
         }
         .btn-check:checked + .btn-employee {
         background: linear-gradient(135deg, #007bff, #0056d2);
         color: #fff;
         border-color: transparent;
         box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
         }
         .btn-check:focus + .btn-employee {
         outline: none;
         box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
         }
      </style>
   </head>
   <body>
      <main class="main" id="top">
         <div class="container-fluid" data-layout="container">
            <?php include '../assets/components/nav-left.php' ?>
            <div class="content">
               <?php include '../assets/components/nav-top.php' ?>
               <div class="mb-3" id="notification_placeholder"></div>
               <div class="card shadow-sm">
                  <div class="card-body">
                     <h5 class="card-title mb-0">Board Statute</h5>
                     <?php
                        $workers = extractEmployeesMap($conMain);
                        if (!empty($workers)): ?>
                     <div class="mb-3">
                        <label class="form-label fw-semibold mt-0 mb-2">Filtrează după lucrător:</label>
                        <div class="filter-buttons d-flex flex-wrap gap-2">
                           <input type="checkbox" class="btn-check employee-filter" id="emp_all" value="" checked>
                           <label class="btn btn-employee" for="emp_all">Toți</label>
                           <?php foreach ($workers as $wid => $wname): ?>
                           <input type="checkbox" class="btn-check employee-filter" id="emp_<?=$wid?>" value="<?=htmlspecialchars($wid)?>">
                           <label class="btn btn-employee" for="emp_<?=$wid?>">
                           <?=htmlspecialchars($wname)?>
                           </label>
                           <?php endforeach; ?>
                        </div>
                     </div>
                     <?php endif; ?>
                     <div class="kanban-container">
                        <?php foreach ($stages as $stage): ?>
                        <div class="kanban-column border rounded-3 p-2 bg-light">
                           <h6 class="text-center fw-bold mb-3 text-primary"><?= htmlspecialchars($stage) ?></h6>
                           <div class="overflow-auto" style="max-height:80vh;">
                              <?php 
                                 $projects = getProjectsByStage($conMain, $stage);
                                 
                                 if (empty($projects)) {
                                     echo '<p class="text-muted small text-center">Niciun proiect</p>';
                                 } else {
                                     foreach ($projects as $p) {
                                 
                                         $slug = "?slug=" . urlencode($p['client_id']);
                                         $url = "/crm/pages/client-page.php" . $slug;
                                 
                                         [$color, $daysText] = getProjectColorAndDays($p['date_received']);
                                 
                                         $employeeIds = getEmployeeIdsFromString($p['employees']);
                                 
                                         echo '
                                         <div class="card mb-3 shadow-sm ' . $color . ' project-card" data-employee-ids="' . htmlspecialchars($employeeIds) . '">
                                             <div class="card-body p-2">
                                               
                                 
                                                 <h6 class="fw-semibold mb-1 tc">
                                                     <a href="' . $url . '" target="_blank" class="text-white text-decoration-none np">
                                                         ' . htmlspecialchars($p['title'] ?? '') . '
                                                     </a>
                                                 </h6>
                                 
                                 
                                                 <div class="small color-white">
                                                     D preluării: ' . htmlspecialchars($p['date_received'] ?? '-') . ' <br>
                                                     <span>' . $daysText . '</span>
                                                 </div>
                                 
                                                 <div style="display: none" class="small color-white">' . htmlspecialchars($p['employees'] ?? '-') . '</div>
                                 
                                                 <!-- 
                                                 <div class="d-flex flex-wrap gap-1 mt-2 small">
                                                     <span class="badge bg-warning text-dark">Tehnic: ' . htmlspecialchars($p['date_technical'] ?? '') . '</span>
                                                     <span class="badge" style="background-color:orange;">3D: ' . htmlspecialchars($p['date_3d'] ?? '') . '</span>
                                                     <span class="badge bg-danger">Deadline: ' . htmlspecialchars($p['employees'] ?? '') . '</span>
                                                 </div>
                                                 -->
                                             </div>
                                         </div>';
                                     }
                                 }
                                 ?>
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
      <button class="btn btn-primary shadow floating-help-btn" type="button"
         data-bs-toggle="modal" data-bs-target="#boardInfoModal" aria-label="Deschide explicații">
      <i class="fa-solid fa-info"></i>
      </button>
      <!-- Modal explicații board -->
      <div class="modal fade" id="boardInfoModal" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title">Explicație logică „Board proiecte”</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Închide"></button>
               </div>
               <div class="modal-body">
                  <h6 class="mb-2">Scop general</h6>
                  <p class="mb-3">
                     Board-ul afișează <strong>toate proiectele</strong>, grupate în coloane după câmpul <code>stage</code> din MySQL
                     (Contractare, Colectare informații, Proiect tehnic, Vizualizare 3D, Finalizat, Realizare mobilier).
                     În fiecare coloană, proiectele sunt sortate după <code>date_deadline ASC</code>.
                  </p>
                  <hr class="my-3">
                  <h6 class="mb-2">Durate &amp; repere</h6>
                  <ul class="mb-3">
                     <li>Total proiect: <strong>60 zile lucrătoare ≈ 80 zile calendaristice</strong>.</li>
                     <li>Fiecare etapă: <strong>20 zile lucrătoare</strong>.</li>
                     <li>Data de pornire: <code>date_received</code> (Data preluării proiectului).</li>
                  </ul>
                  <hr class="my-3">
                  <h6 class="mb-2">Culoarea cardurilor (după zile trecute de la <code>date_received</code>)</h6>
                  <div class="table-responsive">
                     <table class="table table-sm align-middle">
                        <thead>
                           <tr>
                              <th>Interval</th>
                              <th>Reper vizual</th>
                              <th>Culoare</th>
                              <th>Text</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>0–20 zile</td>
                              <td>Data preluării proiectului</td>
                              <td><span class="badge bg-success">verde</span></td>
                              <td>(rămase <em>X</em> zile)</td>
                           </tr>
                           <tr>
                              <td>21–40 zile</td>
                              <td>Schite tehnice (Proiect tehnic)</td>
                              <td><span class="badge bg-warning text-dark">galben</span></td>
                              <td>(rămase <em>X</em> zile)</td>
                           </tr>
                           <tr>
                              <td>41–60 zile</td>
                              <td>Proiect 3D (Vizualizare 3D)</td>
                              <td><span class="badge badge-orange text-dark">portocaliu</span></td>
                              <td>(rămase <em>X</em> zile)</td>
                           </tr>
                           <tr>
                              <td>&gt; 60 zile</td>
                              <td>Termen limită depășit</td>
                              <td><span class="badge bg-danger">roșu</span></td>
                              <td>(întârziat cu <em>X</em> zile)</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <hr class="my-3">
                  <h6 class="mb-2">Rezumat</h6>
                  <ul class="mb-0">
                     <li>Coloana este dată de <code>stage</code> (nu se schimbă automat).</li>
                     <li>Culoarea cardului reflectă progresul în timp față de <code>date_received</code>.</li>
                     <li>Se afișează și <em>(rămase X zile)</em> sau <em>(întârziat cu X zile)</em>.</li>
                  </ul>
               </div>
               <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">Închis</button>
               </div>
            </div>
         </div>
      </div>
      <?php include '../assets/components/off-canvas-design.php' ?>
      <?php include '../assets/components/scripts.php' ?>
   </body>
   <script>
      document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = Array.from(document.querySelectorAll('.employee-filter'));
        const cards = Array.from(document.querySelectorAll('.project-card'));
        const allBox = document.getElementById('emp_all');
      
        function update() {
          const selected = checkboxes
            .filter(cb => cb !== allBox && cb.checked)
            .map(cb => cb.value)
            .filter(Boolean);
          // dacă "Toți" e bifat sau nu e niciun checkbox bifat -> afișează tot
          if (allBox.checked || selected.length === 0) {
            cards.forEach(c => c.style.display = '');
            // păstrează toate unchecked (except all)
            return;
          }
          cards.forEach(card => {
            const ids = (card.dataset.employeeIds || '').split(',').filter(Boolean);
            const match = ids.some(id => selected.includes(id));
            card.style.display = match ? '' : 'none';
          });
        }
      
        // comportament "Toți"
        allBox.addEventListener('change', () => {
          if (allBox.checked) {
            checkboxes.forEach(cb => { if (cb !== allBox) cb.checked = false; });
          }
          update();
        });
      
        checkboxes.forEach(cb => {
          if (cb !== allBox) {
            cb.addEventListener('change', () => {
              if (cb.checked) allBox.checked = false;
              // dacă niciunul nu e bifat, reset la "Toți"
              const any = checkboxes.some(x => x !== allBox && x.checked);
              if (!any) allBox.checked = true;
              update();
            });
          }
        });
      
        // init
        allBox.checked = true;
        update();
      });
   </script>
</html>

<?php
   require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
   checkAuth();
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
   <head>
      <?php include '../assets/components/links.php' ?>
      <?php include '../assets/components/style-datatables.php' ?>

      <link rel="stylesheet" type="text/css" href="/crm/pages/packs/angajati/salarii/css.css">

   </head>
   <body>
      <main class="main" id="top">
         <div class="container" data-layout="container">
            <?php include '../assets/components/nav-left.php' ?>
            <div class="content">
               <?php include '../assets/components/nav-top.php' ?>
                     
                     <div class="card p-4 rounded-4 border-0 shadow-sm p-4">
                        <div class="row gy-4">
                            <div class="col-12">
                                <h4 class="mb-1 text-capitalize fw-bold">
                                    <span id="id_name"></span>
                                </h4>
                                <a href="mailto:victor.rotaru@example.com" id="client_email" class="fw-bold text-decoration-none text-primary">victor.rotaru@example.com</a>
                                <p id="id_email"></p>

                                <div class="row align-items-center mt-1">
                                  <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    <strong id="id_cell"></strong>
                                    <div class="text-muted small">Număr de telefon</div>
                                  </div>

                                  <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    <strong id="id_position"></strong>
                                    <div class="text-muted small">Funcție</div>
                                  </div>

                                  <?php if (isset($_SESSION["crm_user"]["level"]) && $_SESSION["crm_user"]["level"] === 0): ?>
  <div class="col-6 col-sm-4 col-md-3 col-lg-2">
    <strong id="id_pret_m2"></strong>
    <div class="text-muted small">Preț / m²</div>
  </div>

  <!-- Buton aliniat complet dreapta -->
  <div class="col text-end">
    <button class="form-cancel bg-1 ms-1 btn-end" id="btn-open-payment" style="min-width: 40px;">
      <i class="fa-solid fa-plus"></i>
    </button>
  </div>
<?php endif; ?>

                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="mt-1 mb-4">
                        <?php include 'packs/angajati/salarii/ui-ux.php'; ?>
                    </div>

                    
                    <div class="">
                      <h4 class="fw-bold mb-0">Proiecte asociate</h4>
                      <div id="projects_container" class="row g-3"></div>
                    </div>




                  
               <?php include '../assets/components/footer.php' ?>
            </div>
         </div>
      </main>
      <?php include '../assets/components/off-canvas-design.php' ?>
      <?php include '../assets/components/scripts.php' ?>
      <?php include '../assets/components/script-datatables.php' ?>
      <script>
        const USER_LEVEL = <?= isset($_SESSION["crm_user"]["level"]) ? (int)$_SESSION["crm_user"]["level"] : 0 ?>;

      </script>
      <script type="text/javascript" src="/crm/pages/packs/angajati/angajat-page/script.js"></script>

      <script type="text/javascript" src="/crm/pages/packs/angajati/salarii/salarii.js"></script>
   </body>
</html>

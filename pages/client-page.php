<?php
   require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
   checkAuth();
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
    <head>
        <?php include '../assets/components/links.php' ?>
        <?php include '../assets/components/style-datatables.php' ?>

        <link rel="stylesheet" type="text/css" href="/crm/pages/packs/client/client-page/style.css" />
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
                                    <span id="client_name"></span>
                                </h4>
                                <a href="" id="client_email" class="fw-bold text-decoration-none text-primary"></a>
                                <p id="client_address"></p>

                                <div class="row g-3 mt-1 d-flex flex-sm-wrap">
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                        <strong id="client_phone"></strong>
                                        <div class="text-muted small">Număr de telefon</div>
                                    </div>

                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                        <strong id="nr_identitate"></strong>
                                        <div class="text-muted small">IDNP:</div>
                                    </div>

                                    <?php if (($_SESSION["crm_user"]["level"] ?? 0) != 1): ?>
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 ms-auto">
                                        <div class="text-muted small full-end">
                                            <button class="form-cancel bg-1 ms-1 btn-end" data-bs-toggle="modal" data-bs-target="#new-project">
                                                <i class="fa-solid fa-folder-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <div id="projects"></div>


                    

                    <?php include '../assets/components/footer.php' ?>
                </div>
            </div>
        </main>

        <?php include 'packs/client/client-page/projects/create-project/create-project-modal.php'; ?>

        <?php include '../assets/components/off-canvas-design.php' ?>
        <?php include '../assets/components/scripts.php' ?>
        <?php include '../assets/components/script-datatables.php' ?>

        <script>
          const USER_LEVEL = <?= isset($_SESSION["crm_user"]["level"]) ? (int)$_SESSION["crm_user"]["level"] : 0 ?>;
        </script>


        <script type="text/javascript" src="/crm/pages/packs/client/client-page/projects/create-project/renderProjects.js"></script>
        <script type="text/javascript" src="/crm/pages/packs/client/client-page/script.js"></script>
    </body>
</html>

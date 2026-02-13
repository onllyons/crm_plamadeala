<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";

checkAuth()
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
    <?php include '../assets/components/links.php' ?>
    <link rel="stylesheet" type="text/css" href="/crm/pages/packs/settings/style.css">
    <link rel="stylesheet" type="text/css" href="/crm/pages/packs/angajati/salarii/css.css">
    <style type="text/css">
      .avatar-5xl {
    height: 10.5rem;
    width: 10.5rem;
}
    </style>
</head>
<body>
<main class="main" id="top">
    <div class="container" data-layout="container">
        <?php include '../assets/components/nav-left.php' ?>

        <div class="content">
            <?php include '../assets/components/nav-top.php' ?>


            <div class="card mb-3 shadow-sm" style="">
                <div class="card-header position-relative min-vh-25 mb-7">
                    <div class="bg-holder rounded-3 rounded-bottom-0" style="background-image:url(https://prium.github.io/falcon/v3.24.0/assets/img/generic/4.jpg);"></div>
                    <!--/.bg-holder-->

                    <div class="avatar avatar-5xl avatar-profile"><img class="rounded-circle img-thumbnail shadow-sm" src="/crm/assets/img/user.jpg" width="200" alt="" /></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <?php if (isAuth()): ?>
                            <h4 class="mb-1">
                                <?= htmlspecialchars($_SESSION["crm_user"]["name"]) ?>
                                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Verified"><small class="fa fa-check-circle text-primary" data-fa-transform="shrink-4 down-2"></small></span>
                            </h4>
                            <h5 class="fs-9 fw-normal"><?= htmlspecialchars($_SESSION["crm_user"]["email"]) ?></h5>
                            <p class="text-500"><?= htmlspecialchars($_SESSION["crm_user"]["phone"]) ?></p>
                            <div class="border-bottom border-dashed my-4 d-lg-none"></div>
                            <?php endif; ?>
                        </div>
                      
                    </div>
                </div>
            </div>

            <div class="mt-1 mb-4">
                <?php include 'packs/angajati/salarii/ui-ux.php'; ?>
            </div>

            




            


              <div class="col-lg-12 pe-lg-2">

                <div class="">
                  <h4 class="fw-bold mb-0">Proiecte asociate</h4>
                  <div id="projects_container" class="row g-3"></div>
                </div>

              </div>
            <br>






            <div class="row g-0">
            <div class="col-lg-12 pe-lg-2">
              <div class="card mb-3">
                <div class="card-header">
                  <h5 class="mb-0">Profile settings</h5>
                </div>
                <div class="card-body bg-body-tertiary">
                  <form class="row g-3" id="password-change-form" autocomplete="off">
                    <!-- <div class="col-lg-6">
                      <label class="form-label" for="first-name">First Name</label>
                      <input class="form-control" id="first-name" type="text" value="Anthony" />
                    </div>
                    <div class="col-lg-6">
                      <label class="form-label" for="last-name">Last Name</label>
                      <input class="form-control" id="last-name" type="text" value="Hopkins" />
                    </div> -->
                    <div class="col-lg-6">
                      <label class="form-label">Enter old password</label>
                      <input class="form-control" autocomplete="new-password" name="old-password" autocomplete="off" placeholder="Old password" type="Enter old password" />
                    </div>
                    <div class="col-lg-6">
                      <label class="form-label">Choose a new password</label>
                      <input class="form-control" autocomplete="new-password" name="password" type="password" autocomplete="off" placeholder="Choose a new password" />
                    </div>
                    <div class="col-lg-12">
                      <label class="form-label">Re-enter new password</label>
                      <input class="form-control" autocomplete="new-password" name="password-confirm" type="password" autocomplete="off" placeholder="Re-enter new password" />
                    </div>
                    
                    <div class="col-12 d-flex justify-content-end">
                      <button class="btn btn-primary" type="submit" name="submit">Update password</button>
                    </div>
                  </form>
                </div>
              </div>
           
            
            </div>
            
          </div>

            

            <?php include '../assets/components/footer.php' ?>
        </div>
    </div>
</main>
<?php include '../assets/components/off-canvas-design.php' ?>
<?php include '../assets/components/scripts.php' ?>

<?php

$name = $_SESSION["crm_user"]["name"] ?? "";

$employeeId = null;
if ($name) {
  $stmt = $conMain->prepare("SELECT id FROM angajati WHERE last_name_first_name = ?");
  $stmt->bind_param("s", $name);
  $stmt->execute();
  $stmt->bind_result($employeeId);
  $stmt->fetch();
  $stmt->close();
}
?>

<script type="text/javascript">
  const name = <?= json_encode($name) ?>;
  const employeeId = <?= json_encode($employeeId ?? 0) ?>;
  const globalLucratorId = employeeId;

</script>


<script src="/crm/pages/packs/settings/script.js"></script>
<script type="text/javascript" src="/crm/pages/packs/settings/proiecte-asociate/script.js"></script>
<script src="/crm/pages/packs/settings/salarii/salarii.js"></script>



</body>
</html>














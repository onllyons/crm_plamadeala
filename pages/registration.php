<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
    checkAuth();

    if (!isset($_SESSION['crm_user']) || (int)$_SESSION['crm_user']['level'] !== 0) {
        header("Location: /crm/pages/index.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
    <?php include '../assets/components/links.php' ?>
    <?php include '../assets/components/style-datatables.php' ?>
</head>
<body>
<main class="main" id="top">
    <div class="container" data-layout="container">
        <?php include '../assets/components/nav-left.php' ?>

        <div class="content">
            <?php include '../assets/components/nav-top.php' ?>

            <div class="card h-md-100 ecommerce-card-min-width">
                <div class="card-body d-flex flex-column justify-content-end">
                    <h1 class="h1-title">Register new user in CRM</h1>
                    <p class="text-muted">
                        This page allows you to register new users into the CRM platform. Once registered, users will have access to the CRM system, where they can view and manage relevant business information. Please make sure to enter the correct details to grant access to authorized users.
                    </p>
                    <form id="register-form" autocomplete="off">
                      <div class="row gx-2">
                        <div class="mb-3 col-sm-6">
                          <label class="form-label" for="name">Name and surname</label>
                          <input class="form-control" id="name" name="name" type="text" placeholder="Ex: elijah kajar" />
                        </div>
                        <div class="mb-3 col-sm-6">
                          <label class="form-label" for="phone">Phone</label>
                          <input class="form-control" id="phone" name="phone" type="tel" placeholder="Ex: +123456789" />
                        </div>
                      </div>

                      <div class="row gx-2">
                        <div class="mb-3 col-sm-6">
                          <label class="form-label" for="username">Login</label>
                          <input autocomplete="new-password" class="form-control" id="username" name="username" placeholder="Ex: elijah_kajar_2025" />
                        </div>
                        <div class="mb-3 col-sm-6">
                          <label class="form-label" for="email">E-mail</label>
                          <input autocomplete="new-password" class="form-control" id="email" name="email" type="email" placeholder="Ex: elijah_kajar@gmail.com"/>
                        </div>
                      </div>

                      <div class="row gx-2">
                          <div class="mb-3 col-12">
                            <label class="form-label" for="level">Account type, permission, level type</label>
                            <select class="form-select" name="level" id="level">
                              <option value="0">Administrator</option>
                              <option value="1">Employed</option>
                            </select>
                          </div>
                      </div>


                      <div class="row gx-2">
                          <div class="mb-3 col-sm-6">
                              <label class="form-label" for="password">Password</label>
                              <input autocomplete="new-password" class="form-control" id="password" name="password" type="password" placeholder="********" />
                          </div>
                          <div class="mb-3 col-sm-6">
                              <label class="form-label" for="password-confirm">Confirm password</label>
                              <input autocomplete="new-password" class="form-control" id="password-confirm" name="password-confirm" type="password" placeholder="********" />
                          </div>
                      </div>
                      <small class="form-text text-muted">Your password should be at least 8 characters long and contain a mix of letters, numbers, and special characters for security. Weak passwords may trigger security alerts from browsers.</small>

                      <div class="mb-3">
                        <button class="btn btn-primary d-block w-100 mt-3" type="submit">Create account</button>
                      </div>
                    </form>


                    <hr class="my-4" />
                    <h4 class="mb-3">Registered users</h4>
                    <table id="users-table" class="table display dataTable no-footer dtr-inline" style="width: 100%;">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Username</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Role</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>





                </div>
            </div>

            <?php include '../assets/components/footer.php' ?>
        </div>
    </div>
</main>
<?php include '../assets/components/off-canvas-design.php' ?>
<?php include '../assets/components/scripts.php' ?>
<?php include '../assets/components/script-datatables.php' ?>

<script src="/crm/pages/packs/registration/script.js"></script>

</body>
</html>

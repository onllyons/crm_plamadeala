<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
if (isset($_SESSION["crm_user"])) {
    header("Location: /crm/pages/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr">
    <head>
        <?php include '../assets/components/links.php' ?>
    </head>
    <body>
        <main class="main" id="top">
            <div class="container-fluid">
                <script>
                    var isFluid = JSON.parse(localStorage.getItem("isFluid"));
                    if (isFluid) {
                        var container = document.querySelector("[data-layout]");
                        container.classList.remove("container");
                        container.classList.add("container-fluid");
                    }
                </script>
                <div class="row min-vh-100 bg-100">
                    <div class="col-6 d-none d-lg-block position-relative">
                        <div class="bg-holder" style="background-image: url(../assets/new_image/login.png); background-position: 50% 70%;"></div>
                        <!--/.bg-holder-->
                    </div>
                    <div class="col-sm-10 col-md-6 px-sm-0 align-self-center mx-auto py-5">
                        <div class="row justify-content-center g-0">
                            <div class="col-lg-9 col-xl-8 col-xxl-6">
                                <div class="card">
                                    <div class="card-header bg-circle-shape bg-shape text-center p-2">
                                        <a class="font-sans-serif fw-bolder fs-3 z-1 position-relative link-light" style="z-index: 9;" href="#" data-bs-theme="light">
                                            Admin panel
                                        </a>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row flex-between-center">
                                            <div class="col-auto">
                                                <h3>Authentication</h3>
                                            </div>
                                        </div>
                                        <form id="login-form" novalidate>
                                            <div class="mb-3">
                                                <label class="form-label" for="split-login-email">Username or email</label>
                                                <input class="form-control" name="email" id="split-login-email" type="email" />
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <label class="form-label" for="split-login-password">Password</label>
                                                </div>
                                                <input class="form-control" name="password" id="split-login-password" type="password" />
                                            </div>
                                            <div class="row flex-between-center">
                                                <div class="col-auto">
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input" name="remember-me" type="checkbox" id="split-checkbox" />
                                                        <label class="form-check-label mb-0" for="split-checkbox">remember me</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn btn-primary d-block w-100 mt-3" type="submit" name="submit">
                                                    Access Dashboard
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
      

        <?php include '../assets/components/off-canvas-design.php' ?>
        <?php include '../assets/components/scripts.php' ?>

        <script src="/crm/pages/packs/login/script.js"></script>
    </body>
</html>

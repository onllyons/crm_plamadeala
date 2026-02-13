<?php
$user_level = isset($_SESSION["crm_user"]["level"]) ? (int)$_SESSION["crm_user"]["level"] : 0;
$isAdmin = ($user_level === 0);
?>


<script>
    var isFluid = JSON.parse(localStorage.getItem("isFluid"));
    if (isFluid) {
        var container = document.querySelector("[data-layout]");
        container.classList.remove("container");
        container.classList.add("container-fluid");
    }
</script>
<nav class="navbar navbar-light navbar-vertical navbar-expand-xl">
    <script>
        var navbarStyle = localStorage.getItem("navbarStyle");
        if (navbarStyle && navbarStyle !== "transparent") {
            document.querySelector(".navbar-vertical").classList.add(`navbar-${navbarStyle}`);
        }
    </script>
    <div class="d-flex align-items-center">
        <div class="toggle-icon-wrapper">
            <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Toggle Navigation">
                <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
            </button>
        </div>
        <a class="navbar-brand" href="/crm/pages/index.php">
            <div class="d-flex align-items-center py-3 logo-name">
                <!-- <img class="me-2" src="/crm/assets/img/favicons/logo.png" alt="" width="40" /> -->
                <span class="font-sans-serif name-sp">Admin System</span>
            </div>
        </a>
    </div>
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content scrollbar">
            <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">
                <li class="nav-item">
                    <a class="nav-link dropdown-indicator" href="#dashboard" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="dashboard">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon"><span class="fas fa-flag"></span></span><span class="nav-link-text ps-1">Dashboard</span>
                        </div>
                    </a>
                    <ul class="nav collapse show" id="dashboard">



                        <li class="nav-item">
                            <a class="nav-link" href="/crm/pages/index.php">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Cartela Client</span></div>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/crm/pages/angajati.php">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Cartela angajat</span></div>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/crm/pages/board.php">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Board Statute</span></div>
                            </a>
                        </li>

<?php if ($user_level === 0): ?>
<li class="nav-item">
    <a class="nav-link" href="/crm/pages/projects.php">
        <div class="d-flex align-items-center">
            <span class="nav-link-text ps-1">Board Proiecte</span>
        </div>
    </a>
</li>
<?php endif; ?>




                 <!--        <li class="nav-item">
                            <a class="nav-link" href="/crm/pages/calendar.php">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Calendar</span></div>
                            </a>
                        </li>

 -->
















                        



<?php if ($isAdmin): ?>
<li class="nav-item">
    <a class="nav-link" href="/crm/pages/packs/generate-blog-pages/view-cards/view-blog.php">
        <div class="d-flex align-items-center">
            <span class="nav-link-text ps-1">Blog</span>
            <span class="badge rounded-pill ms-2 badge-subtle-success">New</span>
        </div>
    </a>
</li>
<?php endif; ?>


                        <li class="nav-item">
                            <a class="nav-link" href="/crm/pages/drive.php">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Drive</span></div>
                            </a>
                        </li>

                   
                     
                        
                    </ul>
                </li>


                <?php
                $user_level = isset($_SESSION["crm_user"]["level"]) ? (int)$_SESSION["crm_user"]["level"] : 0;

                // Presupunem că nivelul 1 este pentru admin
                $canSeeAdminMenu = $user_level === 0;
                ?>

                <?php if ($canSeeAdminMenu): ?>
                    <li class="nav-item">
                        <a class="nav-link dropdown-indicator" href="#administrator" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="administrator">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span class="fas fa-lock"></span></span>
                                <span class="nav-link-text ps-1">Administrator</span>
                            </div>
                        </a>
                        <ul class="nav collapse show" id="administrator">
                            <li class="nav-item">
                                <a class="nav-link" href="/crm/pages/registration.php">
                                    <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Înregistrarea profilului</span></div>
                                </a>
                            </li>
                      <!--       <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Server info</span></div>
                                </a>
                            </li> -->
                        </ul>
                    </li>
                <?php endif; ?>

              

                
            </ul> 
        </div>
    </div>
</nav>




























<nav class="navbar navbar-light navbar-glass navbar-top navbar-expand-lg">
   <button
      class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarVerticalCollapse"
      aria-controls="navbarVerticalCollapse"
      aria-expanded="false"
      aria-label="Toggle Navigation"
      >
   <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
   </button>
   <a class="navbar-brand me-1 me-sm-3" href="#">
      <div class="d-flex align-items-center"><span class="font-sans-serif"></span></div>
   </a>
   <ul class="navbar-nav align-items-center d-none d-lg-block">
      <li class="nav-item">
         <div class="search-box" data-list='{"valueNames":["title"]}'>
            <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
               <input class="form-control search-input fuzzy-search" type="search" placeholder="Search..." aria-label="Search" />
               <span class="fas fa-search search-box-icon"></span>
            </form>
            <div class="btn-close-falcon-container position-absolute end-0 top-50 translate-middle shadow-none" data-bs-dismiss="search">
               <button class="btn btn-link btn-close-falcon p-0" aria-label="Close"></button>
            </div>
            <div class="dropdown-menu border font-base start-0 mt-2 py-0 overflow-hidden w-100">
               <div class="scrollbar list py-3" style="max-height: 24rem;">
                  <h6 class="dropdown-header fw-medium text-uppercase px-x1 fs-11 pt-0 pb-2">Members</h6>
                  <a class="dropdown-item px-x1 py-2" href="/crm/pages/settings.php">
                     <div class="d-flex align-items-center">
                        <div class="avatar avatar-l status-online me-2">
                           <img class="rounded-circle" src="https://prium.github.io/falcon/v3.24.0/assets/img/team/1.jpg" alt="" />
                        </div>
                        <div class="flex-1">
                           <h6 class="mb-0 title">Anna Karinina</h6>
                           <p class="fs-11 mb-0 d-flex">Technext Limited</p>
                        </div>
                     </div>
                  </a>
                  <a class="dropdown-item px-x1 py-2" href="/crm/pages/settings.php">
                     <div class="d-flex align-items-center">
                        <div class="avatar avatar-l me-2">
                           <img class="rounded-circle" src="https://prium.github.io/falcon/v3.24.0/assets/img/team/2.jpg" alt="" />
                        </div>
                        <div class="flex-1">
                           <h6 class="mb-0 title">Antony Hopkins</h6>
                           <p class="fs-11 mb-0 d-flex">Brain Trust</p>
                        </div>
                     </div>
                  </a>
                  <a class="dropdown-item px-x1 py-2" href="/crm/pages/settings.php">
                     <div class="d-flex align-items-center">
                        <div class="avatar avatar-l me-2">
                           <img class="rounded-circle" src="https://prium.github.io/falcon/v3.24.0/assets/img/team/3.jpg" alt="" />
                        </div>
                        <div class="flex-1">
                           <h6 class="mb-0 title">Emma Watson</h6>
                           <p class="fs-11 mb-0 d-flex">Google</p>
                        </div>
                     </div>
                  </a>
               </div>
               <div class="text-center mt-n3">
                  <p class="fallback fw-bold fs-8 d-none">No Result Found.</p>
               </div>
            </div>
         </div>
      </li>
   </ul>
   <div class="collapse navbar-collapse scrollbar" id="navbarStandard">
      <ul class="navbar-nav" data-top-nav-dropdowns="data-top-nav-dropdowns">
         <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dashboards">
               Altele
            </a>
            <div class="dropdown-menu dropdown-caret dropdown-menu-card border-0 mt-0" aria-labelledby="dashboards">
                <div class="bg-white dark__bg-1000 rounded-3 py-2">
                    <a class="dropdown-item link-600 fw-medium" href="/crm/pages/q-altele/fisiere-proiect.php">Fisiere proiect</a>
                </div>
            </div>
            </li>
            
           <!--  <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dashboards">
                Menu #2
            </a>
            <div class="dropdown-menu dropdown-caret dropdown-menu-card border-0 mt-0" aria-labelledby="dashboards">
                <div class="bg-white dark__bg-1000 rounded-3 py-2">
                    <a class="dropdown-item link-600 fw-medium" href="#">menu 1</a>
                </div>
            </div>
            </li> -->
      </ul>
   </div>
   <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
      <li class="nav-item">
         <div class="theme-control-toggle fa-icon-wait px-2">
            <input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="theme" value="dark" />
            <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch to light theme"><span class="fas fa-sun fs-0"></span></label>
            <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch to dark theme"><span class="fas fa-moon fs-0"></span></label>
         </div>
      </li>
      <!-- <li class="nav-item dropdown">
         <a class="nav-link notification-indicator notification-indicator-primary px-0 fa-icon-wait" id="navbarDropdownNotification" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll"><span class="fas fa-bell" data-fa-transform="shrink-6" style="font-size: 33px;"></span></a>
         <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg" aria-labelledby="navbarDropdownNotification">
            <div class="card card-notification shadow-none">
               <div class="card-header">
                  <div class="row justify-content-between align-items-center">
                     <div class="col-auto">
                        <h6 class="card-header-title mb-0">Notifications</h6>
                     </div>
                     <div class="col-auto ps-0 ps-sm-3"><a class="card-link fw-normal" href="#">Mark all as read</a></div>
                  </div>
               </div>
               <div class="scrollbar-overlay" style="max-height:19rem">
                  <div class="list-group list-group-flush fw-normal fs-10">
                     <div class="list-group-title border-bottom">NEW</div>
                     <div class="list-group-item">
                        <a class="notification notification-flush notification-unread" href="#">
                           <div class="notification-avatar">
                              <div class="avatar avatar-2xl me-3">
                                 <img class="rounded-circle" src="https://prium.github.io/falcon/v3.24.0/assets/img/team/1-thumb.png" alt="" />
                              </div>
                           </div>
                           <div class="notification-body">
                              <p class="mb-1"><strong>Emma Watson</strong> replied to your comment : "Hello world 😍"</p>
                              <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">💬</span>Just now</span>
                           </div>
                        </a>
                     </div>
                     <div class="list-group-item">
                        <a class="notification notification-flush notification-unread" href="#">
                           <div class="notification-avatar">
                              <div class="avatar avatar-2xl me-3">
                                 <div class="avatar-name rounded-circle"><span>AB</span></div>
                              </div>
                           </div>
                           <div class="notification-body">
                              <p class="mb-1"><strong>Albert Brooks</strong> reacted to <strong>Mia Khalifa's</strong> status</p>
                              <span class="notification-time"><span class="me-2 fab fa-gratipay text-danger"></span>9hr</span>
                           </div>
                        </a>
                     </div>
                     <div class="list-group-title border-bottom">EARLIER</div>
                     <div class="list-group-item">
                        <a class="notification notification-flush" href="#">
                           <div class="notification-avatar">
                              <div class="avatar avatar-2xl me-3">
                                 <img class="rounded-circle" src="https://prium.github.io/falcon/v3.24.0/assets/img/icons/weather-sm.jpg" alt="" />
                              </div>
                           </div>
                           <div class="notification-body">
                              <p class="mb-1">The forecast today shows a low of 20&#8451; in California. See today's weather.</p>
                              <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">🌤️</span>1d</span>
                           </div>
                        </a>
                     </div>
                     <div class="list-group-item">
                        <a class="border-bottom-0 notification-unread  notification notification-flush" href="#">
                           <div class="notification-avatar">
                              <div class="avatar avatar-xl me-3">
                                 <img class="rounded-circle" src="https://prium.github.io/falcon/v3.24.0/assets/img/logos/oxford.png" alt="" />
                              </div>
                           </div>
                           <div class="notification-body">
                              <p class="mb-1"><strong>University of Oxford</strong> created an event : "Causal Inference Hilary 2019"</p>
                              <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">✌️</span>1w</span>
                           </div>
                        </a>
                     </div>
                     <div class="list-group-item">
                        <a class="border-bottom-0 notification notification-flush" href="#">
                           <div class="notification-avatar">
                              <div class="avatar avatar-xl me-3">
                                 <img class="rounded-circle" src="https://prium.github.io/falcon/v3.24.0/assets/img/team/10.jpg" alt="" />
                              </div>
                           </div>
                           <div class="notification-body">
                              <p class="mb-1"><strong>James Cameron</strong> invited to join the group: United Nations International Children's Fund</p>
                              <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">🙋‍</span>2d</span>
                           </div>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="card-footer text-center border-top"><a class="card-link d-block" href="#">View all</a></div>
            </div>
         </div>
      </li> -->
      <!-- <li class="nav-item dropdown px-1">
         <a class="nav-link fa-icon-wait nine-dots p-1" id="navbarDropdownMenu" role="button" data-hide-on-body-scroll="data-hide-on-body-scroll" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="43" viewBox="0 0 16 16" fill="none">
               <circle cx="2" cy="2" r="2" fill="#6C6E71"></circle>
               <circle cx="2" cy="8" r="2" fill="#6C6E71"></circle>
               <circle cx="2" cy="14" r="2" fill="#6C6E71"></circle>
               <circle cx="8" cy="8" r="2" fill="#6C6E71"></circle>
               <circle cx="8" cy="14" r="2" fill="#6C6E71"></circle>
               <circle cx="14" cy="8" r="2" fill="#6C6E71"></circle>
               <circle cx="14" cy="14" r="2" fill="#6C6E71"></circle>
               <circle cx="8" cy="2" r="2" fill="#6C6E71"></circle>
               <circle cx="14" cy="2" r="2" fill="#6C6E71"></circle>
            </svg>
         </a>
         <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-caret-bg" aria-labelledby="navbarDropdownMenu">
            <div class="card shadow-none">
               <div class="scrollbar-overlay nine-dots-dropdown">
                  <div class="card-body px-3">
                     <div class="row text-center gx-0 gy-0">
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <div class="avatar avatar-2xl"> <img class="rounded-circle" src="" alt="" /></div>
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11">Account</p>
                           </a>
                        </div>
                        
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="https://mailbluster.com/" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/mailbluster.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Mailbluster</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/google.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Google</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/spotify.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Spotify</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/steam.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Steam</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/github-light.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Github</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/discord.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Discord</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/xbox.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">xbox</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/trello.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Kanban</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/hp.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Hp</p>
                           </a>
                        </div>
                        <div class="col-12">
                           <hr class="my-3 mx-n3 bg-200" />
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/linkedin.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Linkedin</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/twitter.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Twitter</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/facebook.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Facebook</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/instagram.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Instagram</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/pinterest.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Pinterest</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/slack.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Slack</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <img class="rounded" src="https://prium.github.io/falcon/v3.24.0/assets/img/nav-icons/deviantart.png" alt="" width="40" height="40" />
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11 pt-1">Deviantart</p>
                           </a>
                        </div>
                        <div class="col-4">
                           <a class="d-block hover-bg-200 px-2 py-3 rounded-3 text-center text-decoration-none" href="#" target="_blank">
                              <div class="avatar avatar-2xl">
                                 <div class="avatar-name rounded-circle bg-primary-subtle text-primary"><span class="fs-7">E</span></div>
                              </div>
                              <p class="mb-0 fw-medium text-800 text-truncate fs-11">Events</p>
                           </a>
                        </div>
                        <div class="col-12"><a class="btn btn-outline-primary btn-sm mt-4" href="#">Show more</a></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </li> -->
      <li class="nav-item dropdown">
         <?php if (isAuth()): ?>
         <a class="nav-link pe-0 ps-2 d-flex avatar-set-name" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="avatar avatar-xl">
               <img class="rounded-circle" src="/crm/assets/img/user.jpg" alt="photo user" />
            </div>
            <div class="SESSION_NamePrename text-1100">
               <?= $_SESSION["crm_user"]["name"] ?? "" ?>
            </div>
         </a>
         <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
            <div class="bg-white dark__bg-1000 rounded-2 py-2">
               <a class="dropdown-item" href="/crm/pages/settings.php">Profile & account</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="/crm/pages/logout.php">Logout</a>
            </div>
         </div>
         <?php else: ?>
         <a class="nav-link pe-0 ps-2 d-flex avatar-set-name" href="/crm/pages/login.php">
            <div class="SESSION_NamePrename text-1100">
               Login
            </div>
         </a>
         <?php endif; ?>
      </li>
   </ul>
</nav>
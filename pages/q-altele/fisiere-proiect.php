<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
checkAuth();
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
  <head>
    <?php include '../../assets/components/links.php' ?>
    <style>
      .file-card {
        background: #fff;
        border-radius: 1.2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: all .25s ease;
        border: 1px solid #f0f0f0;
      }
      .file-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(0,0,0,0.08);
      }
      .file-thumb {
        height: 150px;
        width: 100%;
        border-radius: 0.8rem;
        object-fit: cover;
        background: #f9f9f9;
      }
      .file-icon {
        font-size: 3rem;
        color: #0d6efd;
      }
      .search-box {
        max-width: 300px;
      }

      .file-name {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        font-size: 0.9rem;
      }


    </style>
  </head>
  <body>
    <main class="main" id="top">
      <div class="container" data-layout="container">
        <?php include '../../assets/components/nav-left.php' ?>
        <div class="content">
          <?php include '../../assets/components/nav-top.php' ?>

          <div class="mt-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h1 class="h4 mb-0"><i class="fas fa-folder-open me-2 text-primary"></i>Fișiere</h1>
              <input autocomplete="off" id="search" type="text" class="form-control search-box" placeholder="Caută fișiere...">
            </div>

            <div id="filesContainer" class="row g-3"></div>

            <div class="text-center mt-4">
              <button id="loadMore" class="btn btn-outline-primary rounded-pill px-4">Încarcă mai multe</button>
            </div>
          </div>

          <?php include '../../assets/components/footer.php' ?>
        </div>
      </div>
    </main>

    <?php include '../../assets/components/off-canvas-design.php' ?>
    <?php include '../../assets/components/scripts.php' ?>

    <script>
      let page = 1;
      const container_file = document.getElementById("filesContainer");
      const searchInput = document.getElementById("search");
      const loadBtn = document.getElementById("loadMore");

      function loadFiles(reset = false) {
        const search = searchInput.value.trim();
        fetch(`/crm/pages/q-altele/fisiere/server-files.php?page=${page}&search=${encodeURIComponent(search)}`)
          .then(res => res.json())
          .then(data => {
            if (reset) container_file.innerHTML = "";
            if (!data.success) return;
            data.files.forEach(f => {
              container_file.innerHTML += `
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                  <div class="file-card p-3 h-100 d-flex flex-column">
                    ${f.thumb}
                    <div class="mt-3">
                     <h6 class="fw-bold text-primary mb-1 file-name" title="${f.file_name}">
                       ${f.file_name}
                     </h6>
                      <div class="small text-muted mb-2">
                        <i class="fas fa-user me-1"></i>${f.username} &nbsp; | &nbsp;
                        <i class="fas fa-clock me-1"></i>${f.created_at}
                      </div>
                      <a href="${f.file_url}" target="_blank" class="btn btn-sm btn-light border w-100">
                        <i class="fas fa-download me-1"></i> Deschide
                      </a>
                    </div>
                  </div>
                </div>`;
            });
            loadBtn.style.display = data.has_more ? "inline-block" : "none";
          });
      }

      document.addEventListener("DOMContentLoaded", () => {
        loadFiles();
        loadBtn.addEventListener("click", () => { page++; loadFiles(); });
        searchInput.addEventListener("input", () => { page = 1; loadFiles(true); });
      });
    </script>
  </body>
</html>

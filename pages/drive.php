<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
checkAuth();
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
  <?php include '../assets/components/links.php' ?>
  <link rel="stylesheet" type="text/css" href="/crm/pages/packs/drive/style.css" />
</head>
<body>
<main class="main" id="top">
  <div class="container" data-layout="container">
    <?php include '../assets/components/nav-left.php' ?>

    <div class="content">
      <?php include '../assets/components/nav-top.php' ?>

      <div class="card mb-3 mb-lg-0">
        <div class="card-header">
          <h5 class="mb-0 h5-title">Drive</h5>
        </div>
        <div class="card-body bg-body-tertiary">
          <a class="mb-4 d-block d-flex align-items-center" href="#upload-form" data-bs-toggle="collapse" aria-expanded="false" aria-controls="upload-form">
            <span class="circle-dashed"><span class="fas fa-plus"></span></span>
            <span class="ms-3">Add document</span>
          </a>

          <div class="collapse" id="upload-form">
            <form id="uploadForm" enctype="multipart/form-data" class="row g-3">
              <div class="col-md-6 col-lg-4">
                <label for="titlu" class="form-label fw-semibold">Document title</label>
                <input autocomplete="off" type="text" name="titlu" id="titlu" class="form-control" required />
              </div>

              <div class="col-md-6 col-lg-4">
                <label for="file" class="form-label fw-semibold">Select file</label>
                <input type="file" name="file" id="file" class="form-control" required />
              </div>

              <div class="col-12">
                <button class="form-accept btn-aufrage" type="submit">
                  <span>Save the document</span>
                </button>
              </div>

              <div class="progress mb-3" style="height:15px" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" id="progressBar" style="width: 0%">0%</div>
              </div>

              <div class="col-12 mt-1">
                <small class="text-muted d-block">
                  🔒 The uploaded documents are securely saved and are visible exclusively to you.
                </small>
              </div>
            </form>

            <div class="border-dashed-bottom my-3"></div>
          </div>

          <div id="resultList"></div>
        </div>
      </div>

      <?php include '../assets/components/footer.php' ?>
    </div>
  </div>
</main>

<?php include '../assets/components/off-canvas-design.php' ?>
<?php include '../assets/components/scripts.php' ?>

<script>
const resultList = document.getElementById("resultList");

function fetchData() {
  fetch(`/crm/pages/packs/drive/ajax/get.php`)
    .then(res => res.json())
    .then(data => {
      resultList.innerHTML = "";

      if (data.length === 0) {
        resultList.innerHTML = "<p>There are no files uploaded.</p>";
        return;
      }

      data.forEach(item => {
        const div = document.createElement("div");
        div.classList.add("d-flex", "mb-4");

        const initials = item.titlu.trim().substring(0, 2).toUpperCase();

        div.innerHTML = `
          <a href="/crm/pages/packs/drive/file/${item.file}" target="_blank">
            <div class="avatar avatar-3xl">
              <div class="avatar-name rounded-circle"><span>${initials}</span></div>
            </div>
          </a>
          <div class="flex-1 position-relative ps-3">
            <h6 class="fs-9 mb-0">
              <a href="/crm/pages/packs/drive/file/${item.file}" target="_blank">${item.titlu}</a>
            </h6>
            <p class="mb-1">${item.file}</p>
            <p class="text-1000 mb-0">Added on: ${item.data_adaugarii}</p>
            <button class="btn btn-sm mt-1 deleteBtn" data-id="${item.id}">Delete</button>
          </div>
        `;
        resultList.appendChild(div);
      });

      document.querySelectorAll(".deleteBtn").forEach(btn => {
        btn.addEventListener("click", function () {
          if (!confirm("Are you sure you want to delete?")) return;
          const id = this.dataset.id;

          fetch(`/crm/pages/packs/drive/ajax/delete.php`, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${id}`
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              this.closest(".d-flex.mb-4").remove();
              showToast("The file has been deleted.", "success");
            } else {
              showToast("Eroare: " + data.message, "error");
            }
          });
        });
      });
    })
    .catch(err => {
      console.error("Eroare fetch:", err);
      alert("err");
    });
}

document.querySelector('input[name="file"]').addEventListener("change", function () {
  const fileInput = this;
  const titleInput = document.querySelector('input[name="titlu"]');

  if (fileInput.files.length > 0) {
    const fileName = fileInput.files[0].name;
    const nameWithoutExt = fileName.replace(/\.[^/.]+$/, "");
    let niceTitle = nameWithoutExt.replace(/[-_]+/g, ' ').replace(/\s+/g, ' ').trim();
    niceTitle = niceTitle.replace(/\b\w/g, char => char.toUpperCase());
    titleInput.value = niceTitle;
  }
});

document.getElementById("uploadForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  const progressBar = document.getElementById("progressBar");

  const xhr = new XMLHttpRequest();

  xhr.upload.addEventListener("progress", function (e) {
    if (e.lengthComputable) {
      const percent = Math.round((e.loaded / e.total) * 100);
      progressBar.style.width = percent + "%";
      progressBar.innerText = percent + "%";
      progressBar.setAttribute("aria-valuenow", percent);
    }
  });

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      const res = JSON.parse(xhr.responseText);
      showToast(res.success ? "✅ succes!" : "Eroare: " + res.message, res.success ? "success" : "error");

      if (res.success) {
        document.getElementById("uploadForm").reset();
        fetchData();
      }

      // Resetăm progress bar-ul după upload
      progressBar.style.width = "0%";
      progressBar.innerText = "0%";
      progressBar.setAttribute("aria-valuenow", 0);
    }
  };

  xhr.open("POST", "/crm/pages/packs/drive/ajax/upload.php");
  xhr.send(formData);
});


fetchData();
</script>
</body>
</html>

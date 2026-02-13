function renderProjects(projects) {
    const container = $("#projects");
    container.empty();

    if (!projects.length) {
        container.html('<p class="text-muted text-center py-5">Niciun proiect găsit</p>');
        return;
    }

    projects.forEach((p) => {
        const htmlId = `project-${p.id}`;
        const dbId = p.id;
        const projectId = `project-${p.id}`;
        const employeesList = p.employees
            ? p.employees
                  .split(",")
                  .map((e) => e.trim())
                  .filter(Boolean)
            : [];

        // 🔹 HTML pentru fiecare lucrător individual
        const employeeCards = employeesList.length
            ? employeesList
                  .map(
                      (name) => `
          <div class="employee-chip me-2 mb-2 d-inline-flex align-items-center px-3 py-1 rounded-pill bg-light border">
            <i class="fas fa-user-circle me-2 text-secondary" style="font-size:1.4rem;"></i>
            <span class="fw-medium">${name}</span>
          </div>
        `
                  )
                  .join("")
            : `<p class="text-muted mb-0 text-center">Nu există lucrători asociați momentan.</p>`;

        function formatMD(dateString) {
            if (!dateString) return "-";
            const d = new Date(dateString);
            if (isNaN(d)) return "-";
            const day = String(d.getDate()).padStart(2, "0");
            const month = String(d.getMonth() + 1).padStart(2, "0");
            const year = d.getFullYear();
            return `${day}.${month}.${year}`;
        }

        function daysRemaining(dateString) {
            if (!dateString) return "";
            const today = new Date();
            const target = new Date(dateString);
            if (isNaN(target)) return "";
            const diff = Math.ceil((target - today) / (1000 * 60 * 60 * 24));
            if (diff > 0) return `(<span class="text-success">în ${diff} zile</span>)`;
            if (diff === 0) return `(<span class="text-warning">astăzi</span>)`;
            return `(<span class="text-danger">expirat cu ${Math.abs(diff)} zile</span>)`;
        }

        const detailsTab = `
        <div class="tab-pane fade" id="detalii-${projectId}">
          <ul class="list-group list-group-flush small">
            ${
                USER_LEVEL == 1
                    ? ""
                    : `
            <li class="list-group-item"><strong>Avans:</strong> ${p.advance}</li>
            <li class="list-group-item"><strong>Restanţă:</strong> ${p.remainder}</li>
            `
            }
            <li class="list-group-item"><strong>Data preluării proiectului:</strong> ${formatMD(p.date_received)}</li>
            <li class="list-group-item"><strong>Prezentare schițe tehnice:</strong> ${formatMD(p.date_technical)} ${daysRemaining(p.date_technical)}</li>
            <li class="list-group-item"><strong>Prezentare proiect 3D:</strong> ${formatMD(p.date_3d)} ${daysRemaining(p.date_3d)}</li>
            <li class="list-group-item"><strong>Termen limită (deadline):</strong> ${formatMD(p.date_deadline)} ${daysRemaining(p.date_deadline)}</li>
            <li class="list-group-item"><strong>Creat la:</strong> ${formatMD(p.created_at)}</li>
          </ul>
        </div>
      `;

        const card = `
      <div class="project-card card border-0 shadow-sm mb-4 p-4 rounded-4" data-id="${p.id}" style="background:#fff; transition:0.25s ease;">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h5 class="mb-1 text-capitalize fw-bold">${p.title}</h5>
            <span class="badge rounded-pill bg-light text-dark border px-2 py-1">Etapă curentă: ${p.stage || "—"}</span>
          </div>
          ${
              USER_LEVEL == 1
                  ? ""
                  : `
          <div>
            <button class="form-cancel bg-1 ms-1 btn-project edit-project" data-id="${p.id}" title="Edit">
              <i class="fas fa-pen i-btn"></i>
            </button>
            <button class="form-cancel bg-1 ms-1 btn-project delete-project" data-id="${p.id}" title="Delete">
              <i class="fas fa-trash i-btn"></i>
            </button>
          </div>
          `
          }
        </div>

        <div class="row small g-2 mb-3 pro-fs">
          <div class="col-md-4"><span class="text-muted">Suprafață:</span> <span>${p.surface || "-"} m²</span></div>
          ${
              USER_LEVEL == 1
                  ? ""
                  : `
            <div class="col-md-4"><span class="text-muted">Preț / m²:</span> <span>${p.price_per_m2 || "-"} ${p.currency || ""}</span></div>
            <div class="col-md-4">
              <span class="text-muted">Total:</span>
              <span class="text-success fw-semibold">${p.total_price || "-"} ${p.currency || ""}</span>
            </div>
          `
          }

        </div>

<ul class="nav nav-tabs small" id="tabs-${projectId}" role="tablist">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#foto-${projectId}">Chat</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#note-${projectId}">Note</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#lucratori-${projectId}">Proiectanți</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contract-${dbId}">Contract</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#detalii-${projectId}">Detalii</a></li>
</ul>

        <div class="tab-content tab-content-pers">

          <div class="tab-pane fade show active" id="foto-${projectId}">
            <div class="chat-textarea">


              <input type="file" id="files-${htmlId}" multiple class="d-none">
              <button type="button" class="btn btn-light me-2 btn-upload" data-target="#files-${htmlId}" title="Atașează fișiere">
                <i class="fa-solid fa-paperclip"></i>
              </button>


              <div class='bg-chat-textarea btn-light'>
                <textarea class="form-control me-2 inp-text-chat" rows="1" placeholder="Scrie o notă..." id="note-input-${projectId}"></textarea>

                <button class="btn btn-primary btn-save-note" data-html-id="${htmlId}" data-db-id="${dbId}">
                  <i class="fa-solid fa-paper-plane"></i>
                </button>
              </div>

            </div>
            <div class="selected-files" id="selected-files-${htmlId}"></div>

            <div id="notes-container-${projectId}" class="mt-3"></div>
          </div>

          <div class="tab-pane fade" id="note-${projectId}">
            <div class="p-3">

              <div class='bg-chat-textarea btn-light'>
                <textarea class="form-control me-2 inp-text-chat" rows="1" placeholder="Scrie o notă..." id="note-text-project-${dbId}"></textarea>

                <button class="btn btn-primary btn-save-simple-note" data-id="${dbId}">
                  <i class="fa-solid fa-paper-plane"></i>
                </button>
              </div>

              <div id="simple-notes-list-${projectId}" class="mt-3"></div> 

            </div>
          </div>


          <div class="tab-pane fade" id="lucratori-${projectId}">
            <div class="d-flex flex-wrap">${employeeCards}</div>
          </div>
           <div class="tab-pane fade" id="contract-${dbId}">
             <h6 class="text-muted small mb-2">Contracte salvate</h6>
             <div id="saved-contracts-project-${dbId}"></div>
             <br>
             <div id="contract-project-${dbId}" class="mb-4">
  <form class="contract-form p-3 rounded-4 shadow-sm bg-white ios-form">
    <div class="form-section">

    <div class="row g-2">
  <div class="col-6">
    <div class="form-floating mb-2">
        <input type="date" class="form-control form-control-sm" id="contract-data-${dbId}" name="data">
        <label for="contract-data-${dbId}">Din data</label>
      </div>
  </div>
  <div class="col-6">
    <div class="form-floating mb-2">
        <input type="text" class="form-control form-control-sm" id="contract-nume-${dbId}" name="nume_prenume">
        <label for="contract-nume-${dbId}">Nume și prenume</label>
      </div>
  </div>
</div>


      <div class="row g-2">
  <div class="col-6">
    <div class="form-floating mb-2">
        <input type="email" class="form-control form-control-sm" id="contract-email-${dbId}" name="email">
        <label for="contract-email-${dbId}">Adresa email</label>
      </div>
  </div>
  <div class="col-6">
    <div class="form-floating mb-2">
        <input type="text" class="form-control form-control-sm" id="contract-idnp-${dbId}" name="numar_identitate">
        <label for="contract-idnp-${dbId}">Număr de identitate</label>
      </div>
  </div>
</div>

      

      

      

      <div class="form-floating mb-3">
        <input type="text" class="form-control form-control-sm" id="contract-adresa-${dbId}" name="adresa">
        <label for="contract-adresa-${dbId}">Adresă</label>
      </div>
    </div>

    <h6 class="fw-semibold text-secondary mb-2 mt-3 border-top pt-3">Etapele aferente prestării serviciilor</h6>
    <div class="row g-2">
      <div class="col-4">
        <div class="form-floating">
          <input type="text" class="form-control form-control-sm" name="etapa1" value="15">
          <label>Etapa 1 zile</label>
        </div>
      </div>
      <div class="col-4">
        <div class="form-floating">
          <input type="text" class="form-control form-control-sm" name="etapa2" value="35">
          <label>Etapa 2 zile</label>
        </div>
      </div>
      <div class="col-4">
        <div class="form-floating">
          <input type="text" class="form-control form-control-sm" name="etapa3" value="10">
          <label>Etapa 3 zile</label>
        </div>
      </div>
    </div>

    <div class="row g-2 mt-2">
      <div class="col-6">
        <div class="form-floating">
          <input type="text" class="form-control form-control-sm" name="etapa4_1" value="12">
          <label>Etapa 4.1 luni</label>
        </div>
      </div>
      <div class="col-6">
        <div class="form-floating">
          <input type="text" class="form-control form-control-sm" name="etapa4_2" value="12">
          <label>Etapa 4.2 luni</label>
        </div>
      </div>
      <div class="col-6">
        <div class="form-floating">
          <input type="text" class="form-control form-control-sm" name="etapa5_1" value="12">
          <label>Etapa 5.1 luni</label>
        </div>
      </div>
      <div class="col-6">
        <div class="form-floating">
          <input type="text" class="form-control form-control-sm" name="etapa5_2" value="12">
          <label>Etapa 5.2 luni</label>
        </div>
      </div>
    </div>

    <div class="form-floating mt-3 mb-2">
      <input type="text" class="form-control form-control-sm" name="valoare">
      <label>Valoarea (euro)</label>
    </div>
    <p c;ass="mb-0">4.2 Remunerația pentru serviciile dedesign interior va fi plătită în două tranșe astfel:</p>
    <div class="row g-2">
      <div class="col-4"><input type="text" name="transa1_1" class="form-control form-control-sm" placeholder="50%"></div>
      <div class="col-4"><input type="text" name="transa1_2" class="form-control form-control-sm" placeholder="525 €"></div>
      <div class="col-4"><input type="text" name="transa1_3" class="form-control form-control-sm" placeholder="3 zile lucratoare"></div>
    </div>

    <div class="row g-2 mt-2">
      <div class="col-4"><input type="text" name="transa2_1" class="form-control form-control-sm" placeholder="50%"></div>
      <div class="col-4"><input type="text" name="transa2_2" class="form-control form-control-sm" placeholder="525 €"></div>
      <div class="col-4"><input type="text" name="transa2_3" class="form-control form-control-sm" placeholder="3 zile lucratoare"></div>
    </div>

    <div class="form-floating mt-3 mb-4">
      <input type="email" class="form-control form-control-sm" name="email_spacedesign" value="space.designds@gmail.com">
      <label>Email SpaceDesign</label>
    </div>
    <button class="form-accept btn-aufrage btn-save-contract col-sm" data-project="${dbId}" data-template="1">
       <span>
          Salvează
       </span>
    </button>
 
  </form>
</div>
           </div>
          ${detailsTab}
        </div>
      </div>
    `;

        container.append(card);
        loadProjectNotes(dbId, htmlId);
        loadSimpleNotes(dbId);
        loadContracts(dbId);
    });
}

function shortFileName(name, maxLen = 8) {
    const dotIndex = name.lastIndexOf(".");
    const ext = dotIndex !== -1 ? name.slice(dotIndex) : "";
    const base = dotIndex !== -1 ? name.slice(0, dotIndex) : name;
    if (base.length > maxLen) {
        return base.slice(0, maxLen) + "…" + ext;
    }
    return name;
}

function renderFileLinks(files) {
  if (!files || !files.length) return "";

  return files.map((f) => {
    const ext = f.split(".").pop().toLowerCase();
    const filePath =
      `/crm/pages/packs/client/client-page/projects/create-project/like-chat/project_notes/${f}`;

    const isImage = ["png","jpg","jpeg","gif","webp","avif","svg"].includes(ext);
    const isVideo = ["mp4","webm","ogg","mov"].includes(ext);

    if (isImage) {
      return `
        <a href="${filePath}" target="_blank" class="me-2 mb-2 d-inline-block">
          <img src="${filePath}"
               style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
        </a>
      `;
    }

    if (isVideo) {
      return `
        <a href="${filePath}" target="_blank"
           class="d-flex align-items-center gap-2 px-2 py-1 border rounded me-2 mb-2 text-decoration-none">
          <i class="fa-solid fa-video text-danger"></i>
          <span class="small">${shortFileName(f)}</span>
        </a>
      `;
    }

    return `
      <a href="${filePath}" target="_blank"
         class="d-flex align-items-center gap-2 px-2 py-1 border rounded me-2 mb-2 text-decoration-none">
        <i class="fas fa-paperclip"></i>
        <span class="small">${shortFileName(f)}</span>
      </a>
    `;
  }).join("");
}


function loadProjectNotes(dbId, htmlId) {
    $.ajax({
        url: "/crm/pages/packs/client/client-page/projects/create-project/like-chat/load-notes.php",
        type: "GET",
        data: { project_id: dbId },
        dataType: "json",
        success: function (notes) {
            const notesContainer = $(`#notes-container-${htmlId}`);
            if (!notesContainer.length) return;

            notesContainer.empty();

            if (!notes.length) {
                notesContainer.html('<p class="text-muted small text-center"></p>');
                // Nicio notă adăugată.
                return;
            }

            notes.forEach((note) => {
                const files = note.files ? JSON.parse(note.files) : [];
                const fileLinks = renderFileLinks(files);

                notesContainer.append(`
          <div class="chat-card">
            <div class='user-chat-img'>
              <img src="/crm/assets/img/user.jpg" class='userphoto-chat'>
            </div>

            <div class='main-chat-info'>
              <div class="user-chat">${note.username || ""}</div>
              <div class='bg-chat-info'>
                <div class="fw-medium">${note.note_text}</div>
              </div>
              <div class="file-chat">
                ${fileLinks}
              </div>
              <div class="date-chat">
                ${new Date(note.created_at).toLocaleString("ro-RO")}
              </div>
            </div>

          </div>
        `);
            });
        },
        error: function () {
            console.error("Eroare la încărcarea notițelor pentru proiectul", dbId);
        },
    });
}

$(document).on("click", ".btn-save-note", function () {
    const htmlId = $(this).data("html-id");
    const dbId = $(this).data("db-id");
    const input = $(`#note-input-${htmlId}`);
    const noteText = input.val().trim();

    if (!noteText) {
        alert("Scrie ceva înainte de a salva.");
        return;
    }

    const files = document.getElementById(`files-${htmlId}`).files;
    const formData = new FormData();
    formData.append("project_id", dbId);
    formData.append("note_text", noteText);
    for (let f of files) formData.append("files[]", f);

    // 🔹 adaugăm progress bar în container
    const progressContainer = $(`#selected-files-${htmlId}`);
    progressContainer.append(`
    <div class="upload-progress mt-2" style="display:none;">
      <div class="progress" style="height:6px;">
        <div class="progress-bar bg-primary" role="progressbar" style="width:0%;"></div>
      </div>
      <small class="text-muted small">Încărcare fișiere...</small>
    </div>
  `);

    $.ajax({
        url: "/crm/pages/packs/client/client-page/projects/create-project/like-chat/add-note.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",

        // 🔹 aici se adaugă progress tracking
        xhr: function () {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    $(".upload-progress").show();
                    $(".upload-progress .progress-bar").css("width", percent + "%");
                }
            });
            return xhr;
        },

        success: function (resp) {
            if (resp.success) {
                const now = new Date().toLocaleString("ro-RO");
                const fileLinks = renderFileLinks(resp.files || []);

                $(`#notes-container-${htmlId}`).prepend(`
          <div class="chat-card">
            <div class='user-chat-img'>
              <img src="/crm/assets/img/user.jpg" class='userphoto-chat'>
            </div>
            <div class='main-chat-info'>
              <div class="user-chat">Tu</div>
              <div class='bg-chat-info'>
                <div class="fw-medium">${noteText}</div>
              </div>
              <div class="file-chat">${fileLinks}</div>
              <div class="date-chat">${now}</div>
            </div>
          </div>
        `);

                input.val("");
                $(`#files-${htmlId}`).val("");
                $(`#selected-files-${htmlId}`).empty();

                // 🔹 ascundem bara de progres
                $(".upload-progress").fadeOut(400, function () {
                    $(this).remove();
                });
            } else {
                alert("Eroare la salvare.");
            }
        },
        error: function () {
            alert("Eroare de conexiune cu serverul.");
        },
    });
});

$(document).on("click", ".btn-upload", function () {
    const inputId = $(this).data("target");
    $(inputId).trigger("click");
});

$(document).on("change", 'input[type="file"]', function () {
    const input = this;
    const htmlId = input.id.replace("files-", "");
    const filesContainer = $(`#selected-files-${htmlId}`);
    filesContainer.empty();

    Array.from(input.files).forEach((file, index) => {
        const ext = file.name.split(".").pop().toLowerCase();
        const isImage = ["png", "jpg", "jpeg", "gif", "webp", "heic", "heif", "tiff", "tif", "bmp", "avif", "svg"].includes(ext);

        const icon = isImage ? `<i class="fa-solid fa-image text-success"></i>` : `<i class="fa-solid fa-file"></i>`;

        filesContainer.append(`
      <div class="selected-file d-flex align-items-center justify-content-between px-2 py-1 indev-file">
        <div class="d-flex align-items-center gap-2">
          ${icon}
          <span class="file-name small text-truncate" style="max-width:140px;">${shortFileName(file.name)}</span>
        </div>
        <button type="button" class="btn btn-sm btn-link text-danger btn-remove-file" data-index="${index}" title="Șterge">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    `);
    });
});

// Ștergere fișier selectat
$(document).on("click", ".btn-remove-file", function () {
    const indexToRemove = Number($(this).data("index"));
    const container = $(this).closest(".selected-files"); // ex: #selected-files-project-1
    const htmlId = container.attr("id").replace("selected-files-", ""); // -> project-1
    const input = document.getElementById(`files-${htmlId}`);
    if (!input) return;

    const dt = new DataTransfer();
    Array.from(input.files).forEach((file, i) => {
        if (i !== indexToRemove) dt.items.add(file);
    });

    input.files = dt.files;
    $(input).trigger("change"); // re-randăm lista cu index-uri corecte
});

// 🔹 Salvare notă simplă
$(document).on("click", ".btn-save-simple-note", function () {
    const projectId = $(this).data("id");
    const textarea = $(`#note-text-project-${projectId}`);
    const text = textarea.val().trim();

    if (!text) {
        alert("Scrie o notă înainte de a salva.");
        return;
    }

    $.ajax({
        url: "/crm/pages/packs/client/client-page/projects/save-simple-note.php",
        type: "POST",
        data: { project_id: projectId, text },
        dataType: "json",
        success: function (resp) {
            if (resp.success) {
                const now = new Date().toLocaleString("ro-RO");
                $(`#simple-notes-list-project-${projectId}`).prepend(`
          <div class="border rounded p-2 mb-2 bg-light">
            <div>${text}</div>
            <small class="text-muted">${now}</small>
          </div>
        `);
                textarea.val("");
                loadSimpleNotes(projectId);
            } else {
                alert("Eroare la salvare.");
            }
        },
        error: function () {
            alert("Eroare la conexiune cu serverul.");
        },
    });
});

// 🔹 Încărcare note simple
function loadSimpleNotes(dbId) {
    $.ajax({
        url: "/crm/pages/packs/client/client-page/projects/load-simple-notes.php",
        type: "GET",
        data: { project_id: dbId }, // trimitem doar numărul
        dataType: "json",
        success: function (notes) {
            const list = $(`#simple-notes-list-project-${dbId}`);
            if (!list.length) {
                console.error("⚠️ Containerul nu există pentru project_id:", dbId);
                return;
            }

            list.empty();

            if (!Array.isArray(notes)) {
                console.error("❌ Răspuns invalid de la PHP:", notes);
                list.html('<p class="text-danger small text-center mb-0">Eroare la citirea notelor.</p>');
                return;
            }

            if (!notes.length) {
                list.html('<p class="text-muted small text-center mb-0">Nicio notă adăugată.</p>');
                return;
            }

            notes.forEach((note) => {
                list.append(`
          <div class="border rounded p-2 mb-2 bg-light">
            <div>${note.note_text}</div>
            <small class="text-success d-block mt-1">
              ${(note.username ? note.username + " · " : "") + new Date(note.created_at).toLocaleString("ro-RO")}
            </small>
          </div>
        `);
            });
        },
        error: function (xhr, status, error) {
            console.error("❌ Eroare AJAX:", status, error, xhr.responseText);
            const list = $(`#simple-notes-list-project-${dbId}`);
            list.html('<p class="text-danger small text-center mb-0">Eroare la conexiune cu serverul.</p>');
        },
    });
}

// afisarea template contracte

// salvare contract
$(document).on("click", ".btn-save-contract", function (e) {
    e.preventDefault(); // oprește trimiterea clasică
    e.stopPropagation();
    const projectId = $(this).data("project");
    const $form = $(`#contract-project-${projectId} form`);
    const fields = {};
    $form.serializeArray().forEach((p) => (fields[p.name] = p.value));
    $.ajax({
        url: "/crm/pages/packs/client/client-page/projects/create-project/contracte/save_contract.php",
        type: "POST",
        dataType: "json",
        data: { project_id: projectId, fields_json: JSON.stringify(fields) },
        success: (resp) => {
            if (resp.success) {
                alert("Contract salvat!");
                loadContracts(projectId);
            } else {
                alert("Eroare la salvare: " + (resp.error || ""));
            }
        },
        error: () => alert("Eroare conexiune."),
    });
});

function loadContracts(projectId) {
    $.ajax({
        url: "/crm/pages/packs/client/client-page/projects/create-project/contracte/load_contracts.php",
        type: "GET",
        data: { project_id: projectId },
        dataType: "json",
        success: function (list) {
            const container = $(`#saved-contracts-project-${projectId}`);
            container.empty();

            if (!Array.isArray(list) || !list.length) {
                container.html('<p class="text-muted small">Niciun contract salvat.</p>');
                return;
            }

            list.forEach((c) => {
                let fields = {};
                try {
                    fields = JSON.parse(c.fields_json);
                } catch (e) {
                    return;
                }

                const prettyFields = Object.entries(fields)
                    .filter(([k, v]) => v && v.trim() !== "")
                    .map(([k, v]) => {
                        const label = k.replace(/_/g, " ").replace(/\b\w/g, (l) => l.toUpperCase());
                        return `<li><strong>${label}:</strong> ${v}</li>`;
                    })
                    .join("");

                const createdAt = new Date(c.created_at).toLocaleString("ro-RO");
                const viewUrl = `/crm/pages/packs/client/client-page/projects/create-project/contracte/view_contract.php?slug=${c.id}`;

                container.append(`
                  <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <div class="fw-semibold text-secondary">Creat la: ${createdAt}</div>
                      <a href="${viewUrl}" target="_blank" class="btn btn-sm btn-outline-primary px-3">
                        <i class="fa-solid fa-eye me-1"></i> Vezi
                      </a>
                    </div>
                    <ul class="mb-0 small" style="line-height:1.6;">
                      ${prettyFields || "<li><em>Nicio valoare completată</em></li>"}
                    </ul>
                  </div>
                `);
            });
        },
        error: function (xhr, status, error) {
            console.error("❌ Eroare la citire contracte:", status, error, xhr.responseText);
            const container = $(`#saved-contracts-project-${projectId}`);
            container.html('<p class="text-danger small text-center">Eroare la încărcarea contractelor.</p>');
        },
    });
}

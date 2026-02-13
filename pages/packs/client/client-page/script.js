const globalClientId = (() => {
    const params = new URLSearchParams(window.location.search);
    const slug = params.get("slug");
    if (!slug) return null;
    const parts = slug.split("-");
    const id = parseInt(parts[parts.length - 1]);
    return isNaN(id) ? null : id;
})();

$(document).ready(function () {
    $("#multiple_employees").select2({
        theme: "bootstrap4",
        width: "style",
        dropdownParent: $("#new-project"),
        placeholder: $("#multiple_employees").attr("placeholder"),
        allowClear: Boolean($("#multiple_employees").data("allow-clear")),
    });

    $("#edit_employees_select").select2({
        theme: "bootstrap4",
        width: "style",
        dropdownParent: $("#edit-project"),
        placeholder: $("#edit_employees_select").attr("placeholder"),
        allowClear: Boolean($("#edit_employees_select").data("allow-clear")),
    });
});

function renderEmployees(containerId, selectId, surfaceSelector) {
    const selected = $(selectId).val() || [];
    const container = $(containerId);
    container.empty();

    selected.forEach((value) => {
        const option = $(`${selectId} option[value="${value}"]`);
        const price = parseFloat(option.data("pret")) || 0;
        const shortName = value.replace(/\[.*?\]\s*-\s*/g, "");

        const row = $(`
      <div class="d-flex align-items-center mb-2 employee-row">
        <span class="me-2 fw-semibold-nam" data-price="${price}">${shortName}</span>
        <input type="text" class="form-control me-2 price_m2 form-valoare" value="${price}">
        <input type="text" class="form-control form-valoare total_field" name="value_${shortName}" placeholder="Valoare">
      </div>
    `);
        container.append(row);
    });

    // calculează automat valorile dacă există suprafață
    recalcEmployeeValues(containerId, surfaceSelector);
}

function recalcEmployeeValues(containerId, surfaceSelector) {
    const surface = parseFloat($(surfaceSelector).val()) || 0;
    $(`${containerId} .employee-row`).each(function () {
        const price = parseFloat($(this).find(".price_m2").val()) || 0;
        const total = (surface * price).toFixed(2);
        $(this)
            .find(".total_field")
            .val(surface ? total : "");
    });
}

// evenimente
$("#multiple_employees").on("change", function () {
    renderEmployees("#infoDivNew", "#multiple_employees", 'input[name="surface"]');
});

$("#edit_employees_select").on("change", function () {
    renderEmployees("#infoDivEdit", "#edit_employees_select", "#edit_surface");
});

// când se schimbă suprafața → recalculează valorile
$('input[name="surface"]').on("input", function () {
    recalcEmployeeValues("#infoDivNew", 'input[name="surface"]');
});

$("#edit_surface").on("input", function () {
    recalcEmployeeValues("#infoDivEdit", "#edit_surface");
});

if (globalClientId) {
  fetch(`/crm/pages/packs/client/client-page/server.php?id=${globalClientId}`)
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const c = data.client;

        // date generale
        document.getElementById("client_name").innerHTML = c.last_name_first_name;
        document.getElementById("client_address").innerHTML = c.adresa_client;
        document.getElementById("client_phone").innerHTML = c.phone_number;
        document.getElementById("client_email").innerHTML = c.user_email_field;
        document.getElementById("client_email").href = "mailto:" + c.user_email_field;
        document.getElementById("nr_identitate").innerHTML = c.nr_identitate;

        // după render
        renderProjects(projects);

        setTimeout(() => {
          document.querySelectorAll('[id^="contract-nume"]').forEach(el => el.value = c.last_name_first_name);
          document.querySelectorAll('[id^="contract-email"]').forEach(el => el.value = c.user_email_field);
          document.querySelectorAll('[id^="contract-idnp"]').forEach(el => el.value = c.nr_identitate);
          document.querySelectorAll('[id^="contract-adresa"]').forEach(el => el.value = c.adresa_client);
        }, 500);
      } else console.error("Client not found");
    })
    .catch(err => console.error(err));
}

$("#form-new-project").on("submit", function (e) {
    e.preventDefault();

    const formData = $(this).serialize();
    const total = $("#total_price").text();

    let employeesFinal = [];
    $("#infoDivNew .employee-row").each(function () {
        const fullName = $(this).find("span").text().trim();
        const totalValue = $(this).find(".total_field").val().trim();
        const price = $(this).find(".price_m2").val().trim();
        // format: [id] - nume (pret x total)
        const match = $("#multiple_employees option")
            .filter(function () {
                return $(this).text().trim() === fullName;
            })
            .val();
        const idPart = match ? match.split(" ")[0] : "";
        employeesFinal.push(`${idPart} - ${fullName} (${price} × ${totalValue})`);
    });

    const employeesString = employeesFinal.join(", ");
    const finalData = formData + "&total_price=" + encodeURIComponent(total) + "&client_id=" + encodeURIComponent(globalClientId) + "&employees=" + encodeURIComponent(employeesString);

    $.ajax({
        type: "POST",
        url: "/crm/pages/packs/client/client-page/projects/create-project/server-create.php",
        data: finalData,
        dataType: "json",
        success: function (projects) {
            renderProjects(projects);
            $("#new-project").modal("hide");
            $("#form-new-project")[0].reset();
            $("#total_price").text("0");
            $("#multiple_employees").val(null).trigger("change");
        },
        error: function () {
            alert("Eroare de conexiune cu serverul.");
        },
    });
});

function loadProjects() {
    if (!globalClientId) return;

    $.ajax({
        url: "/crm/pages/packs/client/client-page/projects/create-project/load-projects.php",
        type: "GET",
        data: { client_id: globalClientId },
        dataType: "json",
        success: function (projects) {
            renderProjects(projects);
        },
        error: function () {
            $("#projects").html('<p class="text-danger">Eroare la încărcarea proiectelor.</p>');
        },
    });
}

// apel automat când se încarcă pagina
$(document).ready(function () {
    loadProjects();
});

$(document).on("click", ".delete-project", function () {
    const id = $(this).data("id");
    if (!confirm("Sigur vrei să ștergi acest proiect?")) return;

    $.post("/crm/pages/packs/client/client-page/projects/create-project/delete-project.php", { id }, function (resp) {
        if (resp.trim() === "success") {
            loadProjects(); // reîncarcă lista actualizată
        } else {
            alert("Eroare la ștergere!");
        }
    });
});

$(document).on("click", ".edit-project", function () {
    const projectId = $(this).data("id");
    console.log("Se editează proiectul ID:", projectId);

    $.ajax({
        url: "/crm/pages/packs/client/client-page/projects/create-project/get-project.php",
        type: "GET",
        data: { id: projectId },
        dataType: "json",
        success: function (response) {
            console.log("Răspuns primit:", response);

            // dacă PHP returnează array, ia primul element
            const p = Array.isArray(response) ? response[0] : response;
            if (!p || !p.id) {
                alert("Proiectul nu a fost găsit.");
                return;
            }

            // completează câmpurile
            $("#edit_project_id").val(p.id);
            $("#edit_title").val(p.title);
            $("#edit_stage").val(p.stage);
            $("#edit_surface").val(p.surface);
            $("#edit_price_per_m2").val(p.price_per_m2);
            $("#edit_currency").val(p.currency);
            $("#edit_advance").val(p.advance);
            $("#edit_remainder").val(p.remainder);
            $("#edit_total_price").text(p.total_price);
            $("#edit_date_received").val(p.date_received);
            $("#edit_date_technical").val(p.date_technical);
            $("#edit_date_3d").val(p.date_3d);
            $("#edit_date_deadline").val(p.date_deadline);

            // angajații
            if (p.employees) {
                const employeesArray = p.employees.split(",").map(e => e.trim());
                const container = $("#infoDivEdit");
                container.empty();

                employeesArray.forEach(entry => {
                    // Exemplu: [1] - Cojocaru Mihai (9 × 45.00)
                    const match = entry.match(/(\[\d+\])\s*-\s*(.*?)\s*\(([\d.,]+)\s*×\s*([\d.,]+)\)/);
                    if (!match) return;
                    const idText = match[1];
                    const name = match[2];
                    const price = match[3];
                    const total = match[4];

                    // Adaugăm opțiunea selectată în dropdown
                    const fullValue = `${idText} - ${name}`;
                    $("#edit_employees_select option").each(function () {
                        if ($(this).val().startsWith(idText)) {
                            $(this).prop("selected", true);
                        }
                    });

                    // Adăugăm rândul complet
                    const row = $(`
                      <div class="d-flex align-items-center mb-2 employee-row">
                        <span class="me-2 fw-semibold-nam" data-price="${price}">${name}</span>
                        <input type="text" class="form-control me-2 price_m2 form-valoare" value="${price}">
                        <input type="text" class="form-control form-valoare total_field" name="value_${name}" value="${total}" placeholder="Valoare">
                      </div>
                    `);
                    container.append(row);
                });

                // Actualizează vizual select2
                $("#edit_employees_select").trigger("change.select2");
            } else {
                $("#edit_employees_select").val(null).trigger("change");
                $("#infoDivEdit").empty();
            }

            $("#edit-project").modal("show");
        },
        error: function (xhr, status, error) {
            console.error("Eroare AJAX:", error);
            alert("Eroare la încărcarea datelor proiectului.");
        },
    });
});

$("#form-edit-project").on("submit", function (e) {
    e.preventDefault();

    const formData = $(this).serialize();
    const total = $("#edit_total_price").text();

    // reconstruim lista employees la fel ca în crearea proiectului
    let employeesFinal = [];
    $("#infoDivEdit .employee-row").each(function () {
        const fullName = $(this).find("span").text().trim();
        const totalValue = $(this).find(".total_field").val().trim();
        const price = $(this).find(".price_m2").val().trim();

        const match = $("#edit_employees_select option")
            .filter(function () {
                return $(this).text().trim() === fullName;
            })
            .val();
        const idPart = match ? match.split(" ")[0] : "";
        if (price && totalValue)
            employeesFinal.push(`${idPart} - ${fullName} (${totalValue} × ${price})`);
    });

    const employeesString = employeesFinal.join(", ");
    const finalData =
        formData +
        "&total_price=" +
        encodeURIComponent(total) +
        "&employees=" +
        encodeURIComponent(employeesString);

    $.ajax({
        type: "POST",
        url: "/crm/pages/packs/client/client-page/projects/create-project/edit-project.php",
        data: finalData,
        success: function (resp) {
            if (resp.trim() === "success") {
                $("#edit-project").modal("hide");
                loadProjects();
            } else {
                alert("Eroare la salvarea modificărilor!");
            }
        },
        error: function () {
            alert("Eroare de conexiune la server.");
        },
    });
});

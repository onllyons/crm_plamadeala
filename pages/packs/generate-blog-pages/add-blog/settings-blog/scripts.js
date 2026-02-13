// Cache selectors
const $employeesList = $("#employees-list");
const $employeesList1 = $("#employees-list1");
const menuItem = $("#blog-category");
const categoryItem = $("#blog-hashtag");
const $editForm = $("#edit-form");
const $btnSubmit = $("#btnSubmit");
const $btnUpdateSubmit = $("#btnUpdateSubmit");

// Get all employee records
async function getAll() {
  try {
    const lang = $("#settings-lang").val();
    const response = await $.get("settings-blog/all.php?lang=" + lang);
    const employees = JSON.parse(response);

    let html = "";
    let html1 = "";
    let menuItemHtml = "";
    let categoryItemHtml = "";

    if (employees.length) {
      html += '<div class="list-group">';
      html1 += '<div class="list-group">';

      employees.forEach((employee) => {

        // --- CATEGORY ---
        if (employee.managementForm.includes("category")) {
          html += `<a href="#" class="list-group-item list-group-item-action">
                      <p>${employee.managementTitle}</p>
                      <button class='btn btn-sm btn-primary mt-2'
                          data-target='#edit-employee-modal'
                          data-id='${employee.id}'>
                          <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <button class='btn btn-sm btn-danger mt-2 ml-2 btn-delete-employee'
                          data-id='${employee.id}' type='button'>
                          <i class="fa-solid fa-trash"></i>
                      </button>
                   </a>`;

          menuItemHtml += `<option value="${employee.managementUniqid}">${employee.managementTitle}</option>`;
        }

        // --- HASHTAG ---
        if (employee.managementForm.includes("hashtag")) {
          html1 += `<a href="#" class="list-group-item list-group-item-action">
                      <p>${employee.managementTitle}</p>
                      <button class='btn btn-sm btn-primary mt-2'
                          data-target='#edit-employee-modal'
                          data-id='${employee.id}'>
                          <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <button class='btn btn-sm btn-danger mt-2 ml-2 btn-delete-employee'
                          data-id='${employee.id}' type='button'>
                          <i class="fa-solid fa-trash"></i>
                      </button>
                    </a>`;

          categoryItemHtml += `<option value="${employee.managementTitle}">${employee.managementTitle}</option>`;
        }
      });

      html += "</div>";
      html1 += "</div>";

    } else {
      html = '<div class="alert alert-warning">No records found!</div>';
      html1 = '<div class="alert alert-warning">No records found!</div>';
    }

    $employeesList.html(html);
    $employeesList1.html(html1);
    menuItem.html(menuItemHtml);
    categoryItem.html(categoryItemHtml);

  } catch (error) {
    console.error(error);
  }
}


// SAVE
async function save() {
  $btnSubmit.on("click", async function () {

    const caption = $btnSubmit.html();
    $btnSubmit.attr("disabled", true).html("Processing...");

    let formData = $("#form").serializeArray();
    formData.push({ name: "lang", value: $("#settings-lang").val() });

    const route = $("#form").attr("action");
    const response = await $.post(route, formData);
    const data = JSON.parse(response);

    if (data.status === "success") {
      await getAll();
      toastr.success(data.message);
      $("#form")[0].reset();
    } else {
      toastr.error(data.message);
    }

    $btnSubmit.attr("disabled", false).html(caption);
  });
}


// EDIT (GET BY ID)
async function getById(employeeId) {
  const lang = $("#settings-lang").val();
  const response = await $.get(`settings-blog/get.php?employee_id=${employeeId}&lang=${lang}`);
  const employee = JSON.parse(response);

  document.querySelector("#form-edit-data").style.display = "block";
  document.querySelector("#form-add-data").style.display = "none";

  $editForm.find("[name='id']").val(employee.id);
  $editForm.find("[name='managementForm']").val(employee.managementForm);
  $editForm.find("[name='managementTitle']").val(employee.managementTitle);
}


// UPDATE
async function update() {
  $btnUpdateSubmit.on("click", async function () {

    const caption = $btnUpdateSubmit.html();
    $btnUpdateSubmit.attr("disabled", true).html("Processing...");

    let formData = $editForm.serializeArray();
    formData.push({ name: "lang", value: $("#settings-lang").val() });

    const route = $editForm.attr("action");
    const response = await $.post(route, formData);
    const data = JSON.parse(response);

    if (data.status === "success") {
      await getAll();
      toastr.success(data.message);
      $editForm[0].reset();
      document.querySelector("#form-add-data").style.display = "block";
      document.querySelector("#form-edit-data").style.display = "none";
    } else {
      toastr.error(data.message);
    }

    $btnUpdateSubmit.attr("disabled", false).html(caption);
  });
}


// DELETE
async function remove(employeeId) {
  if (!employeeId) return;

  if (!confirm("Are you sure you want to delete this record?")) {
    toastr.info("Deletion canceled");
    return;
  }

  const lang = $("#settings-lang").val();

  const response = await $.get("settings-blog/delete.php", {
    employee_id: employeeId,
    lang: lang
  });
  const data = JSON.parse(response);

  if (data.status === "success") {
    await getAll();
    toastr.success(data.message);
  } else {
    toastr.error(data.message);
  }
}


// INIT
$(document).ready(async function () {

  await getAll();

  $("#settings-lang").on("change", function () {
    getAll();
  });

  save();
  update();

  $(document).on("click", "[data-target='#edit-employee-modal']", function () {
    getById($(this).data("id"));
  });

  $(document).on("click", ".btn-delete-employee", function () {
    remove($(this).data("id"));
  });

});

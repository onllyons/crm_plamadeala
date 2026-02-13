$(document).ready(function () {
    //change selectboxes to selectize mode to be searchable
    $(".select_sorting").select2();
});

[
    "#multiple_position_function",
    "#multiple_user_status_field",
    "#multiple_phone_number"
].forEach(function (selector) {
    $(selector).select2({
        theme: "bootstrap4",
        width: "style",
        placeholder: $(selector).attr("placeholder"),
        allowClear: Boolean($(selector).data("allow-clear")),
    });
});

let Tables = {
    startDate: 0,
    endDate: 0,

    tableInit: function () {
        this.tableRef = $("#table-upload_file").DataTable({
            lengthMenu: [
                [50, -1],
                [50, "All"],
            ],
            oLanguage: { sSearch: "" },
            dom: "lBfrtip",
            "columns": [
                { "visible": true },
                { "visible": true },
                { "visible": true },
                { "visible": true },
                { "visible": true },
                { "visible": true },
                { "visible": true },
                { "visible": true },
                { "visible": true },
            ],
            language: {
                lengthMenu: "_MENU_",
            },
            buttons: [
                {
                    extend: "colvis",
                    collectionLayout: "fixed columns",
                    collectionTitle: "Column visibility control",
                },
                {
                    extend: "copyHtml5",
                    text: 'Copy <i class="fas fa-copy"></i>',
                    titleAttr: "Copy",
                },
                {
                    extend: "excelHtml5",
                    text: 'Excel <i class="fas fa-file-excel"></i>',
                    titleAttr: "Excel",
                },
                {
                    extend: "csvHtml5",
                    text: 'CSV <i class="fas fa-file-csv"></i>',
                    titleAttr: "CSV",
                },
                {
                    extend: "pdfHtml5",
                    text: 'PDF <i class="fas fa-file-pdf"></i>',
                    titleAttr: "PDF",
                },
            ],
            responsive: true,
            autoFill: true,
            fnCreatedRow: function (nRow, aData, iDataIndex) {
                $(nRow).attr("id", aData[0]);
            },
            serverSide: "true",
            processing: "true",
            paging: "true",
            order: [],
            ajax: {
                url: "/crm/pages/packs/angajati/fetch_data.php",
                type: "post",
                data: function (d) {
                    d.startDate = Tables.startDate;
                    d.endDate = Tables.endDate;

                    const filters = [
                        "position_function",
                        "user_status_field",
                        "phone_number"
                    ];

                    filters.forEach(function (field) {
                        d["data_ajax_" + field] = $("#multiple_" + field).val();
                    });
                }
            },
            aoColumnDefs: [
                {
                    bSortable: false,
                    aTargets: [-1],
                },
            ],
        });

        [
            "#multiple_position_function",
            "#multiple_user_status_field",
            "#multiple_phone_number"
        ].forEach(function (selector) {
            $(selector).on("change", function () {
                Tables.tableRef.draw();
            });
        });

    },
    daterange: function () {
        let table = this.tableRef;

        $("#daterange").daterangepicker(
            {
                locale: {
                    format: "YYYY-MM-DD",
                    cancelLabel: "Clear",
                },
                ranges: {
                    Today: [moment(), moment()],
                    Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                    "Last 7 Days": [moment().subtract(6, "days"), moment()],
                    "Last 30 Days": [moment().subtract(29, "days"), moment()],
                    "This Month": [moment().startOf("month"), moment().endOf("month")],
                    "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
                },
                alwaysShowCalendars: true,
            },
            function (start, end, label) {
                console.log("New date range selected: " + start.format("YYYY-MM-DD") + " to " + end.format("YYYY-MM-DD") + " (predefined range: " + label + ")");
            }
        );
        $("#daterange").on("apply.daterangepicker", function (ev, picker) {
            /** записываем новые даты **/
            Tables.startDate = picker.startDate.format("YYYY-MM-DD");
            Tables.endDate = picker.endDate.format("YYYY-MM-DD");

            /** обновление таблицы */
            Tables.tableRef.draw();
        });

        $("#daterange").on("cancel.daterangepicker", function (ev, picker) {
            // Setăm valorile de start și sfârșit la null
            Tables.startDate = null;
            Tables.endDate = null;

            // Actualizăm tabelul
            Tables.tableRef.draw();

            // Resetăm datele intervalului
            $(this).val("");
        });
    },
};
$(document).ready(function () {
    Tables.tableInit();
    Tables.daterange();
});

  $('#status_profile').on('change', function () {
    const checked = $(this).is(':checked');
    $('#create-profile-inputs').slideToggle(checked);
    $('#create-cards').toggleClass('create-new-profile', checked);
  });

  // submit general (angajat + profil)
  $(document).on("submit", "#new-lines-upload_file", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const isProfileActive = $('#status_profile').is(':checked');

    // dacă e bifat profil → adaugăm câmpurile
    if (isProfileActive) {
      formData.append('username_profile', $('#username_profile').val());
      formData.append('password_profile', $('#password_profile').val());
      formData.append('password_repeat_profile', $('#password_repeat_profile').val());
      formData.append('level_profile', $('#level_profile').val() || '1');
    }

    $.ajax({
      url: "/crm/pages/packs/angajati/add_user.php",
      type: "POST",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'json',

      beforeSend: function () {
        $('#create-cards span').text('Saving...');
      },

      success: function (res) {
        console.log("Response:", res);
        if (res.status === 'success') {
          alert('✔ Angajat salvat' + (res.profile === 'created' ? ' + profil creat.' : '.'));
          Tables.tableRef.draw();
          $("#modal-new-lines-upload_file").modal("hide");
          $("#new-lines-upload_file")[0].reset();
          $('#create-profile-inputs').hide();
          $('#status_profile').prop('checked', false);
          $('#create-cards').removeClass('create-new-profile');
        } else {
          alert('❌ Eroare: ' + res.message);
        }
      },

      error: function (xhr) {
        console.error("Server error:", xhr.responseText);
        alert("Eroare de comunicare cu serverul.");
      },

      complete: function () {
        $('#create-cards span').text('Save');
      }
    });
  });


$(document).on("submit", "#update-lines-upload_file", function (e) {
    e.preventDefault();
    //var tr = $(this).closest('tr');
    var trid = $("#trid").val();
    var id = $("#id").val();

    var formData = new FormData(this);
    formData.append("id", id);

        $.ajax({
            url: "/crm/pages/packs/angajati/update_user.php",
            type: "post",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            success: function (data) {
                console.log(data)
                var json = JSON.parse(data);
                var status = json.status;
                if (status == "true") {
                    Tables.tableRef.draw();
                    $("#modal-edit-lines-upload_file").modal("hide");
                } else {
                    alert("failed");
                }
            },
        });
 
});


$("#table-upload_file").on("click", ".editbtn ", function (event) {
    var trid = $(this).closest("tr").attr("id");
    var id = $(this).data("id");
    $("#modal-edit-lines-upload_file").modal("show");

    $.ajax({
        url: "/crm/pages/packs/angajati/get_single_data.php",
        data: {
            id: id,
        },
        type: "post",
        success: function (data) {
            var json = JSON.parse(data);
            $("#edit_last_name_first_name").val(json.last_name_first_name);
            $("#edit_position_function").val(json.position_function);
            console.log("TRIMIT:", {
                position_function: $('#edit_position_function').val()
            });

            $("#edit_phone_number").val(json.phone_number);
            $("#edit_user_email_field").val(json.user_email_field);
            $("#edit_pret_m2").val(json.pret_m2);
            $("#edit_user_status_field").val(json.user_status_field);
            $("#edit_dateAdded").val(json.dateAdded);
            $("#id").val(id);
            $("#trid").val(trid);
        },
    });
});

$(document).on("click", ".deleteBtn", function (event) {
    event.preventDefault();
    const id = $(this).data("id");

    if (confirm("Do you want to delete a row?")) {
        $.ajax({
            url: "/crm/pages/packs/angajati/delete_user.php",
            type: "post",
            data: { id: id },
            success: function (data) {
                console.log("Server response for delete:", data);

                if (data.status === "success") {
                    Tables.tableRef.ajax.reload(null, false);
                } else {
                    alert("Row was not deleted. Unknown error.");
                }
            }
        });
    }
});





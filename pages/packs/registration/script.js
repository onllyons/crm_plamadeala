document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("register-form");
  const submitBtn = form.querySelector('button[type="submit"]');

  const table = $('#users-table').DataTable({
    ajax: '/crm/pages/packs/registration/ajax/data.php',
    dom: 'lBfrtip',
    lengthMenu: [
      [5, 10, 25, 50, -1],
      [5, 10, 25, 50, "Toți"]
    ],
    columns: [
      { data: 'id' },
      { data: 'name' },
      { data: 'username' },
      { data: 'email' },
      { data: 'phone' },
      { data: 'level' }
    ],
    oLanguage: { sSearch: "" },
    buttons: [
      {
        extend: 'copyHtml5',
        text: 'Copy <i class="fas fa-copy"></i>',
        titleAttr: 'Copiază',
        exportOptions: { columns: ':visible' }
      },
      {
        extend: 'excelHtml5',
        text: 'Excel <i class="fas fa-file-excel"></i>',
        titleAttr: 'Export Excel',
        exportOptions: { columns: ':visible' }
      },
      {
        extend: 'csvHtml5',
        text: 'CSV <i class="fas fa-file-csv"></i>',
        titleAttr: 'Export CSV',
        exportOptions: { columns: ':visible' }
      },
      {
        extend: 'pdfHtml5',
        text: 'PDF <i class="fas fa-file-pdf"></i>',
        titleAttr: 'Export PDF',
        exportOptions: { columns: ':visible' },
        orientation: 'landscape',
        pageSize: 'A4'
      }
    ]
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const username = form.username.value.trim();
    const password = form.password.value;
    const confirmPassword = form["password-confirm"].value;

    // ✅ Validare username
    if (username.length < 5) {
      alert("The username must be at least 5 characters long.");
      return;
    }

    // ✅ Validare parolă: minim 8 caractere și cel puțin o cifră între 1 și 9
    if (password.length < 8 || !/[1-9]/.test(password)) {
      alert("The password must contain at least 8 characters and a number between 1 and 9.");
      return;
    }

    // ✅ Confirmare parolă
    if (password !== confirmPassword) {
      alert("Parolele nu coincid.");
      return;
    }

    // Dacă totul e ok, continuă
    submitBtn.disabled = true;
    const formData = new FormData(form);

    try {
      const response = await fetch("/crm/pages/packs/registration/ajax/register.php", {
        method: "POST",
        body: formData
      });

      const result = await response.json();

      alert(result.message);

      if (result.success) {
        form.reset();
        table.ajax.reload(null, false); // reîncarcă fără a schimba pagina
      }
    } catch (error) {
      console.error("Eroare la trimitere:", error);
      alert("An error occurred while registering.");
    } finally {
      submitBtn.disabled = false;
    }
  });

});

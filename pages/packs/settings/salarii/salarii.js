// 🔹 definești întâi calea API
const apiBase = '/crm/pages/packs/settings/salarii';


if (employeeId) {
  refreshEmployee(employeeId);
}
function renderSummary(box, data) {
  const fmt = n => (Math.round(n * 100) / 100).toLocaleString('ro-RO');

  $(box).html(`
    <div class="row g-3 mb-3">
      <!-- Tarif (pret/m²) -->
      <div class="col-sm-6 col-md-3">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="bg-holder bg-card"
               style="background-image:url('https://prium.github.io/falcon/v3.24.0/assets/img/icons/spot-illustrations/corner-2.png');">
          </div>
          <div class="card-body position-relative">
            <h6>Tarif <span class="badge badge-subtle-info rounded-pill ms-2">lei/m²</span></h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-info">
              ${fmt(data.rate)}
            </div>
          </div>
        </div>
      </div>

      <!-- Datorat total -->
      <div class="col-sm-6 col-md-3">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="bg-holder bg-card"
               style="background-image:url('https://prium.github.io/falcon/v3.24.0/assets/img/icons/spot-illustrations/corner-3.png');">
          </div>
          <div class="card-body position-relative">
            <h6>Datorat<span class="badge badge-subtle-danger rounded-pill ms-2">total</span></h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-danger">
              ${fmt(data.total_due)}
            </div>
          </div>
        </div>
      </div>

      <!-- Plătit -->
      <div class="col-sm-6 col-md-3">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="bg-holder bg-card"
               style="background-image:url('https://prium.github.io/falcon/v3.24.0/assets/img/icons/spot-illustrations/corner-1.png');">
          </div>
          <div class="card-body position-relative">
            <h6>Plătit<span class="badge badge-subtle-success rounded-pill ms-2">confirmat</span></h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-success">
              ${fmt(data.total_paid)}
            </div>
          </div>
        </div>
      </div>

      <!-- Rămas -->
      <div class="col-sm-6 col-md-3">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="bg-holder bg-card"
               style="background-image:url('https://prium.github.io/falcon/v3.24.0/assets/img/icons/spot-illustrations/corner-4.png');">
          </div>
          <div class="card-body position-relative">
            <h6>Rămas<span class="badge badge-subtle-secondary rounded-pill ms-2">neachitat</span></h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif ${data.balance > 0 ? 'text-danger' : 'text-success'}">
              ${fmt(data.balance)}
            </div>
          </div>
        </div>
      </div>
    </div>
  `);
}


function renderPayments(box, payments) {
  if (!payments.length) {
    $(box).html('<p class="text-muted mb-0">Fără plăți înregistrate.</p>');
    return;
  }

  const rows = payments.map(p => `
    <tr>
      <td>[${p.project_id}] ${p.project_title ?? ''}</td>
      <td>${Number(p.amount).toLocaleString('ro-RO')}</td>
      <td>${p.currency}</td>
      <td>${p.note ? p.note : '-'}</td>
      <td>${new Date(p.created_at).toLocaleString('ro-RO')}</td>
    </tr>
  `).join('');

  $(box).html(`
    <div class="accordion" id="paymentsAccordion">
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingPayments">
          <button class="accordion-button collapsed" type="button"
                  data-bs-toggle="collapse" data-bs-target="#collapsePayments"
                  aria-expanded="false" aria-controls="collapsePayments">
            Istoric plăți (${payments.length})
          </button>
        </h2>
        <div id="collapsePayments" class="accordion-collapse collapse"
             aria-labelledby="headingPayments" data-bs-parent="#paymentsAccordion">
          <div class="accordion-body p-0">
            <div class="table-responsive">
              <table class="table-elegant align-middle mb-0">
                <thead>
                  <tr>
                    <th>Proiect</th>
                    <th>Sumă</th>
                    <th>Monedă</th>
                    <th>Notă</th>
                    <th>Creat la</th>
                  </tr>
                </thead>
                <tbody>${rows}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  `);
}


function refreshEmployee(employeeId) {
  $.getJSON(`${apiBase}/get-employee-summary.php`, { employee_id: employeeId }, function(resp) {
    console.log("Răspuns:", resp);
    renderSummary('#employee-summary', resp);
    renderPayments('#employee-payments', resp.payments || []);
  });
}

$(document).on('click', '#btn-open-payment', function(){
  $('#pm_employee_id').val(employeeId);
  $('#pm_amount').val('');
  $('#pm_currency').val('USD');
  $('#pm_note').val('');
  $('#pm_feedback').empty();

  // proiecte pentru select
  $.getJSON(`${apiBase}/list-employee-projects.php`, { employee_id: employeeId }, function(rows){
    const $sel = $('#pm_project_id').empty().append('<option value="">Alege proiect</option>');
    (rows || []).forEach(r => $sel.append(`<option value="${r.id}">[${r.id}], ${r.title} - ${r.client_name}</option>`));
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
  });
});

$('#paymentForm').on('submit', function (e) {
  e.preventDefault();

  const fd = new FormData(this);
  $('#pm_feedback').html('<span class="text-info">Se salvează...</span>');

  $.ajax({
    url: `${apiBase}/add-payment.php`,
    type: 'POST',
    data: fd,
    contentType: false,
    processData: false,
    dataType: 'json',

    success: function (r) {
      if (r.success) {
        $('#pm_feedback').html('<span class="text-success">✅ Plată salvată!</span>');
        const empId = $('#pm_employee_id').val();
        refreshEmployee(empId);
        setTimeout(() => {
          bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
        }, 800);
      } else {
        $('#pm_feedback').html(`<span class="text-danger">Eroare: ${r.error || 'necunoscută'}</span>`);
      }
    },
    error: function (xhr, status, err) {
      console.error("❌ [4] Eroare rețea:", status, err);
      console.log("Răspuns complet:", xhr.responseText);
      $('#pm_feedback').html('<span class="text-danger">❌ Eroare rețea (verifică consola)</span>');
    }
  });
});

// La încărcare, dacă ai deja un employee curent:
$(function(){
  const employeeIdFromPage = $('#btn-open-payment').data('employee-id');
  if (employeeIdFromPage) refreshEmployee(employeeIdFromPage);
});

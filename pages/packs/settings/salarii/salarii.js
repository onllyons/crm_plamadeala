const apiBase = '/crm/pages/packs/settings/salarii';
const hasAdminAccess = (typeof USER_LEVEL === 'undefined') ? true : Number(USER_LEVEL) === 0;

let projectMap = {};

const params = new URLSearchParams(window.location.search);
const slug = params.get('slug');

let activeEmployeeId = 0;
if (slug) {
  const parts = slug.split('-');
  const maybeId = parseInt(parts[parts.length - 1], 10);
  if (!Number.isNaN(maybeId)) activeEmployeeId = maybeId;
}

if (!activeEmployeeId && typeof globalLucratorId !== 'undefined' && Number(globalLucratorId) > 0) {
  activeEmployeeId = Number(globalLucratorId);
}

if (!activeEmployeeId && typeof window.employeeId !== 'undefined' && Number(window.employeeId) > 0) {
  activeEmployeeId = Number(window.employeeId);
}

if (activeEmployeeId) {
  refreshEmployee(activeEmployeeId);
}

function fmt(n) {
  return (Math.round((Number(n) || 0) * 100) / 100).toLocaleString('ro-RO');
}

function paymentTypeLabel(type) {
  switch ((type || 'project').toLowerCase()) {
    case 'advance': return 'Avans';
    case 'bonus': return 'Bonus';
    case 'extra': return 'Extra';
    default: return 'Proiect';
  }
}

function renderSummary(box, data) {
  if (!hasAdminAccess) {
    $(box).html('');
    return;
  }

  $(box).html(`
    <div class="row g-3 mb-3">
      <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="card-body position-relative">
            <h6>Tarif <span class="badge badge-subtle-info rounded-pill ms-2">euro/m²</span></h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-info">${fmt(data.rate)}</div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="card-body position-relative">
            <h6>Datorat proiecte</h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-danger">${fmt(data.total_due)}</div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="card-body position-relative">
            <h6>Plătit proiecte</h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-success">${fmt(data.total_paid_projects)}</div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="card-body position-relative">
            <h6>Plăți extra</h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-warning">${fmt(data.total_paid_extras)}</div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="card-body position-relative">
            <h6>Total primit</h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-primary">${fmt(data.total_paid)}</div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card overflow-hidden border-0 shadow-sm" style="min-width:12rem; border-radius:1rem;">
          <div class="card-body position-relative">
            <h6>Rămas proiecte</h6>
            <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif ${(Number(data.balance) > 0) ? 'text-danger' : 'text-success'}">${fmt(data.balance)}</div>
          </div>
        </div>
      </div>
    </div>
  `);
}

function renderPayments(box, payments) {
  if (!hasAdminAccess) {
    $(box).html('');
    return;
  }

  if (!payments.length) {
    $(box).html('<p class="text-muted mb-0">Fără plăți înregistrate.</p>');
    return;
  }

  const rows = payments.map(p => {
    const projectCell = p.project_id ? `[${p.project_id}] ${p.project_title ?? ''}` : '<span class="text-muted">Fără proiect</span>';
    return `
      <tr>
        <td>${paymentTypeLabel(p.payment_type)}</td>
        <td>${projectCell}</td>
        <td>${Number(p.amount || 0).toLocaleString('ro-RO')}</td>
        <td>${p.currency || '-'}</td>
        <td>${p.note ? p.note : '-'}</td>
        <td>${new Date(p.created_at).toLocaleString('ro-RO')}</td>
      </tr>
    `;
  }).join('');

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
                    <th>Tip</th>
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

function refreshEmployee(id) {
  $.getJSON(`${apiBase}/get-employee-summary.php`, { employee_id: id }, function(resp) {
    if (!resp || !resp.success) return;
    renderSummary('#employee-summary', resp);
    renderPayments('#employee-payments', resp.payments || []);
  });
}

function populateProjectSelect(rows) {
  projectMap = {};
  const $sel = $('#pm_project_id').empty().append('<option value="">Alege proiect</option>');
  (rows || []).forEach(r => {
    projectMap[String(r.id)] = r;
    const label = `[${r.id}] ${r.title} - ${r.client_name || ''} | rămas: ${fmt(r.remaining)}`;
    $sel.append(`<option value="${r.id}">${label}</option>`);
  });
}

function updateProjectLimitHint() {
  const type = ($('#pm_payment_type').val() || 'project').toLowerCase();
  const isProject = type === 'project';

  $('#pm_limit_hint').text('');
  $('#pm_amount').removeAttr('max');

  if (!isProject) return;

  const projectId = $('#pm_project_id').val();
  const row = projectMap[String(projectId)];
  if (!row) return;

  const remaining = Number(row.remaining || 0);
  const due = Number(row.due_total || 0);
  const paid = Number(row.paid_total || 0);

  $('#pm_limit_hint').text(`Datorat: ${fmt(due)} | Plătit: ${fmt(paid)} | Disponibil: ${fmt(remaining)}`);
  $('#pm_amount').attr('max', remaining.toFixed(2));
}

function updatePaymentTypeUI() {
  const type = ($('#pm_payment_type').val() || 'project').toLowerCase();
  const isProject = type === 'project';

  $('#pm_project_wrap').toggle(isProject);
  $('#pm_project_id').prop('required', isProject);
  $('#pm_note').prop('required', false);

  if (!isProject) {
    $('#pm_project_id').val('');
    $('#pm_limit_hint').text('Plată extra: proiectul nu este obligatoriu.');
    $('#pm_amount').removeAttr('max');
  } else {
    updateProjectLimitHint();
  }
}

$(document).on('change', '#pm_payment_type', updatePaymentTypeUI);
$(document).on('change', '#pm_project_id', updateProjectLimitHint);

$(document).on('click', '#btn-open-payment', function() {
  if (!hasAdminAccess) return;

  $('#pm_employee_id').val(activeEmployeeId);
  $('#pm_payment_type').val('project');
  $('#pm_project_id').val('');
  $('#pm_amount').val('');
  $('#pm_currency').val('EUR');
  $('#pm_note').val('');
  $('#pm_feedback').empty();

  $.getJSON(`${apiBase}/list-employee-projects.php`, { employee_id: activeEmployeeId }, function(rows) {
    populateProjectSelect(rows || []);
    updatePaymentTypeUI();
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
  });
});

$('#paymentForm').on('submit', function(e) {
  e.preventDefault();

  const type = ($('#pm_payment_type').val() || 'project').toLowerCase();
  const amount = Number($('#pm_amount').val() || 0);

  if (type === 'project') {
    const projectId = $('#pm_project_id').val();
    const row = projectMap[String(projectId)];

    if (!row) {
      $('#pm_feedback').html('<span class="text-danger">Selectează un proiect valid.</span>');
      return;
    }

    const remaining = Number(row.remaining || 0);
    if (amount > remaining) {
      $('#pm_feedback').html(`<span class="text-danger">Suma depășește disponibilul (${fmt(remaining)}).</span>`);
      return;
    }
  }

  const fd = new FormData(this);
  $('#pm_feedback').html('<span class="text-info">Se salvează...</span>');

  $.ajax({
    url: `${apiBase}/add-payment.php`,
    type: 'POST',
    data: fd,
    contentType: false,
    processData: false,
    dataType: 'json',
    success: function(r) {
      if (r && r.success) {
        $('#pm_feedback').html('<span class="text-success">Plată salvată.</span>');
        const empId = $('#pm_employee_id').val();
        refreshEmployee(empId);
        setTimeout(() => {
          const instance = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
          if (instance) instance.hide();
        }, 800);
      } else {
        const err = (r && r.error) ? r.error : 'necunoscută';
        if (err === 'amount_exceeds_remaining') {
          const rem = (r && typeof r.remaining !== 'undefined') ? fmt(r.remaining) : '0';
          $('#pm_feedback').html(`<span class="text-danger">Suma depășește restul disponibil (${rem}).</span>`);
        } else {
          $('#pm_feedback').html(`<span class="text-danger">Eroare: ${err}</span>`);
        }
      }
    },
    error: function(xhr, status, err) {
      console.error('Eroare rețea:', status, err);
      console.log('Răspuns complet:', xhr.responseText);
      $('#pm_feedback').html('<span class="text-danger">Eroare rețea (vezi consola).</span>');
    }
  });
});

$(function() {
  const employeeIdFromPage = $('#btn-open-payment').data('employee-id');
  if (employeeIdFromPage) refreshEmployee(employeeIdFromPage);
});

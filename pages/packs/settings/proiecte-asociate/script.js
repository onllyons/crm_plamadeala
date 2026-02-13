document.addEventListener("DOMContentLoaded", () => {

  fetch(`/crm/pages/packs/settings/proiecte-asociate/server-projects.php?employee_id=${globalLucratorId}`)
    .then(res => res.json())
    .then(projectsData => {
      const container = document.getElementById("projects_container");
      if (!container) return;

      container.innerHTML = "";

      const emp = projectsData.employee;
      const grouped = projectsData.projects_grouped || {};
      const months = Object.keys(grouped);

      if (!months.length) {
        container.innerHTML = `
          <div class="col-12 text-center text-muted py-4">
            <i class="fas fa-folder-open fa-2x mb-2"></i><br>
            Niciun proiect asociat acestui angajat.
          </div>`;
        return;
      }

      months.forEach((month, index) => {
        const [y, m] = month.split("-");
        const date = month === "Fără dată" ? null : new Date(y, m - 1);
        const monthName = month === "Fără dată"
          ? "Fără dată"
          : date.toLocaleString("ro-RO", { month: "long", year: "numeric" });
        const mtClass = index === 0 ? "" : "mt-2";

        container.innerHTML += `
          <div class="col-12">
            <h5 class="fw-bold ${mtClass} mb-2 text-primary text-capitalize section-title">${monthName}</h5>
          </div>
        `;

        grouped[month].forEach(p => {
          const totalEmp = p.sum_employee || 0;
          const totalPaid = p.payments && p.payments.length
            ? p.payments.reduce((sum, pay) => sum + Number(pay.amount || 0), 0)
            : 0;
          const remaining = totalEmp - totalPaid;

          // 🔹 Afișează proiectul complet pentru toți
          container.innerHTML += `
            <div class="col-12 col-md-6 col-lg-4">
              <div class="card h-100 project-card">
                <div class="card-body p-0">
                  <div class="d-flex justify-content-between align-items-start mb-2 project-header">
                    <a href="/crm/pages/client-page.php?slug=${p.client_slug}" class="text-decoration-none">
                      <h6 class="fw-semibold mb-0">
                        <i class="fas fa-folder-open me-1"></i>#${p.id}: ${p.title || '—'}
                      </h6>
                    </a>
                    <span class="badge badge-soft rounded-pill">${p.stage || '-'}</span>
                  </div>

                  <ul class="list-unstyled small project-meta mb-3">
                    <li><span class="kv"><i class="fas fa-user me-1 text-secondary"></i>Client:</span>
                      <a href="/crm/pages/client-page.php?slug=${p.client_slug}" class="text-decoration-none">
                        [${p.client_id}] ${p.client_name || '-'}
                      </a>
                    </li>
                    <li><span class="kv"><i class="fas fa-ruler-combined me-1 text-secondary"></i>Suprafață:</span> ${p.surface || '-'}</li>
                    <li><span class="kv"><i class="far fa-calendar-check me-1 text-secondary"></i>Data primire:</span> ${p.date_received || '-'}</li>
                    <li><span class="kv"><i class="far fa-calendar me-1 text-secondary"></i>Data tehnică:</span> ${p.date_technical || '-'}</li>
                    <li><span class="kv"><i class="fas fa-cube me-1 text-secondary"></i>Data 3D:</span> ${p.date_3d || '-'}</li>
                    <li><span class="kv"><i class="fas fa-hourglass-end me-1 text-secondary"></i>Deadline:</span> ${p.date_deadline || '-'}</li>
                    <li><span class="kv"><i class="fas fa-users me-1 text-secondary"></i>Proiectanți:</span> ${p.employees || '-'}</li>
                    <li><span class="kv"><i class="far fa-clock me-1 text-secondary"></i>Creat la:</span> ${p.created_at || '-'}</li>
                  </ul>

                  <div class="payment-summary">
                    <div class="summary-card total">
                      <div class="label">Datorat</div>
                      <div class="value text-danger">${totalEmp.toLocaleString('ro-RO')} €</div>
                    </div>
                    <div class="summary-card paid">
                      <div class="label">Plătit</div>
                      <div class="value text-success">${totalPaid.toLocaleString('ro-RO')} €</div>
                    </div>
                    <div class="summary-card remain">
                      <div class="label">Rămas</div>
                      <div class="value ${remaining > 0 ? 'text-danger' : 'text-success'}">
                        ${remaining.toLocaleString('ro-RO')} €
                      </div>
                    </div>
                  </div>

                  ${p.payments && p.payments.length ? `
                    <div class="accordion mt-3" id="accordion-${p.id}">
                      <div class="accordion-item border-0">
                        <h2 id="heading-${p.id}">
                          <button class="btn btn-acordion" type="button"
                                  data-bs-toggle="collapse"
                                  data-bs-target="#collapse-${p.id}"
                                  aria-expanded="false"
                                  aria-controls="collapse-${p.id}">
                            Istoric achitări (${p.payments.length})
                          </button>
                        </h2>
                        <div id="collapse-${p.id}" class="accordion-collapse collapse"
                             aria-labelledby="heading-${p.id}"
                             data-bs-parent="#accordion-${p.id}">
                          <div class="accordion-body py-2 px-3">
                            <ul class="list-unstyled small mb-0 payments">
                              ${p.payments.map(pay => `
                                <li>${pay.amount} ${pay.currency}
                                  <span class="text-muted">(${new Date(pay.created_at).toLocaleDateString("ro-RO")})</span>
                                  ${pay.note ? ` - <em>${pay.note}</em>` : ""}
                                </li>
                              `).join("")}
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  ` : `<div class="small text-muted mt-2">Fără achitări</div>`}
                </div>
              </div>
            </div>
          `;
        });
      });
    })
    .catch(err => console.error("Eroare fetch proiecte:", err));
});

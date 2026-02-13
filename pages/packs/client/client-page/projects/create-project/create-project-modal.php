<div class="modal fade" id="new-project" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-dialog-pacient">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Proiect nou</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="form-new-project" autocomplete="off">
          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label">Titlu proiect</label>
              <input name="title" type="text" class="form-control" placeholder="Ex: Apartament Eminescu 12" />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Etapă curentă</label>
              <select name="stage" class="form-select">
                <option value="">Selectează...</option>
                <option value="Contractare">Contractare</option>
                <option value="Colectare informații">Colectare informații</option>
                <option value="Proiect tehnic">Proiect tehnic</option>
                <option value="Vizualizare 3D">Vizualizare 3D</option>
                <option value="Întărire proiect">Întărire proiect</option>
                <option value="Finalizat">Finalizat</option>
                <option value="Realizare mobilier">Realizare mobilier</option>
              </select>
            </div>
            <div class="col-md-6" style="display: flex">
              <div class="col">
                <label class="form-label">Proiectanți</label>
                <select multiple id="multiple_employees" class="form-control" data-allow-clear="1" placeholder="Nume angajat / echipă" name="employees[]">
                  <?php
                  $result = $conMain->query("SELECT `id`, `last_name_first_name`, `pret_m2` FROM `angajati`");
                  while ($row = $result->fetch_assoc()) {
                      $value = "[{$row['id']}] - {$row['last_name_first_name']}";
                      $value1 = "{$row['last_name_first_name']}";
                      $pret = "{$row['pret_m2']}";
                      echo "<option value=\"{$value}\" data-pret=\"{$pret}\">{$value1}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="d-flex justify-content-center align-items-end">
                <button class="form-cancel bg-0 pakfryyv730 proj1" type="button"
                  data-bs-toggle="collapse" data-bs-target="#infoDivNew">
                  <i class="fa-solid fa-dollar-sign"></i>
                </button>
              </div>
            </div>
          </div>

          <div id="infoDivNew" class="collapse mt-2">
            info...
          </div>

          <div class="row mb-3">
            <div class="col-md-3">
              <label class="form-label">Suprafață (m²)</label>
              <input name="surface" type="text" class="form-control" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Preț / m²</label>
              <input name="price_per_m2" type="text" class="form-control" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Valută</label>
              <select name="currency" class="form-select">
                <option value="EUR">EUR</option>
                <option value="LEI">LEI</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Preț total</label>
              <p id="total_price" class="mt-2 mb-0 fw-bold">0</p>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Avans</label>
              <input name="advance" type="text" class="form-control" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Restanţă</label>
              <input name="remainder" type="text" class="form-control" />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Data preluării proiectului</label>
              <input name="date_received" type="date" class="form-control" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Data prezentării schitelor tehnice</label>
              <input name="date_technical" type="date" class="form-control" />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Data prezentării proiectului 3D</label>
              <input name="date_3d" type="date" class="form-control" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Termen limită (deadline)</label>
              <input name="date_deadline" type="date" class="form-control" />
            </div>
          </div>

          <div class="modal-footer-pacient">
              <button type="button" class="form-cancel bg-0 pakfryyv730" data-bs-dismiss="modal">Cancel</button>
              <button class="form-accept" type="submit"><span>Save</span></button>
          </div>
                    
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.querySelector('input[name="surface"]').addEventListener('input', calcTotal);
document.querySelector('input[name="price_per_m2"]').addEventListener('input', calcTotal);

function calcTotal() {
  const s = parseFloat(document.querySelector('input[name="surface"]').value) || 0;
  const p = parseFloat(document.querySelector('input[name="price_per_m2"]').value) || 0;
  document.getElementById('total_price').innerText = (s * p).toFixed(2);
}
</script>


















<div class="modal fade" id="edit-project" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-dialog-pacient">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editare proiect</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="form-edit-project" autocomplete="off">
          <input type="hidden" name="id" id="edit_project_id" />

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label">Titlu proiect</label>
              <input name="title" id="edit_title" type="text" class="form-control" />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Etapă curentă</label>
              <select name="stage" id="edit_stage" class="form-select">
                <option value="">Selectează...</option>
                <option value="Contractare">Contractare</option>
                <option value="Colectare informații">Colectare informații</option>
                <option value="Proiect tehnic">Proiect tehnic</option>
                <option value="Vizualizare 3D">Vizualizare 3D</option>
                <option value="Întărire proiect">Întărire proiect</option>
                <option value="Finalizat">Finalizat</option>
                <option value="Realizare mobilier">Realizare mobilier</option>
              </select>
            </div>
            <div class="col-md-6" style="display: flex">
              <div class="col">
                <label class="form-label">Proiectanți</label>
                <select multiple id="edit_employees_select" class="form-control" data-allow-clear="1" placeholder="Nume angajat / echipă" name="employees[]">
                  <?php
                  $result = $conMain->query("SELECT `id`, `last_name_first_name`, `pret_m2` FROM `angajati`");
                  while ($row = $result->fetch_assoc()) {
                      $value = "[{$row['id']}] - {$row['last_name_first_name']}";
                      $value1 = "{$row['last_name_first_name']}";
                      $pret = "{$row['pret_m2']}";
                      echo "<option value=\"{$value}\" data-pret=\"{$pret}\">{$value1}</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="d-flex justify-content-center align-items-end">
                <button class="form-cancel bg-0 pakfryyv730 proj1" type="button"
                  data-bs-toggle="collapse" data-bs-target="#infoDivEdit">
                  <i class="fa-solid fa-dollar-sign"></i>
                </button>
              </div>
            </div>
          </div>

          <div id="infoDivEdit" class="collapse mt-2">
            info edit...
          </div>

          <div class="row mb-3">
            <div class="col-md-3">
              <label class="form-label">Suprafață (m²)</label>
              <input name="surface" id="edit_surface" type="text" class="form-control" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Preț / m²</label>
              <input name="price_per_m2" id="edit_price_per_m2" type="text" class="form-control" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Valută</label>
              <select name="currency" id="edit_currency" class="form-select">
                <option value="EUR">EUR</option>
                <option value="LEI">LEI</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Preț total</label>
              <p id="edit_total_price" class="mt-2 mb-0 fw-bold">0</p>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Avans</label>
              <input name="advance" id="edit_advance" type="text" class="form-control" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Restanţă</label>
              <input name="remainder" id="edit_remainder" type="text" class="form-control" />
            </div>
          </div>


          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Data preluării proiectului</label>
              <input name="date_received" id="edit_date_received" type="date" class="form-control" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Data prezentării schitelor tehnice</label>
              <input name="date_technical" id="edit_date_technical" type="date" class="form-control" />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Data prezentării proiectului 3D</label>
              <input name="date_3d" id="edit_date_3d" type="date" class="form-control" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Termen limită (deadline)</label>
              <input name="date_deadline" id="edit_date_deadline" type="date" class="form-control" />
            </div>
          </div>

          <div class="modal-footer-pacient">
            <button type="button" class="form-cancel bg-0 pakfryyv730" data-bs-dismiss="modal">Cancel</button>
            <button class="form-accept" type="submit"><span>Save changes</span></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.querySelector('#edit_surface').addEventListener('input', editCalcTotal);
document.querySelector('#edit_price_per_m2').addEventListener('input', editCalcTotal);

function editCalcTotal() {
  const s = parseFloat(document.querySelector('#edit_surface').value) || 0;
  const p = parseFloat(document.querySelector('#edit_price_per_m2').value) || 0;
  document.getElementById('edit_total_price').innerText = (s * p).toFixed(2);
}
</script>


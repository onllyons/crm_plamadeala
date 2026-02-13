<?php

   require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
   checkAuth();

   function getSearchU($type = 'nume')
   {
     global $conMain;
     $query = mysqli_query($conMain, "SELECT DISTINCT {$type} FROM clienti");
     $text = '';
     while ($row = mysqli_fetch_assoc($query)) {
       $text .= "<option value='{$row[$type]}'>{$row[$type]}</option>";
     }
     return $text;
   }
   ?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
   <head>
      <?php include '../assets/components/links.php' ?>
      <?php include '../assets/components/style-datatables.php' ?>
      <link href="/crm/pages/packs/angajati/style.css" rel="stylesheet" type="text/css" />
   </head>
   <body>
      <main class="main" id="top">
         <div class="container" data-layout="container">
            <?php include '../assets/components/nav-left.php' ?>
            <div class="content">
               <?php include '../assets/components/nav-top.php' ?>
               <div class="card h-md-100 ecommerce-card-min-width">
                  <div class="card-body d-flex flex-column justify-content-end">
                     <h1 class="h1-title">Cartela Client</h1>

                     <div class="d-flex">

                        <?php
                         $user_level = isset($_SESSION["crm_user"]["level"]) ? (int)$_SESSION["crm_user"]["level"] : 0;
                         $canSeeAdminMenu = $user_level === 0;
                         ?>

                        <?php if ($canSeeAdminMenu): ?>

                        <button class="form-accept btn-aufrage" data-bs-toggle="modal" data-bs-target="#modal-new-lines-upload_file">
                           <span>
                              Înregistrare nouă
                           </span>
                        </button>
                        <?php endif; ?>


                        <button id="btnSortingPartOne" class="form-cancel bg-1 ms-1 btn-sort"><i class="fa-solid fa-gears"></i></button>
                        <button id="btnSortingPartTwo" class="form-cancel bg-1 ms-1 btn-sort" style="display: none;"><i class="fa-solid fa-ellipsis"></i></button>
                        <button id="btnSortingPartThree" class="form-cancel bg-1 ms-1 btn-sort" style="display: none;"><i class="fa-solid fa-xmark"></i></button>
                     </div>
                     
                    <?php 

                      $filtersPartOne = [ 
                          'adresa_client' => 'Adresă'
                      ];
                      

                      $filtersPartTwo = [ 
                          'phone_number' => 'Număr de telefon' 
                      ]; 
                    ?>
               

                     <div class="flex-fustify-content mt-3" id="sortingPartOne" style="display: none;">
                        <div class="li-sorting-table-a">
                           <p class="m-0">Sort by date</p>
                           <input class="js-daterangepicker form-control" id="daterange" />
                        </div>
                        <?php foreach ($filtersPartOne as $field =>
                           $label): ?>
                        <div class="li-sorting-table-a">
                           <p class="m-0"><?= $label ?></p>
                           <select multiple id="multiple_<?= $field ?>" data-allow-clear="1">
                              <option value="">
                                 Select
                                 <?= strtolower($label) ?>
                              </option>
                              <?= getSearchU($field) ?>
                           </select>
                        </div>
                        <?php endforeach; ?>
                     </div>
                     <div class="flex-fustify-content" id="sortingPartTwo" style="display: none;">
                        <?php foreach ($filtersPartTwo as $field =>
                           $label): ?>
                        <div class="li-sorting-table-a">
                           <p class="m-0"><?= $label ?></p>
                           <select multiple id="multiple_<?= $field ?>" data-allow-clear="1">
                              <option value="">
                                 Select
                                 <?= strtolower($label) ?>
                              </option>
                              <?= getSearchU($field) ?>
                           </select>
                        </div>
                        <?php endforeach; ?>
                     </div>
                     <br />
                     <table id="table-upload_file" class="table display" style="width: 100%;">
                        <thead>
                           <th>ID</th>
                           <th>Nume Prenume</th>
                           <th>Adresă</th>
                           <th>Telefon</th>
                           <th>E-mail</th>
                           <th>Nr de identitate</th>
                           <th>Data</th>
                           <th>*</th>
                        </thead>
                        <tbody></tbody>
                     </table>
                     <!-- Modal edit-->
                     <div class="modal fade" id="modal-edit-lines-upload_file" tabindex="-1" aria-labelledby="label-a-upload_file" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-dialog-pacient">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h5 class="modal-title" id="label-a-upload_file">Editați înregistrarea</h5>
                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body modal-body-pacient">
                                 <form id="update-lines-upload_file" autocomplete="off">
                                    <input type="hidden" name="id" id="id" />
                                    <input type="hidden" name="trid" id="trid" />
                                    <h5 class="mb-3">Date personale</h5>
                                    <div class="row mb-3">
                                       <div class="col-md-6">
                                          <label class="form-label">Nume Prenume</label>
                                          <input autocomplete="off" placeholder="Nume Prenume" type="text" class="form-control" name="last_name_first_name" id="edit_last_name_first_name"/>
                                       </div>
                                       <div class="col-md-6">
                                          <label class="form-label">Adresă</label>
                                          <input autocomplete="off" placeholder="Adresa" type="text" class="form-control" name="adresa_client" id="edit_adresa_client"/>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-md-6">
                                          <label class="form-label">E-mail al clientului</label>
                                          <input autocomplete="off" type="text" class="form-control" name="user_email_field" id="edit_user_email_field" placeholder="E-mail al clientului" />
                                       </div>
                                       <div class="col-md-6">
                                          <label class="form-label">Număr de telefon</label>
                                          <input autocomplete="off" type="text" class="form-control" name="phone_number" id="edit_phone_number" placeholder="Număr de telefon" />
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-md-6">
                                          <label class="form-label">Numar de identitate</label>
                                          <input autocomplete="off" placeholder="umar de identitate" type="text" class="form-control" name="nr_identitate" id="edit_nr_identitate"/>
                                       </div>
                                       <div class="col-md-6">
                                          <label class="form-label">Data înregistrării</label>
                                          <input autocomplete="off" type="date" class="form-control" name="dateAdded" id="edit_dateAdded" />
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
                     <!-- Modal add row -->
                     <div class="modal fade" id="modal-new-lines-upload_file" tabindex="-1" aria-labelledby="label-e-upload_file" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-dialog-pacient">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h5 class="modal-title" id="label-e-upload_file">Înregistrare nouă</h5>
                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body modal-body-pacient">
                                 <form id="new-lines-upload_file" action="" autocomplete="off">
                                    <h5 class="mb-3">Date personale</h5>
                                    <div class="row mb-3">
                                       <div class="col-md-6">
                                          <label class="form-label">Nume Prenume</label>
                                          <input autocomplete="off" placeholder="Nume Prenume" type="text" class="form-control" name="last_name_first_name" id="last_name_first_name"/>
                                       </div>
                                       <div class="col-md-6">
                                          <label class="form-label">Adresa</label>
                                          <input autocomplete="off" placeholder="Adresa" type="text" class="form-control" name="adresa_client" id="adresa_client"/>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-md-6">
                                          <label class="form-label">E-mail al clientului</label>
                                          <input autocomplete="off" placeholder="E-mail al clientului" type="text" class="form-control" name="user_email_field" id="user_email_field"/>
                                       </div>
                                       <div class="col-md-6">
                                          <label class="form-label">Număr de telefon</label>
                                          <input autocomplete="off" placeholder="Număr de telefon" type="text" class="form-control" name="phone_number" id="phone_number"/>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-md-6">
                                          <label class="form-label">Numar de identitate</label>
                                          <input autocomplete="off" placeholder="umar de identitate" type="text" class="form-control" name="nr_identitate" id="nr_identitate"/>
                                       </div>
                                       <div class="col-md-6">
                                          <label class="form-label">Data înregistrării</label>
                                          <input autocomplete="off" type="date" class="form-control" name="dateAdded" id="dateAdded" value="<?php echo date('Y-m-d'); ?>" />
                                       </div>
                                    </div>

                                    <div class="modal-footer-pacient">
                                       <button type="button" class="form-cancel bg-0 pakfryyv730" data-bs-dismiss="modal">Cancel</button>
                                       <button id="create-cards" class="form-accept" type="submit"><span>Save</span></button>
                                    </div>
                                 </form>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <?php include '../assets/components/footer.php' ?>
            </div>
         </div>
      </main>
      <?php include '../assets/components/off-canvas-design.php' ?>
      <?php include '../assets/components/scripts.php' ?>
      <?php include '../assets/components/script-datatables.php' ?>
      <script src="/crm/pages/packs/client/script.js" type="text/javascript"></script>
      <script type="text/javascript">
         const btnSortingPartOne = document.querySelector("#btnSortingPartOne");
         const btnSortingPartTwo = document.querySelector("#btnSortingPartTwo");
         const btnSortingPartThree = document.querySelector("#btnSortingPartThree");
         const sortingPartOne = document.querySelector("#sortingPartOne");
         const sortingPartTwo = document.querySelector("#sortingPartTwo");
         
         // Adăugăm evenimentul de click pentru butonul btnSortingPartOne
         btnSortingPartOne.addEventListener("click", () => {
             sortingPartOne.style.display = "flex"; // Afișăm div-ul #sortingPartOne
             btnSortingPartTwo.style.display = "inline-block"; // Afișăm butonul #btnSortingPartTwo
             sortingPartTwo.style.display = "none"; // Ascundem div-ul #sortingPartTwo
             btnSortingPartThree.style.display = "inline-block"; // Afișăm butonul #btnSortingPartTwo
         });
         
         // Adăugăm evenimentul de click pentru butonul btnSortingPartTwo
         btnSortingPartTwo.addEventListener("click", () => {
             sortingPartTwo.style.display = "flex"; // Afișăm div-ul #sortingPartTwo
             btnSortingPartThree.style.display = "inline-block"; // Afișăm butonul #btnSortingPartThree
         });
         
         // Adăugăm evenimentul de click pentru butonul btnSortingPartThree
         btnSortingPartThree.addEventListener("click", () => {
             sortingPartOne.style.display = "none"; // Ascundem div-ul #sortingPartOne
             sortingPartTwo.style.display = "none"; // Ascundem div-ul #sortingPartTwo
             btnSortingPartTwo.style.display = "none"; // Ascundem butonul #btnSortingPartTwo
             btnSortingPartThree.style.display = "none"; // Ascundem butonul #btnSortingPartThree
         });
      </script>
   </body>
</html>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php";
checkAuth();

if (!isset($_SESSION['crm_user']) || (int)$_SESSION['crm_user']['level'] !== 0) {
    header("Location: /crm/pages/index.php");
    exit;
}
?>

<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Proiecte</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.rtl.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
        <!-- multiselect -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
        <!-- style design form blog -->
        <link rel="stylesheet" type="text/css" href="style.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.rtl.min.css" />
    </head>
    <body>
        <div id="upload-loader" class="upload-loader">
            <div class="loader-box">
                <div class="circle"></div>
                <div class="progress-text">0%</div>
            </div>
        </div>

        <div class="flex-content-center">

            <form id="create-blog-content" enctype="multipart/form-data" autocomplete="off">
                <div class="centered-content">
                    <div class="d-flex">
                        <div class="col-sm">
                            <h1 class="mb-3">Creați un proiect <a style="font-size: 15px;" href="../view-cards/view-blog.php">(vizualizați proiectele)</a></h1>
                        </div>
                        <div>
                            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fa-solid fa-gear"></i></button>
                        </div>
                    </div>
                    <input required type="text" class="form-control v--vr--1" placeholder="Titlul pagenii" id="blog-title" />

                    <select class="form-select v--vr--1" id="blog-lang">
                        <option value="ro" selected>Română</option>
                        <option value="ru">Русский</option>
                        <option value="en">English</option>
                    </select>

                    <input type="text" class="form-control v--vr--1 absolute-none" placeholder="Hashtag-urile pe pagină [ #tag, #tag2 ]" id="blog-top-tag" />
                    <input type="text" class="form-control v--vr--1 absolute-none" placeholder="Autor [Studiospacedesign, Dan, Seba, etc...]" id="blog-author" />
                    <input required type="text" class="form-control v--vr--1" placeholder="Url pentru pagină" id="blog-url" />

                    <select class="form-select" id="blog-category" data-placeholder="Categoria">
                        
                    </select>
                    <label class="drop-container">
                        <span class="drop-title">Drop files here</span>or
                        <input required type="file" accept="image/*" title="Blog image" class="drop--image" id="blog-image" />
                    </label>

                   
                </div>
                <textarea required id="blog-content" name="blog-content"></textarea>
                <button type="submit" class="btn-create" id="createBlogContent">create project</button>
            </form>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-body">
                    <div class="row-item">
                      
                        <div class="item-size">
                            <div class="active-content" id="form-add-data">
                                <h4>Block Addition and Management Form</h4>
                                <select class="form-select" id="settings-lang">
                                    <option value="ro" selected>Română</option>
                                    <option value="ru">Русский</option>
                                    <option value="en">English</option>
                                </select>


                            <form action="settings-blog/save.php" id="form">
                            <div class="mb-3">
                                <label for="managementForm" class="form-label">Where do we add a new row?</label>
                                <select class="form-select" name="managementForm">
                                  <option value="category">For menu</option>
                                  <!-- <option value="hashtag">For hashtag</option> -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="managementTitle" class="form-label">Create a unique title</label>
                                <input type="text" class="form-control" name="managementTitle" />
                            </div>
                        
                            <button type="button" class="btn btn-primary" id="btnSubmit">Submit</button>
                        </form>
                        </div>

                        <div class="hidden-content" id="form-edit-data" style="display: none;">
                                <h4>Edit and Management Form</h4>
                            <form action="settings-blog/update.php" id="edit-form">
                            <input type="hidden" name="id" />

                            <div class="form-group">
                                <label for="managementForm" class="form-label">Settings</label>
                                <select class="form-select" name="managementForm">
                                  <option value="category">category</option>
                                  <option value="hashtag">hashtag</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="managementTitle">Title</label>
                                <input class="form-control" type="text" name="managementTitle" />
                            </div>

                            <button type="button" class="btn btn-primary" id="btnUpdateSubmit">Update</button>
                        </form>
                        </div>
                        </div>
                     

                    <div class="col-md-6">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">add menu</button>
  </li>
  <!-- <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">add hashtag</button>
  </li> -->

</ul>
<div class="tab-content" id="pills-tabContent">
  <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
      <div id="employees-list"></div>
  </div>
  <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
      <div id="employees-list1"></div>
  </div>
</div>


                        
                        
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        <!-- multiselect2 script js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <!-- toastr alert -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script type="text/javascript" src="ajaxhalder-blog.js"></script>
        <script src="settings-blog/scripts.js"></script>
    </body>
</html>

// Seteaza optiunile pentru toastr
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
};
$("#blog-content").summernote({
    placeholder: "",
    tabsize: 2,
    height: 500,
    toolbar: [
        ["style", ["undo", "redo", "style", "ul", "ol", "paragraph", "bold", "italic", "misc", "underline", "strikethrough", "superscript", "subscript", "color"]],
        ["view-compilation", ["hr", "clear", "table", "link", "picture", "video", "codeview", "help"]],
    ],
});
// multiselect2 
$(document).ready(function () {

  $("#blog-category").select2({
    theme: "bootstrap-5",
    width: $(this).data("width") ? $(this).data("width") : $(this).hasClass("w-100") ? "100%" : "style",
    placeholder: $(this).data("placeholder"),
    closeOnSelect: false,
    minimumResultsForSearch: -1
  });
});
$(document).ready(function() {
  // Preia ID-ul blogului din parametrii URL
  const urlParams = new URLSearchParams(window.location.search);
  const blogID = urlParams.get('id');
const lang = urlParams.get('lang') || "ro";


  $("#blog-lang").val(lang);
  $.ajax({
    url: 'edit-blog-server.php',
    type: 'GET',
    dataType: 'json',
    data: { blogID: blogID, lang: lang },
    success: function(response) {
      $('#blog-title').val(response.blogTitle);
      $('#blog-blogTopTag').val(response.blogTopTag);
      $('#blog-blogAuthor').val(response.blogAuthor);
      $('#blog-url').val(response.blogURL);
      $('#blog-category').val(response.blogCategory).trigger('change');



      response.blogContent = response.blogContent.replace(/&lt;/g, "<");
      response.blogContent = response.blogContent.replace(/&gt;/g, ">");
      response.blogContent = response.blogContent.replace(/&quot;/g, "\"");
      response.blogContent = response.blogContent.replace(/&amp;/g, "&");
      $('#blog-content').summernote('code', response.blogContent);
    },
    error: function(xhr, status, error) {
      // Afișează un mesaj de eroare dacă solicitarea a eșuat
      toastr.error('Failed to load blog content.');
    }
  });
});


$(document).ready(function() {
  // Preia ID-ul blogului din parametrii URL
  const urlParams = new URLSearchParams(window.location.search);
const lang = urlParams.get("lang") || "ro";
$("#blog-lang").val(lang);


  const blogID = urlParams.get('id');
  // Asculta evenimentul de submit al formularului
  $(document).on('click', '#idbtn', function(event) {
    event.preventDefault();

    // Colecteaza datele introduse in formular
    const blogTitle = $('#blog-title').val();
    const blogTopTag = $('#blog-blogTopTag').val();
    const blogAuthor = $('#blog-blogAuthor').val();
    const blogURL = $('#blog-url').val();
    const blogCategory = $('#blog-category').val();
    const blogImage = $('#blog-image')[0].files[0];
    const blogContent = $('#blog-content').summernote('code');

    // Creeaza un obiect FormData pentru a putea trimite datele ca si fisier
    const formData = new FormData();
    formData.append('blogID', blogID);
    formData.append('blogTitle', blogTitle);
    formData.append('blogTopTag', blogTopTag);
    formData.append('blogAuthor', blogAuthor);
    formData.append('blogURL', blogURL);
    formData.append('blogCategory', blogCategory);
    formData.append('blogImage', blogImage);
    formData.append('blogContent', blogContent);
    formData.append('lang', lang);

    $("#upload-loader").addClass("active");
    $(".progress-text").text("0%");

    // Trimite datele prin AJAX
    $.ajax({
      xhr: function () {
          let xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener("progress", function (e) {
              if (e.lengthComputable) {
                  let percent = Math.round((e.loaded / e.total) * 100);
                  $(".progress-text").text(percent + "%");
              }
          });
          return xhr;
      },
      url: 'update-server.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        $(".progress-text").text("100%");
        setTimeout(() => {
            $("#upload-loader").removeClass("active");
        }, 500);
        toastr.success('Blog updated successfully.');
      },
      error: function(xhr, status, error) {
        $("#upload-loader").removeClass("active");
        toastr.error('Failed to update blog.');
      }
    });
  });
});

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
// textarea for blog summernote
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

// Trimite formularul prin AJAX cand se face submit
$(document).on('click', '#createBlogContent', function(event) {
  event.preventDefault();
  // Verifica daca toate campurile sunt completate
  if ($('#blog-title').val() === '' || 
      $('#blog-url').val() === '' || 
      $('#blog-image').val() === '' || 
      $('#blog-category').val() === '' || 
      $('#blog-content').val() === '') 
  {
    toastr.error('One or more inputs are incomplete, i.e., there are empty spaces without text. In this case, the blog cannot be created. Please fill in all the fields to avoid errors.');
    return;
  }

if ($('#blog-url').val().match(/\s/)) {
  toastr.error('There should not be any spaces in the web URL you are trying to enter. However, you can use hyphens instead.');
  return;
}


  // Aduna informatiile din campurile formularului
  var blogTitle = $('#blog-title').val();
  var blogTopTag = $('#blog-top-tag').val();
  var blogAuthor = $('#blog-author').val();
  var blogUrl = $('#blog-url').val();
  var blogImage = $('#blog-image')[0].files[0];
  var blogCategory = $('#blog-category').val();
  var blogContent = $('#blog-content').summernote('code');

$("#upload-loader").addClass("active");
$(".progress-text").text("0%");


  // Creeaza un obiect FormData pentru a trimite informatiile prin AJAX
  var formData = new FormData();
  formData.append('blogLang', $('#blog-lang').val());
  formData.append('blogTitle', blogTitle);
  formData.append('blogTopTag', blogTopTag);
  formData.append('blogAuthor', blogAuthor);
  formData.append('blogUrl', blogUrl);
  formData.append('blogImage', blogImage);
  formData.append('blogCategory', blogCategory);
  formData.append('blogContent', blogContent);

  // Verifica daca fisierul este o imagine
  // var allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
  // var fileType = blogImage.type.split('/')[1].toLowerCase();
  // if (!allowedTypes.some((type) => fileType.endsWith(type))) {
  //   toastr.error('Error: The uploaded file is not an image.');
  //   return;
  // }

  // Verifica daca dimensiunea fisierului este prea mare
  var maxFileSize = 5 * 1024 * 1024;
  if (blogImage.size > maxFileSize) {
    toastr.error('Error: File size is too large.');
    return;
  }

  // Trimite informatiile prin AJAX
  $.ajax({
    xhr: function() {
        let xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(e) {
            if (e.lengthComputable) {
                let percent = Math.round((e.loaded / e.total) * 100);

                if (percent >= 100) {
                    $(".progress-text").text("Processing…");
                } else {
                    $(".progress-text").text(percent + "%");
                }
            }
        });
        return xhr;
    },
    url: 'generate-blog.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    processData: false,
    contentType: false,
    success: function(response) {

      $(".progress-text").text("100%");

      setTimeout(() => {
          $("#upload-loader").removeClass("active");
      }, 500);

      if (response.success) {
        toastr.success(response.message);
      } else {
        toastr.error(response.message);
      }

    },
    error: function(response) {
      $("#upload-loader").removeClass("active");
      toastr.error("There was an error executing the statement");
    }
  });
});

// transmitem valoarea de la titlu la url
$(document).on('keyup', '#blog-title', function() {
  var title = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-');
  $('#blog-url').val(title);
});

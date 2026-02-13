$(document).ready(function() {
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

    function loadBlogs() {
        $.ajax({
            url: 'view-blog-server.php',
            type: 'GET',
            data: { lang: $("#blog-lang").val() }, 
            dataType: 'json',

            success: function(response) {
                if (response.error) {
                    $('#viewBlogCards').html('<p>' + response.error + '</p>');
                    return;
                }

                var cardsHtml = '';
                $.each(response, function(index, post) {
                    cardsHtml += ''
                    + '<div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-4">'
                    + '  <article class="card h-100 border-0 shadow-sm position-relative">'
                    + '    <a target="_blank" href="#" class="text-decoration-none">'
                    + '      <img src="/web-site/packs/view-blog/images/' + post.blogImage + '" alt="' + (post.blogTitle || '') + '"'
                    + '           class="card-img-top img-fluid" style="height: 350px; object-fit: cover;" loading="lazy">'
                    + '    </a>'
                    + '    <div class="card-body">'
                    + '      <h5 class="card-title mb-2">'
                    + '        <a href="#" class="stretched-link text-decoration-none text-dark">'
                    +            post.blogTitle +
                    '           </a>'
                    + '      </h5>'
                    + '    </div>'
                    + '    <div class="card-footer bg-transparent border-0 pt-0 pb-3 position-relative" style="z-index:2;">'
                    + '      <div class="d-flex gap-2">'
                    + '        <a href="/crm/pages/packs/generate-blog-pages/edit-blog/edit-index.php?id=' 
                    + post.id 
                    + '&lang=' + $("#blog-lang").val() 
                    + '" class="btn btn-primary btn-sm flex-fill">Edit</a>'

                    + '        <a href="#" class="btn btn-primary btn-sm flex-fill" data-id="' + post.id + '">Delete</a>'
                    + '      </div>'
                    + '    </div>'
                    + '  </article>'
                    + '</div>';
                });

                $('#viewBlogCards').html(cardsHtml);
            }
        });
    }

    // Load on page open
    loadBlogs();

    // Change language
    $("#blog-lang").on("change", function() {
        loadBlogs();
    });

    // Delete
    $('#viewBlogCards').on('click', '.btn-primary[data-id]', function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        if (confirm('Sigur doriți să ștergeți acest rând?')) {
            $.ajax({
                url: 'delete-blog-server.php',
                type: 'POST',
                data: {
                    id: id,
                    lang: $("#blog-lang").val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('[data-id="' + id + '"]').closest('.card').remove();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('A apărut o eroare la ștergerea rândului.');
                }
            });
        }
    });

});

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
// display blog
var blogsPerPage = 10;
var currentPage = 1;

$.ajax({
  url: 'view-blog-server.php',
  type: 'GET',
  dataType: 'json',
  success: function(response) {
    if (response.error) {
      $('#viewBlogCards').html('<p>' + response.error + '</p>');
    } else {
      var totalPages = Math.ceil(response.length / blogsPerPage);
      var firstBlogIndex = (currentPage - 1) * blogsPerPage;
      var lastBlogIndex = firstBlogIndex + blogsPerPage;
      if (lastBlogIndex > response.length) {
        lastBlogIndex = response.length;
      }
      var cardsHtml = '';
      for (var i = firstBlogIndex; i < lastBlogIndex; i++) {
        var post = response[i];
        cardsHtml += '<div class="card w-auto p-3 m-4">';
        cardsHtml += '<div class="card-header"><a href="/snippets/generate-blog-pages/view-blog/store/' + post.blogURL + '">' + post.blogTitle + '</a></div>';
        cardsHtml += '<div class="card-header"><img style="max-width: 7rem;" src="../images-blog/' + post.blogImage + '"></div>';
        cardsHtml += '<p class="card-header">' + post.blogDate + '</p>';
        cardsHtml += '</div>';
      }
      $('#viewBlogCards').html(cardsHtml);
      generatePagination(totalPages, currentPage);
    }
  }
});

function generatePagination(totalPages, currentPage) {
  var pagination = $('.pagination');
  pagination.empty();
  if (currentPage > 1) {
    pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">Previous</a></li>');
  } else {
    pagination.append('<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a></li>');
  }
  for (var i = 1; i <= totalPages; i++) {
    if (i === currentPage) {
      pagination.append('<li class="page-item active" aria-current="page"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
    } else {
      pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
    }
  }
  if (currentPage < totalPages) {
    pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Next</a></li>');
  } else {
    pagination.append('<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a></li>');
  }

  // Adaugă un eveniment de click pentru fiecare buton de paginare
  $('.page-link').click(function(event) {
    event.preventDefault();
    var page = parseInt($(this).data('page'));
    if (page !== currentPage) {
      currentPage = page;
      $.ajax({
        url: 'view-blog-server.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
          var totalPages = Math.ceil(response.length / blogsPerPage);
          var firstBlogIndex = (currentPage - 1) * blogsPerPage;
          var lastBlogIndex = firstBlogIndex + blogsPerPage;
          if (lastBlogIndex > response.length) {
            lastBlogIndex = response.length;
          }
          var cardsHtml = '';
          for (var i = firstBlogIndex; i < lastBlogIndex; i++) {
            var post = response[i];
            cardsHtml += '<div class="card w-auto p-3 m-4">';
            cardsHtml += '<div class="card-header"><a href="/snippets/generate-blog-pages/view-blog/store/' + post.blogURL + '">' + post.blogTitle + '</a></div>';
            cardsHtml += '<div class="card-header"><img style="max-width: 7rem;" src="../images-blog/' + post.blogImage + '"></div>';
            cardsHtml += '</div>';
          }
          $('#viewBlogCards').html(cardsHtml);
          generatePagination(totalPages, currentPage);
        }
      });
    }
  });
}





// sorting btn, afisarea tuturor intrebarilor
var buttonContainer = document.getElementById("button-container");

// Creați un obiect XMLHttpRequest
var xhttp = new XMLHttpRequest();

// Definiți funcția de răspuns pentru obiectul XMLHttpRequest
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        // Extrageți datele din răspunsul obținut de la fișierul PHP
        var data = this.responseText;

        // Adăugați datele în containerul HTML
        buttonContainer.innerHTML = data;
    }
};

// Configurați și trimiteți cererea AJAX către fișierul PHP care generează butoanele
xhttp.open("GET", "sorting-blog-menu.php", true);
xhttp.send();



// la apasare pe buton blogurile se sorteaza
var buttonContainer = document.getElementById("button-container");

// Selectați elementul HTML în care să afișați conținutul sortat
var contentContainer = document.getElementById("content-container");

// Adăugați un ascultător de evenimente pentru toate butoanele cu clasa "sort-button"
$(document).on("click", ".sort-button", function() {
    // Obțineți valoarea atributului "data-category" al butonului apăsat
    var category = $(this).attr("data-category");
    
    // Verificați dacă categoria a fost selectată cu succes
    if (category !== undefined && category !== null) {
        // Creați un obiect XMLHttpRequest
        var xhttp = new XMLHttpRequest();
        
        // Definiți funcția de răspuns pentru obiectul XMLHttpRequest
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // Extrageți datele din răspunsul obținut de la fișierul PHP
                    var response = JSON.parse(this.responseText);

                    // Verificăm dacă răspunsul conține un mesaj de eroare
                    if (response.hasOwnProperty('message')) {
                        // Afișăm mesajul de eroare folosind alert()
                        toastr.error(response.message);
                    } else {
                        // Adăugați datele în containerul HTML
                        contentContainer.innerHTML = response.data;
                    }
                } else {
                    // Afișăm un mesaj de eroare folosind alert()
                    toastr.error("A apărut o eroare la încărcarea conținutului. Vă rugăm să încercați din nou mai târziu.");
                }
            }
        };


        // Configurați și trimiteți cererea AJAX către fișierul PHP care returnează rândurile sortate
        xhttp.open("GET", "sorting-click.php?category=" + category, true);
        xhttp.send();
    } else {
        // Afișați un mesaj de alertă dacă categoria nu a fost selectată
        toastr.error("Selectați o categorie pentru a sorta conținutul.");
    }
});

// Creați un obiect XMLHttpRequest
var xhttp = new XMLHttpRequest();

// Definiți funcția de răspuns pentru obiectul XMLHttpRequest
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        // Extrageți datele din răspunsul obținut de la fișierul PHP
        var data = this.responseText;

        // Adăugați datele în containerul HTML
        buttonContainer.innerHTML = data;
    }
};

// Configurați și trimiteți cererea AJAX către fișierul PHP care generează butoanele
xhttp.open("GET", "sorting-blog-menu.php", true);
xhttp.send();



});





// input search of block cards from the table
const searchForm = document.getElementById('search-form');

// Ascultarea evenimentului de trimitere a formularului de căutare
searchForm.addEventListener('submit', function(e) {
  e.preventDefault(); // Oprirea trimiterii formularului

  // Obținerea valorii căutate din input
  const searchTerm = document.getElementById('search-input').value;

  // Verificarea dacă inputul căutării este gol
  if (searchTerm.trim() === '') {
    toastr.error('Enter a search term.');
    return;
  }

  // Crearea unei cereri AJAX cu metoda GET
  const xhr = new XMLHttpRequest();
  xhr.open('GET', `searchi-blog.php?searchi=${searchTerm}`, true);

  // Adăugarea unui ascultător pentru evenimentul de încărcare a răspunsului
  xhr.onload = function() {
    if (this.status === 200) {
      // Actualizarea conținutului div-ului cu rezultatele căutării
      var response = JSON.parse(this.responseText);
      if (response.message) {
        toastr.error(response.message);
      } else {
        // Afisarea rezultatului căutării
        $('#search-results').show();
      $('#viewBlogCards').hide();
      $('#button-container').hide();
      $('#allBlogPosts').hide();
      $('.pagination').hide();
      $('#cancelSort').show();
        var content = response.content;
        document.getElementById('search-results').innerHTML = content;
      }
    }
  }

  // Trimiterea cererii AJAX
  xhr.send();
});

$(document).on("click", "#cancelSort", function() {
    $('#viewBlogCards').show();
    $('#cancelSort').hide();
    $('#search-results').hide();
    $('.pagination').show();
    $('#button-container').show();
    $('#allBlogPosts').show();
    $('#search-input').val("");
});

$(document).on("click", ".sort-button", function() {
  var innerHtml = $(this).html();
  $('#viewBlogCards').hide();
    $('.pagination').hide();
  $('#content-container').show();

});

$(document).on("click", "#allBlogPosts", function() {
    $('.pagination').show();
  $('#content-container').hide();
    $('#viewBlogCards').show();

});




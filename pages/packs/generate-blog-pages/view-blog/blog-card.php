<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View blog</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet" />
  <style type="text/css">
    .search-hidden {
      display: none;
    }
    img{
      width: 100%;
    }
  </style>
</head>
<body>
<h1 class="mb-3">View all cards</h1>

<form id="search-form" method="GET">
  <input type="text" name="searchi" id="search-input">
  <input type="button" value="cancel" id="cancelSort" style="display: none;">
  <button type="submit" id="searchiForm">Cauta</button>
</form>

<div id="search-results" class="row"></div>


<!-- <button class="sort-button btn btn-primary me-2">All</button> -->
<div class="row">
  <button class="btn btn-primary col-sm-2 me-2" id="allBlogPosts">All blogs</button>
  <div class='w-auto' id="button-container"></div>
</div>
<div id="content-container" class="row"></div>


<div id="viewBlogCards" class="row">

</div>

<div class="pagination"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript" src="view-blog-ajax.js"></script>
</body>
</html>
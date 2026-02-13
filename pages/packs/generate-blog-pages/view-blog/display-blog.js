// preiați valoarea parametrului url din URL
const blogURL = window.location.pathname.split('/').pop();
console.log(blogURL)
// Face o solicitare AJAX către fișierul PHP de tip endpoint pentru a obține conținutul blogului
$.ajax({
  url: '/snippets/generate-blog-pages/view-blog/display-blog-server.php',
  type: 'GET',
  dataType: 'json',
  data: { url: blogURL },
  success: function(response) {
    $('#blog-title').text(response.blogTitle);
    $('#blog-url').text(response.blogURL);
    $('#blog-category').text(response.blogCategory);
    $('#blog-image').attr('src', '/snippets/generate-blog-pages/images-blog/' + response.blogImage);

    // inlocuieste virgula cu spatiu si imparte cuvintele in tag span
    const hashtags = response.blogHashtag.replace(/,/g, " ").split(' ');
    let hashtagsHtml = '';
    hashtags.map((hashtag) => {
      hashtagsHtml += `<span>${hashtag}</span> `;
    });
    $('#blog-hashtag').html(hashtagsHtml);


    response.blogContent = response.blogContent.replace(/&lt;/g, "<");
    response.blogContent = response.blogContent.replace(/&gt;/g, ">");
    response.blogContent = response.blogContent.replace(/&quot;/g, "\"");
    response.blogContent = response.blogContent.replace(/&amp;/g, "&");
    $('#blog-content').html(response.blogContent);
  },
  error: function(xhr, status, error) {
    // Afișează un mesaj de eroare dacă solicitarea a eșuat
    toastr.error('Failed to load blog content.');
  }
});

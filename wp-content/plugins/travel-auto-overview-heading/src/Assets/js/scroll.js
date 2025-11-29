document.addEventListener('DOMContentLoaded', function () {
  var toc = document.querySelector('.travel-toc-box');
  if (!toc) return;

  // Fallback: si algún link no encuentra destino, asignar id al heading con el mismo texto
  var links = toc.querySelectorAll('a[href^="#"]');
  links.forEach(function(link){
    var hash = link.getAttribute('href');
    if (!hash) return;
    var target = document.querySelector(hash);
    if (!target) {
      var txt = link.textContent.trim();
      var candidates = Array.from(document.querySelectorAll('h2, h3, h4, h5, h6'));
      var found = candidates.find(function(h){
        return h.textContent.trim() === txt;
      });
      if (found) {
        var id = hash.replace(/^#/, '');
        if (!found.id) found.id = id;
      }
    }
  });

  toc.addEventListener('click', function (e) {
    var link = e.target.closest('a[href^="#"]');
    if (!link) return;
    var target = document.querySelector(link.getAttribute('href'));
    if (!target) return;
    e.preventDefault();
    var headerOffset = 80;
    var y = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;
    window.scrollTo({ top: y < 0 ? 0 : y, behavior: 'smooth' });
  });
});

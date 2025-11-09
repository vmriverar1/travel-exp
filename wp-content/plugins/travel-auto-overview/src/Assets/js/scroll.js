document.addEventListener('DOMContentLoaded', function () {
  var container = document.querySelector('.travel-toc-box');
  if (!container) return;

  container.addEventListener('click', function (e) {
    var link = e.target.closest('a[href^="#"]');
    if (!link) return;
    var target = document.querySelector(link.getAttribute('href'));
    if (!target) return;
    e.preventDefault();

    var headerOffset = 80; // ajusta si tienes header fijo
    var elementPosition = target.getBoundingClientRect().top + window.pageYOffset;
    var offsetPosition = elementPosition - headerOffset;

    window.scrollTo({
      top: Math.max(offsetPosition, 0),
      behavior: 'smooth'
    });
  });
});

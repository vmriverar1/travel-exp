/**
 * Funcionalidad de favoritos para Travel Packages
 */
document.addEventListener('DOMContentLoaded', () => {
  const favoriteButtons = document.querySelectorAll('.travel-package-card .favorite-btn');

  favoriteButtons.forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      e.stopPropagation();
      btn.classList.toggle('active');
    });
  });
});

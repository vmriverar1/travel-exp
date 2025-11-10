document.addEventListener('DOMContentLoaded', () => {
  const swiperEl = document.querySelector('.tcp-swiper');
  if (!swiperEl) return;

  const swiper = new Swiper(swiperEl, {
    slidesPerView: 1,
    spaceBetween: 16,
    centeredSlides: true,
    pagination: { el: '.swiper-pagination__mobile', clickable: true },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
  });

  // Recalcular cuando cambia el tamaño de ventana
  window.addEventListener('resize', () => swiper.update());
});

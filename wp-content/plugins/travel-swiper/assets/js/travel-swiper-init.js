(function () {
  function initSwiper(container) {
    if (container._tsb && container._tsb.swiper) return;

    // número de filas
    let rows = 1;
    if (container.classList.contains('swiper-rows-2')) rows = 2;
    if (container.classList.contains('swiper-rows-3')) rows = 3;

    // slidesPerView dinámico (desde ACF o 1 por defecto)
    const slidesMobile = parseFloat(container.dataset.slidesMobile || 1);

    const el = container.querySelector('.tsb-swiper');
    if (!el) return;

    // Buscar el paginador tanto dentro como fuera del swiper
    const paginationEl =
      el.querySelector('.swiper-pagination__mobile') ||
      container.querySelector('.swiper-pagination__mobile');

    // Buscar flechas dentro o fuera
    const nextEl =
      el.querySelector('.swiper-button-next') ||
      container.querySelector('.swiper-button-next');

    const prevEl =
      el.querySelector('.swiper-button-prev') ||
      container.querySelector('.swiper-button-prev');

    // Inicializar Swiper
    container._tsb = {
      swiper: new Swiper(el, {
        slidesPerView: slidesMobile,
        spaceBetween: 16,
        loop: false,
        watchOverflow: false,
        allowTouchMove: true,
        speed: 500,
        grid: { rows, fill: 'row' },

        pagination: {
          el: paginationEl,
          clickable: true,
        },

        navigation: {
          nextEl: nextEl,
          prevEl: prevEl,
        },

        breakpoints: {
          1024: {
            slidesPerView: 'auto',
            allowTouchMove: false,
            loop: false,
            grid: undefined,
          },
        },
      }),
    };
  }

  function destroySwiper(container) {
    if (container._tsb && container._tsb.swiper) {
      container._tsb.swiper.destroy(true, true);
      container._tsb = null;
    }
  }

  function handle() {
    const isMobile = window.innerWidth < 1024;
    document.querySelectorAll('.travel-swiper-block').forEach((c) => {
      if (isMobile) initSwiper(c);
      else destroySwiper(c);
    });
  }

  document.addEventListener('DOMContentLoaded', handle);
  window.addEventListener('resize', () => {
    clearTimeout(window._tsbR);
    window._tsbR = setTimeout(handle, 200);
  });
})();

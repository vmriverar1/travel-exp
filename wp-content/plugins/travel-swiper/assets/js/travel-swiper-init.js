(function () {
  function initSwiper(container) {
    // Evita reinicializar si ya existe
    if (container._tsb && container._tsb.swiper) return;

    // Detectar número de filas desde la clase
    let rows = 1;
    if (container.classList.contains('swiper-rows-2')) rows = 2;
    if (container.classList.contains('swiper-rows-3')) rows = 3;

    const el = container.querySelector('.tsb-swiper');
    if (!el) return;

    container._tsb = {
      swiper: new Swiper(el, {
        slidesPerView: 1, // 1 columna limpia en móvil
        spaceBetween: 16,

        // Claves para que los dots siempre aparezcan
        loop: false,               // fuerza el bucle, siempre hay “páginas”
        watchOverflow: false,     // no ocultar paginación aunque haya pocos slides
        allowTouchMove: true,     // permite deslizar siempre
        speed: 500,

        // activa grid si hay más de una fila
        grid: { rows, fill: 'row' },

        pagination: {
          el: el.querySelector('.swiper-pagination__mobile'), // dentro del mismo swiper
          clickable: true,
        },

        navigation: {
          nextEl: container.querySelector('.swiper-button-next'),
          prevEl: container.querySelector('.swiper-button-prev'),
        },

        breakpoints: {
          1024: {
            slidesPerView: 'auto',
            allowTouchMove: false,
            loop: false,       // desactiva bucle en desktop
            grid: undefined,   // desactiva grid en desktop
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

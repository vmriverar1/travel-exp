(function () {
  // Helper: init/destroy based on viewport
  function initSwiper(container) {
    if (container._tsb && container._tsb.swiper) return; // ya iniciado

    var rows = 1;
    if (container.classList.contains('swiper-rows-2')) rows = 2;
    if (container.classList.contains('swiper-rows-3')) rows = 3;

    var el = container.querySelector('.tsb-swiper');
    if (!el) return;

    container._tsb = {
      swiper: new Swiper(el, {
        slidesPerView: 1.1,
        spaceBetween: 16,
        grid: rows > 1 ? { rows: rows, fill: 'row' } : undefined,
        pagination: {
          el: container.querySelector('.swiper-pagination'),
          clickable: true
        },
        navigation: {
          nextEl: container.querySelector('.swiper-button-next'),
          prevEl: container.querySelector('.swiper-button-prev')
        },
        breakpoints: {
          // En desktop lo "desactivamos" visualmente (se mostrará como grid por CSS)
          1024: {
            slidesPerView: 'auto',
            allowTouchMove: false
          }
        }
      })
    };
  }

  function destroySwiper(container) {
    if (container._tsb && container._tsb.swiper) {
      container._tsb.swiper.destroy(true, true);
      container._tsb = null;
    }
  }

  function handle() {
    var isMobile = window.innerWidth < 1024;
    var blocks = document.querySelectorAll('.travel-swiper-block');
    blocks.forEach(function (c) {
      if (isMobile) initSwiper(c);
      else destroySwiper(c);
    });
  }

  document.addEventListener('DOMContentLoaded', handle);
  window.addEventListener('resize', function () {
    // debounce simple
    clearTimeout(window._tsbResize);
    window._tsbResize = setTimeout(handle, 150);
  });
})();

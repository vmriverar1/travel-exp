(function(){
  document.addEventListener('DOMContentLoaded', () => {
    const swiperEl = document.querySelector('.tcp-swiper');
    if(!swiperEl) return;

    const prev = swiperEl.querySelector('.tcp-prev');
    const next = swiperEl.querySelector('.tcp-next');
    const pagination = swiperEl.querySelector('.tcp-pagination');

    new Swiper(swiperEl, {
      slidesPerView: 1.2,
      spaceBetween: 16,
      pagination: { el: pagination, clickable: true },
      navigation: { nextEl: next, prevEl: prev },
      breakpoints: {
        640: { slidesPerView: 1.3 },
        768: { slidesPerView: 1.5 }
      },
      on: {
        init(swiper){ toggleDisabled(swiper); },
        slideChange(swiper){ toggleDisabled(swiper); }
      }
    });

    function toggleDisabled(swiper){
      prev.classList.toggle('swiper-button-disabled', swiper.isBeginning);
      next.classList.toggle('swiper-button-disabled', swiper.isEnd);
    }
  });
})();

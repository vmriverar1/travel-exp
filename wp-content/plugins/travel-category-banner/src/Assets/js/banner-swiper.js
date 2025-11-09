(function(){
  function init(el){
    const wrap = el.closest('.tcb-slider');
    const prev = wrap.querySelector('.tcb-prev');
    const next = wrap.querySelector('.tcb-next');

    new Swiper(el, {
      slidesPerView: 1,
      spaceBetween: 20,
      navigation: {
        prevEl: prev,
        nextEl: next
      },
      watchOverflow: true,
      breakpoints: {
        0:   { slidesPerView: 1, spaceBetween: 14 },
        640: { slidesPerView: 2, spaceBetween: 16 },
        1024:{ slidesPerView: 3, spaceBetween: 20 }
      },
      on: {
        init: function(swiper){
          toggleDisabled(swiper);
        },
        slideChange: function(swiper){
          toggleDisabled(swiper);
        }
      }
    });

    function toggleDisabled(swiper){
      const { isBeginning, isEnd } = swiper;
      prev.classList.toggle('swiper-button-disabled', isBeginning);
      next.classList.toggle('swiper-button-disabled', isEnd);
    }
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('.tcb-swiper').forEach(init);
  });
})();

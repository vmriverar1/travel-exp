(function(){
  function initSwiper(section){
    if (typeof Swiper === 'undefined') return; // Si la página ya no lo tiene, no hacemos nada
    var el = section.querySelector('.swiper');
    if (!el || el._travel_swiper) return;

    var isMobile = window.matchMedia('(max-width: 768px)').matches;
    if (!isMobile) return;

    var swiper = new Swiper(el, {
      slidesPerView: 1,
      spaceBetween: 16,
      pagination: {
        el: section.querySelector('.travel-lp__pagination'),
        clickable: true
      }
      // Sin flechas: dots only
    });
    el._travel_swiper = swiper;
  }

  function onReady(){
    document.querySelectorAll('.travel-lp').forEach(initSwiper);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', onReady);
  } else {
    onReady();
  }
})();

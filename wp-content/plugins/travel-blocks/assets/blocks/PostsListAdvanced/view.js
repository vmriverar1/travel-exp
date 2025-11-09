/**
 * ==========================================================
 * POSTS LIST ADVANCED (SSR + Swiper por viewport)
 * ==========================================================
 * - Carga Swiper solo si ACF lo activa (desktop o mobile)
 * - Evita inicialización si los controles están off
 * - SSR 100% compatible y rendimiento óptimo
 * ==========================================================
 */
(function(){
  const SWIPER_CDN_JS  = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js';
  const SWIPER_CDN_CSS = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css';
  let loaded = false;

  function loadSwiper(cb){
    if (loaded) return cb();
    const link = document.createElement('link');
    link.rel  = 'stylesheet';
    link.href = SWIPER_CDN_CSS;
    document.head.appendChild(link);

    const s = document.createElement('script');
    s.src = SWIPER_CDN_JS;
    s.async = true;
    s.onload = () => { loaded = true; cb(); };
    document.head.appendChild(s);
  }

  function html(str){
    const t = document.createElement('template');
    t.innerHTML = str.trim();
    return t.content.firstElementChild;
  }

  function init(root){
    const cfgEl = root.querySelector('[data-pla-config]');
    if (!cfgEl) return;

    let cfg = {};
    try { cfg = JSON.parse(cfgEl.getAttribute('data-pla-config')); } catch(e) {}

    const isMobile = window.matchMedia('(max-width: 767px)').matches;
    const enableDesktop = !!cfg.enableDesktopSwiper;
    const enableMobile  = !!cfg.enableMobileSwiper;

    // 🧩 Si ambos están desactivados, salir inmediatamente
    if (!enableDesktop && !enableMobile) return;

    const tplSelector = isMobile
      ? (enableMobile ? 'template[data-mobile-swiper]' : 'template[data-mobile-grid]')
      : (enableDesktop ? 'template[data-desktop-swiper]' : 'template[data-desktop-grid]');

    const tpl   = root.querySelector(tplSelector);
    const mount = root.querySelector('.pla-mount');
    if (!tpl || !mount) return;

    root.classList.add('loading');

    const placeholderCount = isMobile ? cfg.slidesMobile || 1 : cfg.slidesDesktop || 3;
    const grid = document.createElement('div');
    grid.className = 'pla-grid';
    for (let i = 0; i < placeholderCount; i++) {
      const card = document.createElement('div');
      card.className = 'pla-card';
      grid.appendChild(card);
    }
    mount.innerHTML = '';
    mount.appendChild(grid);

    // ⚙️ Si el control Swiper está activo para este viewport
    const shouldInitSwiper = (isMobile && enableMobile) || (!isMobile && enableDesktop);

    setTimeout(() => {
      // Reemplazar el template correcto
      mount.replaceWith(html(tpl.innerHTML));

      // Si Swiper no está habilitado, salir sin cargar librería
      if (!shouldInitSwiper) {
        root.classList.remove('loading');
        return;
      }

      const swiperEl = root.querySelector('.swiper');
      if (!swiperEl) return;

      loadSwiper(() => {
        new Swiper(swiperEl, {
          slidesPerView: isMobile ? cfg.slidesMobile || 1 : cfg.slidesDesktop || 3,
          spaceBetween: 16,
          loop: false,
          pagination: { el: '.swiper-pagination', clickable: true },
          watchOverflow: true,
          on: {
            init: () => {
              root.classList.remove('loading');
              swiperEl.style.opacity = '1';
            }
          }
        });
      });
    }, 300);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.acf-gbr-pla').forEach(init);
  });
})();

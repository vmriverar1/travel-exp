document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('.vtc-tabs button');
  const contents = document.querySelectorAll('.vtc-tab-content');
  const headerTrip = document.querySelector('[data-header-trip]');
  const reviewLink = document.querySelector('.review-link');

  const swipers = {};

  const initSwiper = (container) => {
    if (!container) return;
    const id = container.dataset.tabContent;
    const swiperEl = container.querySelector('.vtc-swiper');

    // Si ya existe, solo refrescamos cuando sea visible
    if (swipers[id]) {
      requestAnimationFrame(() => {
        setTimeout(() => {
          swipers[id].update();
          swipers[id].slideTo(0); // 🧠 forzar reacomodo
        }, 120);
      });
      return;
    }

    // 🔥 Inicializar nuevo Swiper
    swipers[id] = new Swiper(swiperEl, {
      slidesPerView: 4,
      spaceBetween: 24,
      grid: {
        rows: 2,
        fill: 'row',
      },
      loop: true,
      speed: 700,
      autoplay: {
        delay: 4500,
        disableOnInteraction: false,
      },
      navigation: {
        nextEl: swiperEl.querySelector('.swiper-nex'),
        prevEl: swiperEl.querySelector('.swiper-prev'),
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true,
        dynamicMainBullets: 4,
      },

      breakpoints: {
        0: {
          slidesPerView: 1,
          grid: { rows: 1 },
          dynamicBullets: true,
        },
        640: {
          slidesPerView: 2,
          grid: { rows: 1 },
          dynamicBullets: true,
        },
        1024: {
          slidesPerView: 4,
          grid: { rows: 2 },
          dynamicBullets: true,
        },
      },
      observer: true,
      observeParents: true,
    });
  };

  // === Inicializa el primero (TripAdvisor) ===
  const first = document.querySelector('[data-tab-content="trip-advisor"]');
  if (first) initSwiper(first);

  // === Cambio de pestañas ===
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const target = tab.dataset.tab;

      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      contents.forEach(c => {
        const isTarget = c.dataset.tabContent === target;
        c.style.display = isTarget ? 'block' : 'none';

        if (isTarget) {
          // Espera a que el DOM del tab se pinte y luego inicializa o actualiza
          requestAnimationFrame(() => {
            setTimeout(() => initSwiper(c), 150);
          });
        }
      });

      if (headerTrip) headerTrip.style.display = target === 'trip-advisor' ? 'flex' : 'none';
      if (reviewLink) reviewLink.style.display = target === 'trip-advisor' ? 'inline-block' : 'none';
    });
  });

  // === Show more / less ===
  // === Show more / less ===
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('show-more')) {
      const card = e.target.closest('.vtc-card');
      const text = card.querySelector('.review-text');
      const isExpanded = card.classList.toggle('expanded');
      text.classList.toggle('expanded');
      text.classList.toggle('truncated');
      e.target.textContent = isExpanded ? 'Show less' : 'Show more';

      // 🔹 Sincroniza alturas en los slides visibles
      const swiperEl = card.closest('.vtc-swiper');
      if (swiperEl) {
        const visibleCards = swiperEl.querySelectorAll('.swiper-slide-active .vtc-card, .swiper-slide-next .vtc-card, .swiper-slide-prev .vtc-card');
        let maxH = 0;
        visibleCards.forEach(c => { maxH = Math.max(maxH, c.scrollHeight); });
        visibleCards.forEach(c => c.style.height = isExpanded ? `${maxH}px` : '340px');
      }
    }
  });
});

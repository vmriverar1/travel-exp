/**
 * Travel Reviews - Tabs & Swiper
 * @version 7.2 (stable)
 */
(function () {
  'use strict';

  const SELECTORS = {
    container: '#vtc-reviews',
    tabs: '.vtc-tabs button[data-tab]',
    tabContent: '.vtc-tab-content',
    headerTrip: '[data-header-trip]',
    swiper: '.vtc-swiper',
    showMore: '.show-more',
    reviewText: '.review-text',
    card: '.vtc-card'
  };

  const swiperInstances = {};

  /**
   * Inicializa Swiper
   */
  function initSwiper(container) {
    if (!container) return null;

    const tabId = container.dataset.tabContent;
    const swiperEl = container.querySelector(SELECTORS.swiper);
    if (!swiperEl) return null;

    if (swiperInstances[tabId]) {
      swiperInstances[tabId].update();
      return swiperInstances[tabId];
    }

    const config = {
      slidesPerView: 4,
      spaceBetween: 28,
      grid: { rows: 2, fill: 'row' },
      loop: false,
      speed: 600,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
        pauseOnMouseEnter: true
      },
      navigation: {
        nextEl: swiperEl.querySelector('.swiper-nex'),
        prevEl: swiperEl.querySelector('.swiper-prev')
      },
      pagination: {
        el: swiperEl.querySelector('.swiper-pagination'),
        clickable: true,
        dynamicBullets: true,
        dynamicMainBullets: 4
      },
      breakpoints: {
        0: { slidesPerView: 1, grid: { rows: 1 }, spaceBetween: 16 },
        640: { slidesPerView: 2, grid: { rows: 1 }, spaceBetween: 20 },
        1024: { slidesPerView: 4, grid: { rows: 2 }, spaceBetween: 14 }
      },
      observer: true,
      observeParents: true
    };

    swiperInstances[tabId] = new Swiper(swiperEl, config);
    return swiperInstances[tabId];
  }

  /**
   * Mostrar tab
   */
  function showTab(tabId) {
    const container = document.querySelector(SELECTORS.container);
    if (!container) return;

    const tabs = container.querySelectorAll(SELECTORS.tabs);
    const contents = container.querySelectorAll(SELECTORS.tabContent);
    const headerTrip = container.querySelector(SELECTORS.headerTrip);

    tabs.forEach(tab => {
      tab.classList.toggle('active', tab.dataset.tab === tabId);
    });

    contents.forEach(content => {
      const isTarget = content.dataset.tabContent === tabId;
      content.style.display = isTarget ? 'block' : 'none';
      content.classList.toggle('active', isTarget);

      if (isTarget) {
        setTimeout(() => {
          const swiper = initSwiper(content);
          if (swiper) swiper.update();
        }, 100);
      }
    });

    if (headerTrip) {
      headerTrip.style.display = tabId === 'trip-advisor' ? 'flex' : 'none';
    }
  }

  /**
   * Show More – versión anti-saltos v7.2
   */
  function handleShowMore(e) {
    const btn = e.target.closest('.show-more');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    let card = btn.closest('.vtc-card');

    // fallback por seguridad (parrafos insertados por WP)
    if (!card) {
      let node = btn.parentElement;
      while (node && !node.classList?.contains('vtc-card')) {
        node = node.parentElement;
      }
      card = node;
    }
    if (!card) return;

    const text = card.querySelector('.review-text');
    if (!text) return;

    const isExpanded = !card.classList.contains('expanded');

    // Toggle
    card.classList.toggle('expanded', isExpanded);
    text.classList.toggle('expanded', isExpanded);
    text.classList.toggle('truncated', !isExpanded);
    btn.textContent = isExpanded ? 'Show less' : 'Show more';

    // Ajustar card
    card.style.height = isExpanded ? 'auto' : '';
    card.style.maxHeight = isExpanded ? 'none' : '';

    // 🔥 FIX DEFINITIVO PARA GRID (NO SALTOS)
    setTimeout(() => {
      const swiperEl = card.closest('.vtc-swiper');

      if (swiperEl && swiperEl.swiper) {
        const sw = swiperEl.swiper;

        // reconstruye TODO el grid
        sw.updateSlides();
        sw.updateSize();
        sw.updateProgress();
        sw.updateSlidesClasses();
        sw.update();

        // mobile: ajusta altura dinámica
        if (sw.params.grid && sw.params.grid.rows === 1) {
          sw.updateAutoHeight();
        }
      }
    }, 80);
  }

  /**
   * INIT
   */
  function init() {
    const container = document.querySelector(SELECTORS.container);
    if (!container || container.dataset.initialized === 'true') return;
    container.dataset.initialized = 'true';

    container.addEventListener('click', handleTabClick);
    container.addEventListener('click', function (e) {
      if (e.target.closest('.show-more')) handleShowMore(e);
    });

    const firstContent = container.querySelector('[data-tab-content="trip-advisor"]');
    if (firstContent) {
      firstContent.style.display = 'block';
      firstContent.classList.add('active');
      setTimeout(() => initSwiper(firstContent), 50);
    }
  }

  function handleTabClick(e) {
    const tab = e.target.closest('[data-tab]');
    if (!tab) return;
    e.preventDefault();
    showTab(tab.dataset.tab);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else init();

  window.addEventListener('load', init);

})();

/**
 * Sticky Side Menu - Trigger global aside toggle only
 */
(function () {
  'use strict';

  function initStickySideMenus() {
    const menus = document.querySelectorAll('[data-sticky-menu]');

    menus.forEach(menu => {
      const wrapper = menu.closest('.wp-block-travel-sticky-side-menu');
      if (wrapper) {
        const isEditorPreview = wrapper.closest('.block-editor-block-list__layout') !== null;
        if (isEditorPreview) {
          menu.classList.add('is-visible');
          return;
        }

        const offsetTopValue = getComputedStyle(menu).getPropertyValue('--offset-top').trim() || '20vh';
        function handleStickyBehavior() {
          const scrollPosition = window.scrollY;
          let offsetPixels = 0;
          if (offsetTopValue.includes('vh')) {
            offsetPixels = (parseFloat(offsetTopValue) / 100) * window.innerHeight;
          } else if (offsetTopValue.includes('px')) {
            offsetPixels = parseFloat(offsetTopValue);
          }
          if (scrollPosition >= offsetPixels) menu.classList.add('is-visible');
          else menu.classList.remove('is-visible');
        }

        handleStickyBehavior();
        let ticking = false;
        window.addEventListener('scroll', function() {
          if (!ticking) {
            window.requestAnimationFrame(function() {
              handleStickyBehavior();
              ticking = false;
            });
            ticking = true;
          }
        }, { passive: true });
      }

      // === Trigger only the global toggle ===
      const hamburger = menu.querySelector('.sticky-side-menu__hamburger');
      if (!hamburger) return;

      hamburger.addEventListener('click', function(e) {
        e.preventDefault();

        if (typeof window.asideMenuToggle === 'function') {
          console.log('[Sticky] llamando asideMenuToggle global');
          window.asideMenuToggle(hamburger);
        } else {
          console.warn('[Sticky] No se encontró window.asideMenuToggle');
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStickySideMenus);
  } else {
    initStickySideMenus();
  }
})();

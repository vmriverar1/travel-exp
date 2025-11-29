/**
 * Hero Carousel - Editor Padding Fix
 * Transfers padding from WordPress wrapper to inner carousel container
 */

(function () {
  'use strict';

  /**
   * Transfer padding from WordPress wrapper to hero carousel
   */
  function transferPadding() {
    const blocks = document.querySelectorAll('.wp-block-acf-hero-carousel');

    blocks.forEach((block) => {
      const carousel = block.querySelector('.hc-hero-carousel');
      if (!carousel) return;

      // Get computed style from WordPress wrapper
      const blockStyle = window.getComputedStyle(block);

      // Get padding values
      const paddingTop = blockStyle.paddingTop;
      const paddingRight = blockStyle.paddingRight;
      const paddingBottom = blockStyle.paddingBottom;
      const paddingLeft = blockStyle.paddingLeft;

      // Check if any padding exists
      if (paddingTop !== '0px' || paddingRight !== '0px' || paddingBottom !== '0px' || paddingLeft !== '0px') {
        // Apply to carousel
        carousel.style.paddingTop = paddingTop;
        carousel.style.paddingRight = paddingRight;
        carousel.style.paddingBottom = paddingBottom;
        carousel.style.paddingLeft = paddingLeft;

        // Remove from wrapper (with !important to override WordPress inline styles)
        block.style.setProperty('padding-top', '0', 'important');
        block.style.setProperty('padding-right', '0', 'important');
        block.style.setProperty('padding-bottom', '0', 'important');
        block.style.setProperty('padding-left', '0', 'important');
      }
    });
  }

  /**
   * Initialize on DOM ready
   */
  function init() {
    // Initial transfer
    transferPadding();

    // Watch for changes using MutationObserver
    const observer = new MutationObserver((mutations) => {
      let shouldUpdate = false;

      mutations.forEach((mutation) => {
        // Check if it's a style attribute change on a hero carousel block
        if (mutation.type === 'attributes' &&
            mutation.attributeName === 'style' &&
            mutation.target.classList.contains('wp-block-acf-hero-carousel')) {
          shouldUpdate = true;
        }
      });

      if (shouldUpdate) {
        // Use setTimeout to ensure WordPress has finished applying styles
        setTimeout(transferPadding, 50);
      }
    });

    // Observe the editor for changes
    const editorRoot = document.querySelector('.block-editor-writing-flow');
    if (editorRoot) {
      observer.observe(editorRoot, {
        attributes: true,
        attributeFilter: ['style'],
        subtree: true,
      });
    }

    // Also run on ACF block preview render
    if (window.acf) {
      window.acf.addAction('render_block_preview/type=hero-carousel', () => {
        setTimeout(transferPadding, 100);
      });
    }
  }

  // Run when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Also run periodically in case blocks are added dynamically
  setInterval(transferPadding, 1000);
})();

/**
 * Side by Side Cards - Slider Mobile
 * Grid en desktop, slider nativo en mobile
 */

(function() {
  'use strict';

  /**
   * Initialize slider for a specific block instance
   */
  function initSlider(block) {
    const container = block.querySelector('.sbs-container');
    const dots = block.querySelectorAll('.sbs-dot');
    const prevBtn = block.querySelector('.sbs-arrow--prev');
    const nextBtn = block.querySelector('.sbs-arrow--next');
    const cards = block.querySelectorAll('.sbs-card');

    if (!container || cards.length === 0) return;

    let currentIndex = 0;
    let autoplayInterval = null;
    const isMobile = () => window.innerWidth <= 1024;

    // Get settings from data attributes
    const autoplayEnabled = block.dataset.sliderAutoplay === '1';
    const autoplayDelay = parseInt(block.dataset.sliderDelay) || 5000;

    /**
     * Scroll to specific card
     */
    function scrollToCard(index) {
      if (!isMobile()) return;

      const card = cards[index];
      if (card) {
        const containerRect = container.getBoundingClientRect();
        const cardRect = card.getBoundingClientRect();
        const scrollLeft = card.offsetLeft - (containerRect.width / 2) + (cardRect.width / 2);

        container.scrollTo({
          left: scrollLeft,
          behavior: 'smooth'
        });

        currentIndex = index;
        updateDots(index);
      }
    }

    /**
     * Update active dot
     */
    function updateDots(index) {
      dots.forEach((dot, i) => {
        dot.classList.toggle('is-active', i === index);
      });
    }

    /**
     * Go to next card
     */
    function nextCard() {
      const nextIndex = (currentIndex + 1) % cards.length;
      scrollToCard(nextIndex);
    }

    /**
     * Go to previous card
     */
    function prevCard() {
      const prevIndex = (currentIndex - 1 + cards.length) % cards.length;
      scrollToCard(prevIndex);
    }

    /**
     * Start autoplay
     */
    function startAutoplay() {
      if (!autoplayEnabled || !isMobile()) return;

      stopAutoplay(); // Clear any existing interval
      autoplayInterval = setInterval(nextCard, autoplayDelay);
    }

    /**
     * Stop autoplay
     */
    function stopAutoplay() {
      if (autoplayInterval) {
        clearInterval(autoplayInterval);
        autoplayInterval = null;
      }
    }

    /**
     * Detect scroll position and update active dot
     */
    function handleScroll() {
      if (!isMobile()) return;

      const containerRect = container.getBoundingClientRect();
      const containerCenter = containerRect.left + containerRect.width / 2;

      let closestIndex = 0;
      let closestDistance = Infinity;

      cards.forEach((card, index) => {
        const cardRect = card.getBoundingClientRect();
        const cardCenter = cardRect.left + cardRect.width / 2;
        const distance = Math.abs(containerCenter - cardCenter);

        if (distance < closestDistance) {
          closestDistance = distance;
          closestIndex = index;
        }
      });

      if (closestIndex !== currentIndex) {
        currentIndex = closestIndex;
        updateDots(closestIndex);
      }
    }

    /**
     * Event listeners
     */

    // Navigation arrows
    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        prevCard();
        stopAutoplay(); // Pause autoplay on manual interaction
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        nextCard();
        stopAutoplay(); // Pause autoplay on manual interaction
      });
    }

    // Pagination dots
    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        scrollToCard(index);
        stopAutoplay(); // Pause autoplay on manual interaction
      });
    });

    // Scroll detection (throttled)
    let scrollTimeout;
    container.addEventListener('scroll', () => {
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(handleScroll, 100);
    });

    // Touch events - pause autoplay on touch
    container.addEventListener('touchstart', stopAutoplay);

    // Resume autoplay when not interacting (optional)
    container.addEventListener('touchend', () => {
      setTimeout(startAutoplay, 1000);
    });

    // Responsive: reinitialize on resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => {
        if (isMobile()) {
          startAutoplay();
        } else {
          stopAutoplay();
        }
      }, 200);
    });

    // Keyboard navigation (arrows)
    block.addEventListener('keydown', (e) => {
      if (!isMobile()) return;

      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        prevCard();
        stopAutoplay();
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        nextCard();
        stopAutoplay();
      }
    });

    // Initialize autoplay if enabled and mobile
    if (isMobile()) {
      startAutoplay();
    }

    // Initial dot state
    updateDots(0);
  }

  /**
   * Initialize all sliders on page
   */
  function initAllSliders() {
    const blocks = document.querySelectorAll('.sbs-cards');
    blocks.forEach(initSlider);
  }

  /**
   * Run on DOM ready
   */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllSliders);
  } else {
    initAllSliders();
  }

  /**
   * Re-initialize on Gutenberg block changes (editor preview)
   */
  if (window.wp && window.wp.data) {
    let lastBlockCount = 0;

    function checkForNewBlocks() {
      const blocks = document.querySelectorAll('.sbs-cards');
      if (blocks.length !== lastBlockCount) {
        lastBlockCount = blocks.length;
        initAllSliders();
      }
    }

    // Check for new blocks periodically (Gutenberg editor)
    setInterval(checkForNewBlocks, 1000);
  }

})();

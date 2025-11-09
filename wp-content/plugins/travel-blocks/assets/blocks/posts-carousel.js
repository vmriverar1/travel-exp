/**
 * Posts Carousel - Material Design
 * Desktop: Grid 3 columnas (solo CSS)
 * Mobile: Slider con JavaScript
 */

(function () {
  'use strict';

  const MOBILE_BREAKPOINT = 768; // px

  /**
   * Check if viewport is mobile
   */
  function isMobile() {
    return window.innerWidth <= MOBILE_BREAKPOINT;
  }

  /**
   * Initialize all carousels on page
   */
  function initCarousels() {
    const carousels = document.querySelectorAll('.posts-carousel');
    if (!carousels.length) return;

    carousels.forEach((carousel) => {
      // Create carousel instance
      if (!carousel._carouselInstance) {
        carousel._carouselInstance = new PostsCarousel(carousel);
      } else {
        // Update existing instance
        carousel._carouselInstance.update();
      }
    });

    // Initialize favorite buttons (travel variant)
    initFavoriteButtons();
  }

  /**
   * Initialize favorite buttons for travel cards
   */
  function initFavoriteButtons() {
    const favoriteButtons = document.querySelectorAll('.pc-card__favorite');

    favoriteButtons.forEach((button) => {
      if (button._favoriteListener) return;

      button._favoriteListener = (e) => {
        e.preventDefault();
        e.stopPropagation();
        button.classList.toggle('is-active');

        // Optional: Save to localStorage
        const cardIndex = button.closest('.pc-card').dataset.index;
        const blockId = button.closest('.posts-carousel').id;
        const favoriteKey = `favorite_${blockId}_${cardIndex}`;

        if (button.classList.contains('is-active')) {
          localStorage.setItem(favoriteKey, 'true');
        } else {
          localStorage.removeItem(favoriteKey);
        }
      };

      button.addEventListener('click', button._favoriteListener);

      // Restore favorite state from localStorage
      const cardIndex = button.closest('.pc-card').dataset.index;
      const blockId = button.closest('.posts-carousel').id;
      const favoriteKey = `favorite_${blockId}_${cardIndex}`;

      if (localStorage.getItem(favoriteKey) === 'true') {
        button.classList.add('is-active');
      }
    });
  }

  /**
   * PostsCarousel Class
   * Solo activo en mobile
   */
  class PostsCarousel {
    constructor(element) {
      this.carousel = element;
      this.grid = this.carousel.querySelector('.pc-grid');
      this.cards = Array.from(this.carousel.querySelectorAll('.pc-card'));
      this.dots = Array.from(this.carousel.querySelectorAll('.pc-dot'));
      this.prevBtn = this.carousel.querySelector('.pc-arrow--prev');
      this.nextBtn = this.carousel.querySelector('.pc-arrow--next');

      this.currentIndex = 0;
      this.isActive = false;
      this.autoplayTimer = null;
      this.resizeTimer = null;

      // Settings from data attributes
      this.settings = {
        autoplay: this.carousel.dataset.sliderAutoplay === '1',
        delay: parseInt(this.carousel.dataset.sliderDelay) || 5000,
        speed: parseFloat(this.carousel.dataset.sliderSpeed) || 0.3,
      };

      if (!this.grid || !this.cards.length) return;

      this.init();
    }

    /**
     * Initialize carousel
     */
    init() {
      this.setupResize();
      this.update();
    }

    /**
     * Update carousel state based on viewport
     */
    update() {
      if (isMobile()) {
        this.enable();
      } else {
        this.disable();
      }
    }

    /**
     * Enable slider functionality (mobile only)
     */
    enable() {
      if (this.isActive) return;
      this.isActive = true;

      // Reset to first card
      this.currentIndex = 0;

      // Setup navigation
      this.setupNavigation();

      // Setup dots
      this.setupDots();

      // Setup scroll observer
      this.setupScrollObserver();

      // Setup keyboard navigation
      this.setupKeyboard();

      // Setup autoplay
      if (this.settings.autoplay) {
        this.startAutoplay();
        this.setupAutoplayPause();
      }

      // Set initial active states
      this.updateActiveStates();

      // Reset scroll position to start (with slight delay to ensure DOM is ready)
      requestAnimationFrame(() => {
        if (this.grid) {
          this.grid.scrollLeft = 0;
          // Force scroll to first card
          if (this.cards[0]) {
            this.cards[0].scrollIntoView({ block: 'nearest', inline: 'start', behavior: 'auto' });
          }
        }
      });
    }

    /**
     * Disable slider functionality (desktop)
     */
    disable() {
      if (!this.isActive) return;
      this.isActive = false;

      // Stop autoplay
      this.stopAutoplay();

      // Remove observers
      if (this.scrollObserver) {
        this.scrollObserver.disconnect();
        this.scrollObserver = null;
      }

      // Reset to first card
      this.currentIndex = 0;
      this.updateActiveStates();
    }

    /**
     * Setup window resize handler
     */
    setupResize() {
      window.addEventListener('resize', () => {
        clearTimeout(this.resizeTimer);
        this.resizeTimer = setTimeout(() => {
          this.update();
        }, 200);
      });
    }

    /**
     * Setup navigation buttons
     */
    setupNavigation() {
      if (this.prevBtn && !this.prevBtn._pcListener) {
        this.prevBtn._pcListener = () => this.prev();
        this.prevBtn.addEventListener('click', this.prevBtn._pcListener);
      }

      if (this.nextBtn && !this.nextBtn._pcListener) {
        this.nextBtn._pcListener = () => this.next();
        this.nextBtn.addEventListener('click', this.nextBtn._pcListener);
      }

      this.updateNavButtons();
    }

    /**
     * Setup dots navigation
     */
    setupDots() {
      this.dots.forEach((dot, index) => {
        if (!dot._pcListener) {
          dot._pcListener = () => this.goToSlide(index);
          dot.addEventListener('click', dot._pcListener);
        }
      });
    }

    /**
     * Setup IntersectionObserver to update active states
     */
    setupScrollObserver() {
      if (this.scrollObserver) return;

      const options = {
        root: this.grid,
        threshold: 0.6,
      };

      let isInitializing = true;
      // Allow observer to work after a brief moment
      setTimeout(() => { isInitializing = false; }, 100);

      this.scrollObserver = new IntersectionObserver((entries) => {
        // Skip updates during initialization to prevent jumping to last card
        if (isInitializing) return;

        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const index = this.cards.indexOf(entry.target);
            if (index !== -1 && index !== this.currentIndex) {
              this.currentIndex = index;
              this.updateActiveStates();
            }
          }
        });
      }, options);

      this.cards.forEach((card) => this.scrollObserver.observe(card));
    }

    /**
     * Setup keyboard navigation
     */
    setupKeyboard() {
      if (!this.carousel._pcKeyboardListener) {
        this.carousel._pcKeyboardListener = (e) => {
          if (!this.isActive) return;

          if (e.key === 'ArrowLeft') {
            e.preventDefault();
            this.prev();
          } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            this.next();
          }
        };
        this.carousel.addEventListener('keydown', this.carousel._pcKeyboardListener);
      }
    }

    /**
     * Navigate to previous slide
     */
    prev() {
      if (!this.isActive) return;
      const targetIndex = this.currentIndex > 0 ? this.currentIndex - 1 : 0;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to next slide
     */
    next() {
      if (!this.isActive) return;
      const targetIndex =
        this.currentIndex < this.cards.length - 1
          ? this.currentIndex + 1
          : this.cards.length - 1;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to specific slide
     */
    goToSlide(index) {
      if (!this.isActive || index < 0 || index >= this.cards.length || index === this.currentIndex) {
        return;
      }

      const card = this.cards[index];
      if (!card) return;

      // Smooth scroll to card
      card.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
        inline: 'center',
      });

      this.currentIndex = index;
      this.updateActiveStates();
    }

    /**
     * Update active states (dots and cards)
     */
    updateActiveStates() {
      // Update dots
      this.dots.forEach((dot, index) => {
        dot.classList.toggle('is-active', index === this.currentIndex);
        dot.setAttribute('aria-current', index === this.currentIndex ? 'true' : 'false');
      });

      // Update nav buttons
      this.updateNavButtons();
    }

    /**
     * Update navigation buttons state
     */
    updateNavButtons() {
      if (this.prevBtn) {
        this.prevBtn.disabled = this.currentIndex === 0;
      }

      if (this.nextBtn) {
        this.nextBtn.disabled = this.currentIndex === this.cards.length - 1;
      }
    }

    /**
     * Start autoplay
     */
    startAutoplay() {
      if (!this.isActive) return;

      this.autoplayTimer = setInterval(() => {
        // Loop to first slide when reaching the end
        if (this.currentIndex >= this.cards.length - 1) {
          this.goToSlide(0);
        } else {
          this.next();
        }
      }, this.settings.delay);
    }

    /**
     * Stop autoplay
     */
    stopAutoplay() {
      if (this.autoplayTimer) {
        clearInterval(this.autoplayTimer);
        this.autoplayTimer = null;
      }
    }

    /**
     * Reset autoplay
     */
    resetAutoplay() {
      if (!this.settings.autoplay || !this.isActive) return;
      this.stopAutoplay();
      this.startAutoplay();
    }

    /**
     * Setup autoplay pause on hover/focus
     */
    setupAutoplayPause() {
      if (!this.carousel._pcHoverListener) {
        this.carousel._pcHoverListener = () => this.stopAutoplay();
        this.carousel.addEventListener('mouseenter', this.carousel._pcHoverListener);
      }

      if (!this.carousel._pcLeaveListener) {
        this.carousel._pcLeaveListener = () => {
          if (this.isActive) this.startAutoplay();
        };
        this.carousel.addEventListener('mouseleave', this.carousel._pcLeaveListener);
      }

      if (!this.carousel._pcFocusInListener) {
        this.carousel._pcFocusInListener = () => this.stopAutoplay();
        this.carousel.addEventListener('focusin', this.carousel._pcFocusInListener);
      }

      if (!this.carousel._pcFocusOutListener) {
        this.carousel._pcFocusOutListener = () => {
          if (this.isActive) this.startAutoplay();
        };
        this.carousel.addEventListener('focusout', this.carousel._pcFocusOutListener);
      }
    }
  }

  /**
   * Initialize on DOM ready
   */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCarousels);
  } else {
    initCarousels();
  }

  /**
   * Re-initialize on Gutenberg block update
   */
  if (window.acf) {
    window.acf.addAction('render_block_preview', initCarousels);
  }
})();

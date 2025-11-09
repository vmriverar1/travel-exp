/**
 * Flexible Grid Carousel - Native JavaScript Implementation
 * CSS scroll-snap + Vanilla JS navigation
 * Desktop: Grid with cards + text blocks
 * Mobile: Carousel (cards only), text blocks separate
 */

(function () {
  'use strict';

  /**
   * Initialize all carousels on page
   */
  function initCarousels() {
    const carousels = document.querySelectorAll('.fgc-flexible-grid');
    if (!carousels.length) return;

    carousels.forEach((carousel) => {
      new FlexibleGridCarousel(carousel);
    });
  }

  /**
   * FlexibleGridCarousel Class
   */
  class FlexibleGridCarousel {
    constructor(element) {
      this.carousel = element;
      this.itemsWrapper = this.carousel.querySelector('.fgc-items');
      this.allItems = Array.from(this.carousel.querySelectorAll('.fgc-item'));

      // Only track card items for carousel (text blocks are desktop-only in grid)
      this.cardItems = Array.from(this.carousel.querySelectorAll('.fgc-item--card'));

      this.prevBtn = this.carousel.querySelector('.fgc-nav--prev');
      this.nextBtn = this.carousel.querySelector('.fgc-nav--next');
      this.dotsContainer = this.carousel.querySelector('.fgc-dots');
      this.skeleton = this.carousel.querySelector('.fgc-skeleton');

      this.currentIndex = 0;
      this.isAutoplay = this.carousel.dataset.autoplay === 'true';
      this.autoplayDelay = parseInt(this.carousel.dataset.delay) || 5000;
      this.autoplayTimer = null;
      this.isMobile = window.innerWidth < 1024;

      if (!this.itemsWrapper || !this.cardItems.length) return;

      this.init();
    }

    /**
     * Initialize carousel
     */
    init() {
      // Hide skeleton and show carousel
      setTimeout(() => {
        if (this.skeleton) {
          this.skeleton.classList.add('is-hidden');
          setTimeout(() => this.skeleton.remove(), 500);
        }
        this.carousel.classList.add('is-loaded');
      }, 300);

      // Only setup carousel features on mobile
      if (this.isMobile) {
        this.setupNavigation();
        this.createDots();
        this.setupDots();
        this.setupScrollObserver();
        this.setupKeyboard();

        if (this.isAutoplay) {
          this.startAutoplay();
          this.setupAutoplayPause();
        }
      }

      // Handle resize
      this.handleResize();
    }

    /**
     * Create dots dynamically (only for card items)
     */
    createDots() {
      if (!this.dotsContainer) return;

      this.dotsContainer.innerHTML = '';
      this.cardItems.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.className = 'fgc-dot';
        dot.setAttribute('type', 'button');
        dot.setAttribute('aria-label', `Go to card ${index + 1}`);
        dot.setAttribute('aria-current', index === 0 ? 'true' : 'false');
        if (index === 0) dot.classList.add('is-active');
        this.dotsContainer.appendChild(dot);
      });

      this.dots = Array.from(this.dotsContainer.querySelectorAll('.fgc-dot'));
    }

    /**
     * Setup navigation buttons
     */
    setupNavigation() {
      if (this.prevBtn) {
        this.prevBtn.addEventListener('click', () => this.prev());
      }

      if (this.nextBtn) {
        this.nextBtn.addEventListener('click', () => this.next());
      }

      this.updateNavButtons();
    }

    /**
     * Setup dots navigation
     */
    setupDots() {
      if (!this.dots) return;

      this.dots.forEach((dot, index) => {
        dot.addEventListener('click', () => this.goToSlide(index));
      });
    }

    /**
     * Setup IntersectionObserver to update active states
     */
    setupScrollObserver() {
      const options = {
        root: this.itemsWrapper,
        threshold: 0.5,
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const index = this.cardItems.indexOf(entry.target);
            if (index !== -1 && index !== this.currentIndex) {
              this.currentIndex = index;
              this.updateActiveStates();
            }
          }
        });
      }, options);

      this.cardItems.forEach((item) => observer.observe(item));
    }

    /**
     * Setup keyboard navigation
     */
    setupKeyboard() {
      this.carousel.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          this.prev();
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          this.next();
        }
      });
    }

    /**
     * Navigate to previous slide
     */
    prev() {
      if (!this.isMobile) return;

      const targetIndex = this.currentIndex > 0 ? this.currentIndex - 1 : this.cardItems.length - 1;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to next slide
     */
    next() {
      if (!this.isMobile) return;

      const targetIndex = this.currentIndex < this.cardItems.length - 1 ? this.currentIndex + 1 : 0;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to specific slide
     */
    goToSlide(index) {
      if (index < 0 || index >= this.cardItems.length || index === this.currentIndex) return;
      if (!this.isMobile) return;

      const item = this.cardItems[index];
      if (!item) return;

      // Smooth scroll to item
      item.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
        inline: 'center',
      });

      this.currentIndex = index;
      this.updateActiveStates();
    }

    /**
     * Update active states (dots and items)
     */
    updateActiveStates() {
      // Update card items
      this.cardItems.forEach((item, index) => {
        item.classList.toggle('is-active', index === this.currentIndex);
      });

      // Update dots
      if (this.dots) {
        this.dots.forEach((dot, index) => {
          dot.classList.toggle('is-active', index === this.currentIndex);
          dot.setAttribute('aria-current', index === this.currentIndex ? 'true' : 'false');
        });
      }

      // Update nav buttons
      this.updateNavButtons();
    }

    /**
     * Update navigation buttons state
     */
    updateNavButtons() {
      if (!this.isMobile) {
        if (this.prevBtn) this.prevBtn.style.display = 'none';
        if (this.nextBtn) this.nextBtn.style.display = 'none';
        return;
      }

      if (this.prevBtn) {
        this.prevBtn.style.display = 'flex';
        this.prevBtn.disabled = this.currentIndex === 0;
      }

      if (this.nextBtn) {
        this.nextBtn.style.display = 'flex';
        this.nextBtn.disabled = this.currentIndex === this.cardItems.length - 1;
      }
    }

    /**
     * Start autoplay
     */
    startAutoplay() {
      this.autoplayTimer = setInterval(() => {
        this.next();
      }, this.autoplayDelay);
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
      if (!this.isAutoplay) return;
      this.stopAutoplay();
      this.startAutoplay();
    }

    /**
     * Setup autoplay pause on hover/focus
     */
    setupAutoplayPause() {
      this.carousel.addEventListener('mouseenter', () => this.stopAutoplay());
      this.carousel.addEventListener('mouseleave', () => this.startAutoplay());

      this.carousel.addEventListener('focusin', () => this.stopAutoplay());
      this.carousel.addEventListener('focusout', () => this.startAutoplay());
    }

    /**
     * Handle window resize
     */
    handleResize() {
      let resizeTimeout;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
          const wasMobile = this.isMobile;
          this.isMobile = window.innerWidth < 1024;

          if (wasMobile !== this.isMobile) {
            // Recreate dots if switching to mobile
            if (this.isMobile && this.dotsContainer) {
              this.createDots();
              this.setupDots();
            }

            // Update nav buttons visibility
            this.updateNavButtons();

            // Stop/start autoplay based on screen size
            if (this.isAutoplay) {
              if (this.isMobile) {
                this.startAutoplay();
              } else {
                this.stopAutoplay();
              }
            }
          }
        }, 250);
      });
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

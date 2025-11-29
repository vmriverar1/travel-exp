/**
 * Hero Carousel - Native JavaScript Implementation
 * CSS scroll-snap + Vanilla JS navigation
 * Three variations: Overlay, Side Left, Side Right
 * Conditional carousel: only activates if cards > columns
 */

(function () {
  'use strict';

  /**
   * Initialize all carousels on page
   */
  function initCarousels() {
    const carousels = document.querySelectorAll('.hc-hero-carousel');
    if (!carousels.length) return;

    carousels.forEach((carousel) => {
      new HeroCarousel(carousel);
    });
  }

  /**
   * HeroCarousel Class
   */
  class HeroCarousel {
    constructor(element) {
      this.carousel = element;
      this.cardsWrapper = this.carousel.querySelector('.hc-cards');
      this.cards = Array.from(this.carousel.querySelectorAll('.hc-card'));
      this.prevBtn = this.carousel.querySelector('.hc-nav--prev');
      this.nextBtn = this.carousel.querySelector('.hc-nav--next');
      this.dotsContainer = this.carousel.querySelector('.hc-dots');
      this.skeleton = this.carousel.querySelector('.hc-skeleton');

      this.currentIndex = 0;
      this.isAutoplay = this.carousel.dataset.autoplay === 'true';
      this.autoplayDelay = parseInt(this.carousel.dataset.delay) || 5000;
      this.autoplayTimer = null;
      this.isMobile = window.innerWidth < 1024;
      this.isCarousel = this.carousel.dataset.isCarousel === 'true';
      this.columnsDesktop = parseInt(this.carousel.dataset.columns) || 3;

      if (!this.cardsWrapper || !this.cards.length) return;

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

      // Only setup carousel features if it's actually a carousel
      if (this.isCarousel || this.isMobile) {
        this.setupNavigation();
        this.setupScrollObserver();
        this.setupKeyboard();

        if (this.isMobile) {
          this.createDots();
          this.setupDots();
        }

        if (this.isAutoplay && this.isMobile) {
          this.startAutoplay();
          this.setupAutoplayPause();
        }
      }

      // Handle resize
      this.handleResize();
    }

    /**
     * Create dots dynamically
     */
    createDots() {
      if (!this.dotsContainer) return;

      this.dotsContainer.innerHTML = '';
      this.cards.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.className = 'hc-dot';
        dot.setAttribute('type', 'button');
        dot.setAttribute('aria-label', `Go to card ${index + 1}`);
        dot.setAttribute('aria-current', index === 0 ? 'true' : 'false');
        if (index === 0) dot.classList.add('is-active');
        this.dotsContainer.appendChild(dot);
      });

      this.dots = Array.from(this.dotsContainer.querySelectorAll('.hc-dot'));
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
        root: this.cardsWrapper,
        threshold: 0.5,
      };

      const observer = new IntersectionObserver((entries) => {
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

      this.cards.forEach((card) => observer.observe(card));
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
      // Only work if carousel mode or mobile
      if (!this.isCarousel && !this.isMobile) return;

      const targetIndex = this.currentIndex > 0 ? this.currentIndex - 1 : this.cards.length - 1;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to next slide
     */
    next() {
      // Only work if carousel mode or mobile
      if (!this.isCarousel && !this.isMobile) return;

      const targetIndex = this.currentIndex < this.cards.length - 1 ? this.currentIndex + 1 : 0;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to specific slide
     */
    goToSlide(index) {
      if (index < 0 || index >= this.cards.length || index === this.currentIndex) return;

      // Only work if carousel mode or mobile
      if (!this.isCarousel && !this.isMobile) return;

      const card = this.cards[index];
      if (!card) return;

      // Calculate scroll position relative to cards container
      const cardRect = card.getBoundingClientRect();
      const containerRect = this.cardsWrapper.getBoundingClientRect();
      const scrollLeft = this.cardsWrapper.scrollLeft;

      // Calculate target scroll position to center the card
      const targetScroll = scrollLeft + (cardRect.left - containerRect.left) - (containerRect.width / 2) + (cardRect.width / 2);

      // Smooth scroll to card within the container
      this.cardsWrapper.scrollTo({
        left: targetScroll,
        behavior: 'smooth',
      });

      this.currentIndex = index;
      this.updateActiveStates();
    }

    /**
     * Update active states (dots and cards)
     */
    updateActiveStates() {
      // Update cards
      this.cards.forEach((card, index) => {
        card.classList.toggle('is-active', index === this.currentIndex);
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
      // Hide navigation if not carousel mode (desktop) and not mobile
      if (!this.isCarousel && !this.isMobile) {
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
        this.nextBtn.disabled = this.currentIndex === this.cards.length - 1;
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

/**
 * Posts Carousel - Native JavaScript Implementation
 * CSS scroll-snap + Vanilla JS navigation
 * No dependencies
 */

(function () {
  'use strict';

  /**
   * Initialize all carousels on page
   */
  function initCarousels() {
    const carousels = document.querySelectorAll('.posts-carousel');
    if (!carousels.length) return;

    carousels.forEach((carousel) => {
      new PostsCarousel(carousel);
    });
  }

  /**
   * PostsCarousel Class
   */
  class PostsCarousel {
    constructor(element) {
      this.carousel = element;
      this.slidesWrapper = this.carousel.querySelector('.pc-slides');
      this.slides = Array.from(this.carousel.querySelectorAll('.pc-slide'));
      this.dots = Array.from(this.carousel.querySelectorAll('.pc-dot'));
      this.prevBtn = this.carousel.querySelector('.pc-nav--prev');
      this.nextBtn = this.carousel.querySelector('.pc-nav--next');
      this.loader = this.carousel.querySelector('.pc-loader');
      this.carouselWrapper = this.carousel.querySelector('.pc-carousel');

      this.currentIndex = 0;
      this.isAutoplay = this.carousel.dataset.autoplay === '1';
      this.autoplayDelay = parseInt(this.carousel.dataset.delay) || 5000;
      this.autoplayTimer = null;

      if (!this.slidesWrapper || !this.slides.length) return;

      this.init();
    }

    /**
     * Initialize carousel
     */
    init() {
      // Hide loader and show carousel
      setTimeout(() => {
        if (this.loader) {
          this.loader.classList.add('is-hidden');
          setTimeout(() => this.loader.remove(), 500);
        }
        if (this.carouselWrapper) {
          this.carouselWrapper.classList.add('is-loaded');
        }
      }, 300);

      // Setup navigation
      this.setupNavigation();

      // Setup dots
      this.setupDots();

      // Setup scroll observer
      this.setupScrollObserver();

      // Setup keyboard navigation
      this.setupKeyboard();

      // Setup autoplay
      if (this.isAutoplay) {
        this.startAutoplay();
        this.setupAutoplayPause();
      }
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
      this.dots.forEach((dot, index) => {
        dot.addEventListener('click', () => this.goToSlide(index));
      });
    }

    /**
     * Setup IntersectionObserver to update active states
     */
    setupScrollObserver() {
      const options = {
        root: this.slidesWrapper,
        threshold: 0.5,
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const index = this.slides.indexOf(entry.target);
            if (index !== -1 && index !== this.currentIndex) {
              this.currentIndex = index;
              this.updateActiveStates();
            }
          }
        });
      }, options);

      this.slides.forEach((slide) => observer.observe(slide));
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
      const targetIndex = this.currentIndex > 0 ? this.currentIndex - 1 : this.slides.length - 1;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to next slide
     */
    next() {
      const targetIndex = this.currentIndex < this.slides.length - 1 ? this.currentIndex + 1 : 0;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to specific slide
     */
    goToSlide(index) {
      if (index < 0 || index >= this.slides.length || index === this.currentIndex) return;

      const slide = this.slides[index];
      if (!slide) return;

      // Smooth scroll to slide
      slide.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
        inline: 'center',
      });

      this.currentIndex = index;
      this.updateActiveStates();
    }

    /**
     * Update active states (dots and slides)
     */
    updateActiveStates() {
      // Update slides
      this.slides.forEach((slide, index) => {
        slide.classList.toggle('is-active', index === this.currentIndex);
      });

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
        this.nextBtn.disabled = this.currentIndex === this.slides.length - 1;
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

/**
 * Team Carousel - Native JavaScript Implementation
 * CSS scroll-snap + Vanilla JS navigation
 * No dependencies
 */

(function () {
  'use strict';

  /**
   * Initialize all carousels on page
   */
  function initCarousels() {
    const carousels = document.querySelectorAll('.tc-carousel');
    if (!carousels.length) return;

    carousels.forEach((carousel) => {
      new TeamCarousel(carousel);
    });
  }

  /**
   * TeamCarousel Class
   */
  class TeamCarousel {
    constructor(element) {
      this.carousel = element;
      this.slidesWrapper = this.carousel.querySelector('.tc-slides');
      this.slides = Array.from(this.carousel.querySelectorAll('.tc-slide'));
      this.prevBtn = this.carousel.querySelector('.tc-nav--prev');
      this.nextBtn = this.carousel.querySelector('.tc-nav--next');
      this.dotsContainer = this.carousel.querySelector('.tc-dots');
      this.skeleton = this.carousel.querySelector('.tc-skeleton');

      this.currentIndex = 0;
      this.isAutoplay = this.carousel.dataset.autoplay === 'true';
      this.autoplayDelay = parseInt(this.carousel.dataset.delay) || 5000;
      this.autoplayTimer = null;
      this.isMobile = window.innerWidth < 1024;

      if (!this.slidesWrapper || !this.slides.length) return;

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

      // Setup navigation
      this.setupNavigation();

      // Setup dots (only on mobile)
      if (this.isMobile) {
        this.createDots();
        this.setupDots();
      }

      // Setup scroll observer (mobile only)
      if (this.isMobile) {
        this.setupScrollObserver();
      }

      // Setup keyboard navigation
      this.setupKeyboard();

      // Setup autoplay (mobile only)
      if (this.isAutoplay && this.isMobile) {
        this.startAutoplay();
        this.setupAutoplayPause();
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
      this.slides.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.className = 'tc-dot';
        dot.setAttribute('type', 'button');
        dot.setAttribute('aria-label', `Go to team member ${index + 1}`);
        dot.setAttribute('aria-current', index === 0 ? 'true' : 'false');
        if (index === 0) dot.classList.add('is-active');
        this.dotsContainer.appendChild(dot);
      });

      this.dots = Array.from(this.dotsContainer.querySelectorAll('.tc-dot'));
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
      if (!this.isMobile) return; // Solo funciona en mobile

      const targetIndex = this.currentIndex > 0 ? this.currentIndex - 1 : this.slides.length - 1;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to next slide
     */
    next() {
      if (!this.isMobile) return; // Solo funciona en mobile

      const targetIndex = this.currentIndex < this.slides.length - 1 ? this.currentIndex + 1 : 0;
      this.goToSlide(targetIndex);
      this.resetAutoplay();
    }

    /**
     * Navigate to specific slide
     */
    goToSlide(index) {
      if (index < 0 || index >= this.slides.length || index === this.currentIndex) return;
      if (!this.isMobile) return; // Solo funciona en mobile

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
        // Ocultar navegación en desktop
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

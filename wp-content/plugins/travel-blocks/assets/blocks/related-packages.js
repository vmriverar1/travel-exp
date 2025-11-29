/**
 * Related Packages - Enhanced Mobile Slider (Fase 3)
 * Desktop: Grid (solo CSS)
 * Mobile: Slider con JavaScript + Autoplay + Arrows + Dots
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
   * Initialize all sliders on page
   */
  function initSliders() {
    const sliders = document.querySelectorAll('.related-packages');
    if (!sliders.length) return;

    sliders.forEach((slider) => {
      if (!slider._sliderInstance) {
        slider._sliderInstance = new RelatedPackagesSlider(slider);
      } else {
        slider._sliderInstance.update();
      }
    });
  }

  /**
   * RelatedPackagesSlider Class - Enhanced with Phase 3 features
   */
  class RelatedPackagesSlider {
    constructor(element) {
      this.element = element;
      this.container = element.querySelector('.related-packages__grid');
      this.cards = Array.from(element.querySelectorAll('.rp-card'));
      this.currentIndex = 0;
      this.isAnimating = false;
      this.touchStartX = 0;
      this.touchEndX = 0;
      this.autoplayTimer = null;

      // Get slider options from data attributes
      this.options = {
        autoplay: element.dataset.sliderAutoplay === 'true',
        autoplayDelay: parseInt(element.dataset.sliderAutoplayDelay) || 5000,
        speed: parseInt(element.dataset.sliderSpeed) || 300,
        showArrows: element.dataset.sliderShowArrows === 'true',
        showDots: element.dataset.sliderShowDots === 'true',
      };

      // Get slider controls
      this.arrowPrev = element.querySelector('.rp-slider__arrow--prev');
      this.arrowNext = element.querySelector('.rp-slider__arrow--next');
      this.dotsContainer = element.querySelector('.rp-slider__dots');
      this.dots = Array.from(element.querySelectorAll('.rp-slider__dot'));

      if (!this.container || this.cards.length === 0) return;

      this.init();
    }

    init() {
      this.update();
      this.bindEvents();
    }

    update() {
      if (isMobile()) {
        this.enableSlider();
      } else {
        this.disableSlider();
      }
    }

    enableSlider() {
      if (this.element.classList.contains('slider-enabled')) return;

      this.element.classList.add('slider-enabled');
      this.container.style.transform = `translateX(0)`;
      this.currentIndex = 0;
      this.updateDots();

      // Start autoplay if enabled
      if (this.options.autoplay) {
        this.startAutoplay();
      }

      // Show/hide controls based on options
      this.updateControlsVisibility();
    }

    disableSlider() {
      if (!this.element.classList.contains('slider-enabled')) return;

      this.element.classList.remove('slider-enabled');
      this.container.style.transform = '';
      this.stopAutoplay();
    }

    bindEvents() {
      // Touch events
      this.container.addEventListener('touchstart', this.handleTouchStart.bind(this), { passive: true });
      this.container.addEventListener('touchmove', this.handleTouchMove.bind(this), { passive: true });
      this.container.addEventListener('touchend', this.handleTouchEnd.bind(this));

      // Arrow buttons
      if (this.arrowPrev) {
        this.arrowPrev.addEventListener('click', () => this.prev());
      }
      if (this.arrowNext) {
        this.arrowNext.addEventListener('click', () => this.next());
      }

      // Dot navigation
      this.dots.forEach((dot) => {
        dot.addEventListener('click', (e) => {
          const index = parseInt(e.target.dataset.slideIndex);
          if (!isNaN(index)) {
            this.goToSlide(index);
          }
        });
      });

      // Resize event
      window.addEventListener('resize', this.debounce(this.update.bind(this), 250));

      // Pause autoplay on hover/focus
      this.element.addEventListener('mouseenter', () => this.pauseAutoplay());
      this.element.addEventListener('mouseleave', () => this.resumeAutoplay());
      this.element.addEventListener('focusin', () => this.pauseAutoplay());
      this.element.addEventListener('focusout', () => this.resumeAutoplay());
    }

    handleTouchStart(e) {
      if (!isMobile()) return;
      this.touchStartX = e.touches[0].clientX;
      this.pauseAutoplay();
    }

    handleTouchMove(e) {
      if (!isMobile()) return;
      this.touchEndX = e.touches[0].clientX;
    }

    handleTouchEnd(e) {
      if (!isMobile() || this.isAnimating) return;

      const swipeThreshold = 50; // px
      const diff = this.touchStartX - this.touchEndX;

      if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
          // Swipe left - next
          this.next();
        } else {
          // Swipe right - prev
          this.prev();
        }
      }

      this.touchStartX = 0;
      this.touchEndX = 0;
      this.resumeAutoplay();
    }

    next() {
      if (this.currentIndex >= this.cards.length - 1) {
        // Loop back to first slide
        this.goToSlide(0);
      } else {
        this.goToSlide(this.currentIndex + 1);
      }
    }

    prev() {
      if (this.currentIndex <= 0) {
        // Loop to last slide
        this.goToSlide(this.cards.length - 1);
      } else {
        this.goToSlide(this.currentIndex - 1);
      }
    }

    goToSlide(index) {
      if (this.isAnimating || index < 0 || index >= this.cards.length) return;

      this.isAnimating = true;
      this.currentIndex = index;

      const cardWidth = this.cards[0].offsetWidth;
      const gap = 24; // Same as CSS --card-gap
      const offset = -(index * (cardWidth + gap));

      this.container.style.transition = `transform ${this.options.speed}ms ease-out`;
      this.container.style.transform = `translateX(${offset}px)`;

      this.updateDots();

      setTimeout(() => {
        this.isAnimating = false;
      }, this.options.speed);
    }

    updateDots() {
      if (!this.dotsContainer) return;

      this.dots.forEach((dot, index) => {
        if (index === this.currentIndex) {
          dot.classList.add('rp-slider__dot--active');
          dot.setAttribute('aria-selected', 'true');
        } else {
          dot.classList.remove('rp-slider__dot--active');
          dot.setAttribute('aria-selected', 'false');
        }
      });
    }

    updateControlsVisibility() {
      if (!isMobile()) return;

      // Arrows visibility
      if (this.arrowPrev && this.arrowNext) {
        if (this.options.showArrows) {
          this.arrowPrev.style.display = 'flex';
          this.arrowNext.style.display = 'flex';
        } else {
          this.arrowPrev.style.display = 'none';
          this.arrowNext.style.display = 'none';
        }
      }

      // Dots visibility
      if (this.dotsContainer) {
        if (this.options.showDots) {
          this.dotsContainer.style.display = 'flex';
        } else {
          this.dotsContainer.style.display = 'none';
        }
      }
    }

    startAutoplay() {
      if (!this.options.autoplay || !isMobile()) return;

      this.stopAutoplay();
      this.autoplayTimer = setInterval(() => {
        this.next();
      }, this.options.autoplayDelay);
    }

    stopAutoplay() {
      if (this.autoplayTimer) {
        clearInterval(this.autoplayTimer);
        this.autoplayTimer = null;
      }
    }

    pauseAutoplay() {
      this.stopAutoplay();
    }

    resumeAutoplay() {
      if (this.options.autoplay && isMobile()) {
        this.startAutoplay();
      }
    }

    debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }
  }

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSliders);
  } else {
    initSliders();
  }

  // Re-initialize on window load (for editor)
  window.addEventListener('load', initSliders);

  // For Gutenberg editor
  if (window.wp && window.wp.data) {
    window.wp.data.subscribe(() => {
      setTimeout(initSliders, 100);
    });
  }
})();

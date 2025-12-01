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
   * Truncate excerpt text to make space for inline button on last line
   * Counts words in last line, removes half to make room for button
   */
  function truncateOverlayExcerpts() {
    const overlayCards = document.querySelectorAll('.posts-carousel--overlay .pc-card');

    overlayCards.forEach((card) => {
      const excerpt = card.querySelector('.pc-card__excerpt');
      const inlineCta = card.querySelector('.pc-card__inline-cta');

      if (!excerpt) return;

      // Store original text on first run
      if (!excerpt.dataset.originalText) {
        const fullText = excerpt.textContent.trim();
        const buttonText = inlineCta ? inlineCta.textContent.trim() : '';
        const cleanText = fullText.replace(buttonText, '').trim();
        excerpt.dataset.originalText = cleanText;
      }

      const originalText = excerpt.dataset.originalText;
      if (!originalText) return;

      // Get container width
      const containerWidth = excerpt.offsetWidth;
      if (containerWidth <= 0) return;

      // Get CSS variable for lines
      const carousel = card.closest('.posts-carousel');
      const computedStyle = getComputedStyle(carousel || card);
      const descriptionLines = parseInt(computedStyle.getPropertyValue('--description-lines')) || 3;
      const lineHeight = 23;

      // Check if hovered (show more lines)
      const isHovered = card.matches(':hover');
      const linesToShow = isHovered ? descriptionLines + 2 : descriptionLines;
      const maxHeight = linesToShow * lineHeight;

      // Create temporary element to measure text
      const tempEl = document.createElement('p');
      tempEl.style.cssText = `
        position: absolute;
        visibility: hidden;
        width: ${containerWidth}px;
        font: inherit;
        line-height: ${lineHeight}px;
        margin: 0;
        padding: 0;
      `;
      excerpt.parentNode.appendChild(tempEl);

      // Start with original text
      const words = originalText.split(' ');
      tempEl.textContent = originalText;

      // Find how many words fit in the allowed lines
      let currentWords = words.length;
      while (tempEl.offsetHeight > maxHeight && currentWords > 0) {
        currentWords--;
        tempEl.textContent = words.slice(0, currentWords).join(' ');
      }

      // Now we have text that fits. Count words in the last line.
      // To do this: remove one word at a time until height decreases
      let wordsInLastLine = 0;
      const fittingHeight = tempEl.offsetHeight;
      let testWords = currentWords;

      while (testWords > 0) {
        testWords--;
        tempEl.textContent = words.slice(0, testWords).join(' ');
        if (tempEl.offsetHeight < fittingHeight) {
          // Height decreased, so we found the start of last line
          wordsInLastLine = currentWords - testWords;
          break;
        }
      }

      // If couldn't detect, assume ~5 words per line
      if (wordsInLastLine === 0) {
        wordsInLastLine = 5;
      }

      // Remove half the words from last line to make space for button
      const wordsToRemove = Math.ceil(wordsInLastLine / 2);
      const finalWordCount = Math.max(1, currentWords - wordsToRemove);

      // Clean up temp element
      tempEl.remove();

      // Build final text
      let finalText = words.slice(0, finalWordCount).join(' ');
      finalText = finalText.replace(/[.,;:!?\s]+$/, '') + '...';

      // Update excerpt content
      if (inlineCta) {
        const buttonClone = inlineCta.cloneNode(true);
        excerpt.textContent = finalText;
        excerpt.insertBefore(buttonClone, excerpt.firstChild);
      } else {
        excerpt.textContent = finalText;
      }
    });
  }

  /**
   * Setup hover listeners for dynamic text expansion
   */
  function setupHoverTruncation() {
    const overlayCards = document.querySelectorAll('.posts-carousel--overlay .pc-card');

    overlayCards.forEach((card) => {
      if (card._truncationListeners) return;
      card._truncationListeners = true;

      card.addEventListener('mouseenter', () => {
        // Small delay to let CSS transition start
        setTimeout(() => {
          truncateOverlayExcerpts();
        }, 50);
      });

      card.addEventListener('mouseleave', () => {
        // Delay to let CSS transition complete before recalculating
        setTimeout(() => {
          truncateOverlayExcerpts();
        }, 350); // Match CSS transition duration (0.3s = 300ms) + buffer
      });
    });
  }

  /**
   * Initialize text truncation
   */
  function initTruncation() {
    truncateOverlayExcerpts();
    setupHoverTruncation();
  }

  /**
   * Initialize on DOM ready
   */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initCarousels();
      initTruncation();
    });
  } else {
    initCarousels();
    initTruncation();
  }

  // Re-run truncation on resize
  let resizeTruncationTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTruncationTimer);
    resizeTruncationTimer = setTimeout(truncateOverlayExcerpts, 200);
  });

  /**
   * Re-initialize on Gutenberg block update
   */
  if (window.acf) {
    window.acf.addAction('render_block_preview', () => {
      initCarousels();
      setTimeout(initTruncation, 100);
    });
  }
})();

/**
 * Taxonomy Tabs Block JavaScript
 * Maneja la navegación entre tabs y slider mobile
 */

(function() {
    'use strict';

    const MOBILE_BREAKPOINT = 768; // px

    /**
     * Check if viewport is mobile
     */
    function isMobile() {
        return window.innerWidth <= MOBILE_BREAKPOINT;
    }

    /**
     * Initialize all taxonomy tabs blocks on the page
     */
    function initTaxonomyTabs() {
        const tabsContainers = document.querySelectorAll('.taxonomy-tabs');

        tabsContainers.forEach(container => {
            initSingleTabsBlock(container);
            // Initialize sliders for each panel
            initSliders(container);
        });
    }

    /**
     * Initialize a single tabs block
     */
    function initSingleTabsBlock(container) {
        const tabButtons = container.querySelectorAll('.tt-nav__item');
        const tabPanels = container.querySelectorAll('.tt-panel');

        if (!tabButtons.length || !tabPanels.length) {
            return;
        }

        // Add click handlers to tab buttons
        tabButtons.forEach((button, index) => {
            button.addEventListener('click', () => {
                switchTab(index, tabButtons, tabPanels);
            });
        });

        // Add keyboard navigation
        container.querySelector('.tt-nav').addEventListener('keydown', (e) => {
            handleKeyboardNav(e, tabButtons, tabPanels);
        });

        // Initialize with first tab active
        if (!container.querySelector('.tt-nav__item.is-active')) {
            switchTab(0, tabButtons, tabPanels);
        }
    }

    /**
     * Switch to a specific tab
     */
    function switchTab(targetIndex, tabButtons, tabPanels) {
        // Deactivate all tabs and panels
        tabButtons.forEach((btn, idx) => {
            btn.classList.remove('is-active');
            btn.setAttribute('aria-selected', 'false');
            btn.setAttribute('tabindex', '-1');
        });

        tabPanels.forEach(panel => {
            panel.classList.remove('is-active');
        });

        // Activate target tab and panel
        const targetButton = tabButtons[targetIndex];
        const targetPanel = tabPanels[targetIndex];

        if (targetButton && targetPanel) {
            targetButton.classList.add('is-active');
            targetButton.setAttribute('aria-selected', 'true');
            targetButton.setAttribute('tabindex', '0');
            targetButton.focus();

            targetPanel.classList.add('is-active');

            // Trigger event for analytics/tracking
            const event = new CustomEvent('taxonomyTabChange', {
                detail: {
                    tabIndex: targetIndex,
                    tabButton: targetButton,
                    tabPanel: targetPanel
                }
            });
            document.dispatchEvent(event);
        }
    }

    /**
     * Handle keyboard navigation (Arrow keys)
     */
    function handleKeyboardNav(e, tabButtons, tabPanels) {
        const currentIndex = Array.from(tabButtons).findIndex(btn =>
            btn.classList.contains('is-active')
        );

        let newIndex = currentIndex;

        switch(e.key) {
            case 'ArrowLeft':
            case 'ArrowUp':
                e.preventDefault();
                newIndex = currentIndex > 0 ? currentIndex - 1 : tabButtons.length - 1;
                break;

            case 'ArrowRight':
            case 'ArrowDown':
                e.preventDefault();
                newIndex = currentIndex < tabButtons.length - 1 ? currentIndex + 1 : 0;
                break;

            case 'Home':
                e.preventDefault();
                newIndex = 0;
                break;

            case 'End':
                e.preventDefault();
                newIndex = tabButtons.length - 1;
                break;

            default:
                return;
        }

        switchTab(newIndex, tabButtons, tabPanels);
    }

    /**
     * Initialize sliders for all panels in a container
     */
    function initSliders(container) {
        const panels = container.querySelectorAll('.tt-panel');

        panels.forEach(panel => {
            if (!panel._sliderInstance) {
                panel._sliderInstance = new TaxonomyTabsSlider(panel, container);
            } else {
                panel._sliderInstance.update();
            }
        });
    }

    /**
     * TaxonomyTabsSlider Class
     * Solo activo en mobile
     */
    class TaxonomyTabsSlider {
        constructor(panel, container) {
            this.panel = panel;
            this.container = container;
            this.grid = this.panel.querySelector('.tt-cards-grid');
            this.cards = Array.from(this.panel.querySelectorAll('.tt-card'));
            this.dots = Array.from(this.panel.querySelectorAll('.tt-dot'));
            this.prevBtn = this.panel.querySelector('.tt-arrow--prev');
            this.nextBtn = this.panel.querySelector('.tt-arrow--next');

            this.currentIndex = 0;
            this.isActive = false;
            this.autoplayTimer = null;
            this.resizeTimer = null;

            // Settings from data attributes
            this.settings = {
                autoplay: this.container.dataset.sliderAutoplay === '1',
                delay: parseInt(this.container.dataset.sliderDelay) || 5000,
                speed: parseFloat(this.container.dataset.sliderSpeed) || 0.4,
            };

            this.init();
        }

        init() {
            if (!this.grid || !this.cards.length) return;

            // Ocultar controles si solo hay 1 card
            if (this.cards.length === 1) {
                this.hideControls();
                return; // No inicializar slider si solo hay 1 card
            }

            // Set initial transform
            this.updateSlider();

            // Event listeners
            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', () => this.prev());
            }
            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', () => this.next());
            }

            // Dot navigation
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goToSlide(index));
            });

            // Resize handler
            window.addEventListener('resize', () => {
                clearTimeout(this.resizeTimer);
                this.resizeTimer = setTimeout(() => this.handleResize(), 150);
            });

            // Touch events for swipe
            this.initTouchEvents();

            // Initial activation check
            this.handleResize();
        }

        hideControls() {
            // Ocultar flechas y dots cuando solo hay 1 card
            if (this.prevBtn) this.prevBtn.style.display = 'none';
            if (this.nextBtn) this.nextBtn.style.display = 'none';

            const dotsContainer = this.panel.querySelector('.tt-dots');
            if (dotsContainer) dotsContainer.style.display = 'none';
        }

        handleResize() {
            const shouldBeActive = isMobile();

            if (shouldBeActive && !this.isActive) {
                this.activate();
            } else if (!shouldBeActive && this.isActive) {
                this.deactivate();
            }
        }

        activate() {
            this.isActive = true;
            this.currentIndex = 0;

            // Reset scroll position to start (como PostsCarousel)
            requestAnimationFrame(() => {
                if (this.grid) {
                    this.grid.scrollLeft = 0;
                    // Force scroll to first card
                    if (this.cards[0]) {
                        this.cards[0].scrollIntoView({ block: 'nearest', inline: 'start', behavior: 'auto' });
                    }
                }
            });

            this.updateSlider();

            if (this.settings.autoplay) {
                this.startAutoplay();
            }
        }

        deactivate() {
            this.isActive = false;
            this.stopAutoplay();

            // Reset scroll position
            if (this.grid) {
                this.grid.scrollLeft = 0;
            }
        }

        updateSlider() {
            if (!this.isActive) return;

            // Update dots
            this.dots.forEach((dot, index) => {
                dot.classList.toggle('is-active', index === this.currentIndex);
            });

            // Update aria-selected for cards
            this.cards.forEach((card, index) => {
                card.setAttribute('aria-hidden', index !== this.currentIndex);
            });
        }

        goToSlide(index, resetAutoplay = true) {
            if (!this.isActive || index < 0 || index >= this.cards.length || index === this.currentIndex) {
                return;
            }

            const card = this.cards[index];
            if (!card) return;

            // Smooth scroll to card (como PostsCarousel)
            card.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center',
            });

            this.currentIndex = index;
            this.updateSlider();

            if (resetAutoplay && this.settings.autoplay) {
                this.restartAutoplay();
            }
        }

        next() {
            const nextIndex = (this.currentIndex + 1) % this.cards.length;
            this.goToSlide(nextIndex);
        }

        prev() {
            const prevIndex = (this.currentIndex - 1 + this.cards.length) % this.cards.length;
            this.goToSlide(prevIndex);
        }

        startAutoplay() {
            this.stopAutoplay();
            this.autoplayTimer = setInterval(() => this.next(), this.settings.delay);
        }

        stopAutoplay() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        }

        restartAutoplay() {
            if (this.settings.autoplay) {
                this.stopAutoplay();
                this.startAutoplay();
            }
        }

        initTouchEvents() {
            let startX = 0;
            let currentX = 0;
            let isDragging = false;

            this.grid.addEventListener('touchstart', (e) => {
                if (!isMobile()) return;
                startX = e.touches[0].clientX;
                isDragging = true;
                this.stopAutoplay();
            }, { passive: true });

            this.grid.addEventListener('touchmove', (e) => {
                if (!isDragging || !isMobile()) return;
                currentX = e.touches[0].clientX;
            }, { passive: true });

            this.grid.addEventListener('touchend', () => {
                if (!isDragging || !isMobile()) return;

                const diff = startX - currentX;
                const threshold = 50;

                if (Math.abs(diff) > threshold) {
                    if (diff > 0) {
                        this.next();
                    } else {
                        this.prev();
                    }
                }

                isDragging = false;
                this.restartAutoplay();
            }, { passive: true });
        }

        update() {
            this.handleResize();
        }
    }

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTaxonomyTabs);
    } else {
        initTaxonomyTabs();
    }

    /**
     * Re-initialize when new blocks are added (Gutenberg editor)
     */
    if (window.wp && window.wp.domReady) {
        window.wp.domReady(initTaxonomyTabs);
    }

    // Expose to global scope for manual re-initialization if needed
    window.initTaxonomyTabs = initTaxonomyTabs;

})();

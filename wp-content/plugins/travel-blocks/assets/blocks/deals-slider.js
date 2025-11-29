/**
 * Deals Slider Block JavaScript
 *
 * Handles Swiper slider initialization and countdown timer
 *
 * @package Travel\Blocks
 * @since 1.4.0
 */

(function() {
    'use strict';

    /**
     * Initialize Deals Slider
     */
    function initDealsSlider() {
        const sliders = document.querySelectorAll('.deals-slider');

        sliders.forEach(slider => {
            // Initialize countdown if present
            if (slider.hasAttribute('data-end-date')) {
                initCountdown(slider);
            }

            // Initialize Swiper
            initSwiper(slider);
        });
    }

    /**
     * Initialize Swiper Slider
     */
    function initSwiper(sliderEl) {
        const swiperEl = sliderEl.querySelector('.deals-slider__swiper');

        if (!swiperEl) return;

        // Get configuration from data attribute
        const configAttr = sliderEl.getAttribute('data-slider-config');
        let config = {
            autoplay: true,
            delay: 6000,
            loop: true,
            showArrows: true,
            showDots: true
        };

        if (configAttr) {
            try {
                const parsedConfig = JSON.parse(configAttr);
                config = { ...config, ...parsedConfig };
            } catch (e) {
                console.error('Error parsing slider config:', e);
            }
        }

        // Build Swiper options
        const swiperOptions = {
            slidesPerView: 1,
            spaceBetween: 0,
            speed: 600,
            loop: config.loop,
            grabCursor: true,
            keyboard: {
                enabled: true,
                onlyInViewport: true
            },
            a11y: {
                enabled: true,
                prevSlideMessage: 'Previous deal',
                nextSlideMessage: 'Next deal',
                firstSlideMessage: 'This is the first deal',
                lastSlideMessage: 'This is the last deal',
                paginationBulletMessage: 'Go to deal {{index}}'
            }
        };

        // Autoplay
        if (config.autoplay) {
            swiperOptions.autoplay = {
                delay: config.delay || 6000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            };
        }

        // Navigation arrows
        if (config.showArrows) {
            const prevArrow = sliderEl.querySelector('.deals-slider__arrow--prev');
            const nextArrow = sliderEl.querySelector('.deals-slider__arrow--next');

            if (prevArrow && nextArrow) {
                swiperOptions.navigation = {
                    prevEl: prevArrow,
                    nextEl: nextArrow,
                    disabledClass: 'swiper-button-disabled'
                };
            }
        }

        // Pagination dots
        if (config.showDots) {
            const pagination = sliderEl.querySelector('.deals-slider__pagination');

            if (pagination) {
                swiperOptions.pagination = {
                    el: pagination,
                    clickable: true,
                    bulletClass: 'swiper-pagination-bullet',
                    bulletActiveClass: 'swiper-pagination-bullet-active'
                };
            }
        }

        // Initialize Swiper
        // Check if Swiper is available
        if (typeof Swiper === 'undefined') {
            console.error('Swiper library not loaded');
            return;
        }

        try {
            new Swiper(swiperEl, swiperOptions);
        } catch (e) {
            console.error('Error initializing Swiper:', e);
        }
    }

    /**
     * Initialize Countdown Timer
     */
    function initCountdown(sliderEl) {
        const endDateStr = sliderEl.getAttribute('data-end-date');

        if (!endDateStr) return;

        // Parse end date (expecting format: Y-m-d H:i:s or ISO format)
        const endDate = new Date(endDateStr.replace(' ', 'T'));

        // Validate date
        if (isNaN(endDate.getTime())) {
            console.error('Invalid end date format:', endDateStr);
            return;
        }

        // Get countdown elements
        const daysEl = sliderEl.querySelector('[data-unit="days"]');
        const hoursEl = sliderEl.querySelector('[data-unit="hours"]');
        const minutesEl = sliderEl.querySelector('[data-unit="minutes"]');
        const secondsEl = sliderEl.querySelector('[data-unit="seconds"]');

        if (!daysEl || !hoursEl || !minutesEl || !secondsEl) {
            console.error('Countdown elements not found');
            return;
        }

        // Declare interval variable before the function
        let countdownInterval;

        /**
         * Update countdown display
         */
        function updateCountdown() {
            const now = new Date();
            const diff = endDate - now;

            // If expired
            if (diff <= 0) {
                daysEl.textContent = '00';
                hoursEl.textContent = '00';
                minutesEl.textContent = '00';
                secondsEl.textContent = '00';

                // Hide slider or show expired message
                const countdownBar = sliderEl.querySelector('.deals-slider__countdown-bar');
                if (countdownBar) {
                    // You can customize what happens when expired
                    // Option 1: Hide the entire block
                    // sliderEl.style.display = 'none';

                    // Option 2: Show "Deal Ended" message
                    const label2 = countdownBar.querySelector('.deals-slider__countdown-label-2');
                    if (label2) {
                        label2.textContent = 'Deal Ended';
                        label2.style.color = '#ff6b6b';
                    }
                }

                // Stop the interval
                clearInterval(countdownInterval);
                return;
            }

            // Calculate time units
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            // Update display with leading zeros
            daysEl.textContent = String(days).padStart(2, '0');
            hoursEl.textContent = String(hours).padStart(2, '0');
            minutesEl.textContent = String(minutes).padStart(2, '0');
            secondsEl.textContent = String(seconds).padStart(2, '0');
        }

        // Initial update
        updateCountdown();

        // Update every second
        countdownInterval = setInterval(updateCountdown, 1000);

        // Store interval ID for cleanup if needed
        sliderEl.dataset.countdownInterval = countdownInterval;
    }

    /**
     * Cleanup function for when block is removed (editor)
     */
    function cleanupSlider(sliderEl) {
        // Clear countdown interval
        if (sliderEl.dataset.countdownInterval) {
            clearInterval(parseInt(sliderEl.dataset.countdownInterval));
        }

        // Destroy Swiper instance if it exists
        const swiperEl = sliderEl.querySelector('.deals-slider__swiper');
        if (swiperEl && swiperEl.swiper) {
            swiperEl.swiper.destroy(true, true);
        }
    }

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDealsSlider);
    } else {
        initDealsSlider();
    }

    /**
     * Re-initialize for Gutenberg editor
     * When blocks are added/updated in the editor
     */
    if (window.acf) {
        window.acf.addAction('render_block_preview/type=deals-slider', function($el) {
            // Small delay to ensure DOM is ready
            setTimeout(function() {
                const slider = $el[0].querySelector('.deals-slider');
                if (slider) {
                    // Cleanup old instance
                    cleanupSlider(slider);

                    // Reinitialize
                    if (slider.hasAttribute('data-end-date')) {
                        initCountdown(slider);
                    }
                    initSwiper(slider);
                }
            }, 100);
        });
    }

    /**
     * Cleanup on page unload (optional, for SPA-like behavior)
     */
    window.addEventListener('beforeunload', function() {
        const sliders = document.querySelectorAll('.deals-slider');
        sliders.forEach(cleanupSlider);
    });

    /**
     * Expose init function globally for manual initialization
     */
    window.initDealsSlider = initDealsSlider;

})();
